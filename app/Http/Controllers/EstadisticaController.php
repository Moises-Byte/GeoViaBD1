<?php

namespace App\Http\Controllers;

use App\Services\EstadisticaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EstadisticaController extends Controller
{
    public function index(Request $request, EstadisticaService $estadisticas): View
    {
        abort_unless(
            in_array($request->user()?->rol, ['AUTORIDAD', 'SUPERVISOR'], true),
            403,
            'No tienes permiso para consultar estadísticas.'
        );

        return view('estadisticas.index', $estadisticas->resumen());
    }
}
