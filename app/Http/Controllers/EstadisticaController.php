<?php

namespace App\Http\Controllers;

use App\Models\Cuadrilla;
use App\Models\Reporte;
use App\Models\Via;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EstadisticaController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(
            in_array($request->user()?->rol, ['AUTORIDAD', 'SUPERVISOR'], true),
            403,
            'No tienes permiso para consultar estadísticas.'
        );

        $totalReportes = Reporte::count();

        $conteos = Reporte::query()
            ->select('estado')
            ->selectRaw('COUNT(*) AS cantidad')
            ->groupBy('estado')
            ->pluck('cantidad', 'estado');

        $porEstado = collect([
            'PENDIENTE', 'VALIDADO', 'RECHAZADO', 'EN REPARACION', 'FINALIZADO',
        ])->mapWithKeys(fn ($estado) => [$estado => (int) ($conteos[$estado] ?? 0)]);

        $porVia = Via::withCount('reportes')
            ->orderByDesc('reportes_count')
            ->orderBy('nombre_via')
            ->get();

        // En Oracle, la diferencia entre fechas DATE se expresa en días.
        $resultado = DB::table('orden_trabajo')
            ->join('reporte', 'orden_trabajo.reporte_id_reporte', '=', 'reporte.id_reporte')
            ->where('orden_trabajo.estado', 'FINALIZADA')
            ->where('reporte.estado', 'FINALIZADO')
            ->whereNotNull('orden_trabajo.fecha_finalizacion')
            ->whereColumn('orden_trabajo.fecha_finalizacion', '>=', 'reporte.fecha_reporte')
            ->selectRaw('AVG(orden_trabajo.fecha_finalizacion - reporte.fecha_reporte) AS promedio_dias')
            ->first();

        $tiempoPromedio = $resultado?->promedio_dias !== null
            ? round((float) $resultado->promedio_dias, 2)
            : null;

        // Más activa: mayor cantidad total de órdenes asignadas.
        $cuadrillaMasActiva = Cuadrilla::withCount('ordenesTrabajo')
            ->whereHas('ordenesTrabajo')
            ->orderByDesc('ordenes_trabajo_count')
            ->orderBy('id_cuadrilla')
            ->first();

        return view('estadisticas.index', compact(
            'totalReportes', 'porEstado', 'porVia', 'tiempoPromedio', 'cuadrillaMasActiva'
        ));
    }
}
