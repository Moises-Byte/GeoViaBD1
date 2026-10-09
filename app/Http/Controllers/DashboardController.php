<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use App\Models\Reporte;
use App\Services\EstadisticaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, EstadisticaService $estadisticas): View
    {
        if (! in_array($request->user()?->rol, ['AUTORIDAD', 'SUPERVISOR'], true)) {
            return view('dashboard');
        }

        $datos = $estadisticas->resumen();
        $datos['ordenesActivas'] = OrdenTrabajo::whereIn('estado', ['PENDIENTE', 'EN PROCESO'])->count();
        $datos['reportesPrioritarios'] = Reporte::with(['via', 'tipoDano'])
            ->where('estado', 'VALIDADO')->whereDoesntHave('ordenesTrabajo')
            ->orderByDesc('prioridad')->orderBy('fecha_reporte')->orderBy('id_reporte')->limit(5)->get();
        $datos['ordenesRecientes'] = OrdenTrabajo::with(['reporte.via', 'cuadrilla', 'supervisor'])
            ->orderByDesc('fecha_asignacion')->orderByDesc('id_orden')->limit(5)->get();

        return view('dashboard.municipal', $datos);
    }
}
