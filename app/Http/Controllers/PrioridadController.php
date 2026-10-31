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

        $datos = $request->validate(['buscar' => ['nullable', 'string', 'max:150']]);
        $buscar = trim($datos['buscar'] ?? '');
        $reportes = Reporte::with(['via', 'tipoDano'])
            ->where('estado', 'VALIDADO')
            ->when($buscar !== '', function ($consulta) use ($buscar) {
                $patron = '%'.mb_strtoupper($buscar).'%';
                $consulta->where(fn ($grupo) => $grupo
                    ->whereRaw('UPPER(descripcion) LIKE ?', [$patron])
                    ->orWhereHas('via', fn ($via) => $via->whereRaw('UPPER(nombre_via) LIKE ?', [$patron])));
            })
            ->orderByDesc('prioridad')
            ->orderBy('fecha_reporte')
            ->orderBy('id_reporte')
            ->paginate(10)
            ->withQueryString();

        // Se usa el mismo criterio que el cálculo: reportes de la vía no rechazados.
        $cantidadesPorVia = Reporte::where('estado', '<>', 'RECHAZADO')
            ->whereIn('via_id_via', $reportes->pluck('via_id_via'))
            ->select('via_id_via')->selectRaw('COUNT(*) AS cantidad')
            ->groupBy('via_id_via')->pluck('cantidad', 'via_id_via');

        return view('prioridades.index', [
            'reportes' => $reportes,
            'buscar' => $buscar,
            'cantidadesPorVia' => $cantidadesPorVia,
        ]);
    }

    public function recalcular(
        Request $request,
        PrioridadService $servicio
    ): RedirectResponse {
        abort_unless(
            $request->user()?->rol === 'AUTORIDAD',
            403,
            'Solo la autoridad municipal puede recalcular prioridades.'
        );

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
