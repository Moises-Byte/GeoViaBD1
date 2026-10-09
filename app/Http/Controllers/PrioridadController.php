<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Services\PrioridadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrioridadController extends Controller
{
    public function index(Request $request): View
    {
        $this->verificarAcceso($request);

        $reportes = Reporte::with(['via', 'tipoDano'])
            ->where('estado', 'VALIDADO')
            ->orderByDesc('prioridad')
            ->orderBy('fecha_reporte')
            ->orderBy('id_reporte')
            ->paginate(10);

        return view('prioridades.index', [
            'reportes' => $reportes,
        ]);
    }

    public function recalcular(
        Request $request,
        PrioridadService $servicio
    ): RedirectResponse {
        $this->verificarAcceso($request);

        $cantidad = $servicio->recalcular();

        return redirect()
            ->route('prioridades.index')
            ->with(
                'success',
                "Se actualizaron las prioridades de {$cantidad} reportes."
            );
    }

    private function verificarAcceso(Request $request): void
    {
        abort_unless(
            in_array(
                $request->user()?->rol,
                ['AUTORIDAD', 'SUPERVISOR'],
                true
            ),
            403,
            'No tienes permiso para gestionar prioridades.'
        );
    }
}