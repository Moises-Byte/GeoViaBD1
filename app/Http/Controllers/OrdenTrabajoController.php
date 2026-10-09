<?php

namespace App\Http\Controllers;

use App\Models\Cuadrilla;
use App\Models\OrdenTrabajo;
use App\Models\Reporte;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrdenTrabajoController extends Controller
{
    public function index(Request $request): View
    {
        $this->verificarAcceso($request);

        $ordenes = OrdenTrabajo::with(['reporte.via', 'cuadrilla', 'supervisor'])
            ->orderByDesc('fecha_asignacion')
            ->orderByDesc('id_orden')
            ->paginate(10);

        return view('ordenes.index', compact('ordenes'));
    }

    public function create(Request $request): View
    {
        $this->verificarAcceso($request);

        $reportes = Reporte::with(['via', 'tipoDano'])
            ->where('estado', 'VALIDADO')
            ->whereDoesntHave('ordenesTrabajo')
            ->orderByDesc('prioridad')
            ->orderBy('fecha_reporte')
            ->orderBy('id_reporte')
            ->get();

        $cuadrillas = Cuadrilla::orderBy('nombre')->get();
        $supervisores = User::where('rol', 'SUPERVISOR')->orderBy('nombre')->get();

        return view('ordenes.create', compact('reportes', 'cuadrillas', 'supervisores'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->verificarAcceso($request);

        $datos = $request->validate([
            'reporte_id_reporte' => ['required', 'integer', Rule::exists('reporte', 'id_reporte')],
            'cuadrilla_id_cuadrilla' => ['required', 'integer', Rule::exists('cuadrilla', 'id_cuadrilla')],
            'usuario_id_usuario' => [
                'required',
                'integer',
                Rule::exists('usuario', 'id_usuario')->where('rol', 'SUPERVISOR'),
            ],
            'fecha_asignacion' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'observaciones' => ['nullable', 'string', 'max:500'],
        ], [
            'reporte_id_reporte.required' => 'Selecciona un reporte.',
            'reporte_id_reporte.exists' => 'El reporte seleccionado no existe.',
            'cuadrilla_id_cuadrilla.required' => 'Selecciona una cuadrilla.',
            'cuadrilla_id_cuadrilla.exists' => 'La cuadrilla seleccionada no existe.',
            'usuario_id_usuario.required' => 'Selecciona un supervisor.',
            'usuario_id_usuario.exists' => 'Selecciona un usuario con el rol SUPERVISOR.',
            'fecha_asignacion.required' => 'Indica la fecha de asignación.',
            'fecha_asignacion.date_format' => 'La fecha de asignación no es válida.',
            'fecha_asignacion.before_or_equal' => 'La fecha de asignación no puede ser futura.',
            'observaciones.max' => 'Las observaciones no pueden superar 500 caracteres.',
        ]);

        $orden = DB::transaction(function () use ($datos) {
            // Bloquear el reporte evita que dos solicitudes creen órdenes duplicadas.
            $reporte = Reporte::whereKey($datos['reporte_id_reporte'])
                ->lockForUpdate()
                ->get()
                ->first();

            if (! $reporte || $reporte->estado !== 'VALIDADO') {
                throw ValidationException::withMessages([
                    'reporte_id_reporte' => 'Solo puedes crear órdenes para reportes validados.',
                ]);
            }

            if ($reporte->ordenesTrabajo()->exists()) {
                throw ValidationException::withMessages([
                    'reporte_id_reporte' => 'Este reporte ya tiene una orden de trabajo.',
                ]);
            }

            return $reporte->ordenesTrabajo()->create([
                'cuadrilla_id_cuadrilla' => $datos['cuadrilla_id_cuadrilla'],
                'usuario_id_usuario' => $datos['usuario_id_usuario'],
                'fecha_asignacion' => $datos['fecha_asignacion'],
                'fecha_finalizacion' => null,
                'estado' => 'PENDIENTE',
                'avance' => 0,
                'observaciones' => $datos['observaciones'] ?? null,
            ]);
        });

        return redirect()->route('ordenes.index')->with(
            'success',
            "Se creó la orden #{$orden->id_orden} y se asignaron cuadrilla y supervisor."
        );
    }

    private function verificarAcceso(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->rol, ['AUTORIDAD', 'SUPERVISOR'], true),
            403,
            'No tienes permiso para gestionar órdenes de trabajo.'
        );
    }
}
