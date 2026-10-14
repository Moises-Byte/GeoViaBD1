<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use App\Models\Reporte;
use App\Services\EstadisticaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, EstadisticaService $estadisticas): View|RedirectResponse
    {
        return match ($request->user()?->rol) {
            'AUTORIDAD', 'SUPERVISOR' => $this->vistaMunicipal($estadisticas),
            // El inspector aún no tiene panel propio: va directo a la lista de reportes.
            'INSPECTOR' => redirect()->route('reportes.index'),
            default => $this->vistaVecino($request),
        };
    }

    private function vistaMunicipal(EstadisticaService $estadisticas): View
    {
        $datos = $estadisticas->resumen();
        $datos['ordenesActivas'] = OrdenTrabajo::whereIn('estado', ['PENDIENTE', 'EN PROCESO'])->count();
        $datos['reportesPrioritarios'] = Reporte::with(['via', 'tipoDano'])
            ->where('estado', 'VALIDADO')->whereDoesntHave('ordenesTrabajo')
            ->orderByDesc('prioridad')->orderBy('fecha_reporte')->orderBy('id_reporte')->limit(5)->get();
        $datos['ordenesRecientes'] = OrdenTrabajo::with(['reporte.via', 'cuadrilla', 'supervisor'])
            ->orderByDesc('fecha_asignacion')->orderByDesc('id_orden')->limit(5)->get();

        return view('dashboard.municipal', $datos);
    }

    private function vistaVecino(Request $request): View
    {
        $usuarioId = $request->user()->id_usuario;

        $porEstado = Reporte::query()
            ->where('usuario_id_usuario', $usuarioId)
            ->select('estado')
            ->selectRaw('COUNT(*) AS cantidad')
            ->groupBy('estado')
            ->pluck('cantidad', 'estado');

        $recientes = Reporte::with(['via', 'tipoDano', 'ordenesTrabajo'])
            ->where('usuario_id_usuario', $usuarioId)
            ->orderByDesc('fecha_reporte')
            ->orderByDesc('id_reporte')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'porEstado' => $porEstado,
            'total' => (int) $porEstado->sum(),
            'recientes' => $recientes,
        ]);
    }
}