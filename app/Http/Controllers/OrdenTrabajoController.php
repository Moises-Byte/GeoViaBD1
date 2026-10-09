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

    public function edit(Request $request, OrdenTrabajo $orden): View
    {
        $this->verificarResponsable($request, $orden);
        $orden->load(['reporte.via', 'cuadrilla', 'supervisor']);

        return view('ordenes.edit', compact('orden'));
    }

    public function update(Request $request, OrdenTrabajo $orden): RedirectResponse
    {
        $this->verificarResponsable($request, $orden);
        $datos = $request->validate([
            'avance' => ['required', 'integer', 'between:0,99'],
            'observaciones' => ['nullable', 'string', 'max:500'],
        ], [
            'avance.required' => 'Indica el porcentaje de avance.',
            'avance.integer' => 'El avance debe ser un número entero.',
            'avance.between' => 'El avance debe estar entre 0 y 99. Usa Finalizar orden para registrar el 100%.',
            'observaciones.max' => 'Las observaciones no pueden superar 500 caracteres.',
        ]);

        DB::transaction(function () use ($request, $orden, $datos) {
            [$actual, $reporte] = $this->bloquearOrden($request, $orden);
            if ((int) $datos['avance'] < $actual->avance) {
                throw ValidationException::withMessages([
                    'avance' => 'El avance no puede ser menor que el ya registrado.',
                ]);
            }

            $actual->avance = (int) $datos['avance'];
            $actual->estado = $actual->avance > 0 ? 'EN PROCESO' : 'PENDIENTE';
            $actual->observaciones = $datos['observaciones'] ?? null;
            $actual->save();

            if ($actual->avance > 0) {
                $reporte->estado = 'EN REPARACION';
                $reporte->save();
            }
        });

        return redirect()->route('ordenes.edit', $orden)->with('success', 'Avance guardado correctamente.');
    }

    public function finalizar(Request $request, OrdenTrabajo $orden): RedirectResponse
    {
        $this->verificarResponsable($request, $orden);
        $datos = $request->validate([
            'fecha_finalizacion' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'observaciones_finales' => ['nullable', 'string', 'max:500'],
        ], [
            'fecha_finalizacion.required' => 'Indica la fecha de finalización.',
            'fecha_finalizacion.date_format' => 'La fecha de finalización no es válida.',
            'fecha_finalizacion.before_or_equal' => 'La fecha de finalización no puede ser futura.',
            'observaciones_finales.max' => 'Las observaciones no pueden superar 500 caracteres.',
        ]);

        DB::transaction(function () use ($request, $orden, $datos) {
            [$actual, $reporte] = $this->bloquearOrden($request, $orden);
            if ($datos['fecha_finalizacion'] < $actual->fecha_asignacion->format('Y-m-d')) {
                throw ValidationException::withMessages([
                    'fecha_finalizacion' => 'La finalización no puede ser anterior a la asignación.',
                ]);
            }

            $actual->avance = 100;
            $actual->estado = 'FINALIZADA';
            $actual->fecha_finalizacion = $datos['fecha_finalizacion'];
            $actual->observaciones = $datos['observaciones_finales'] ?? null;
            $actual->save();

            $reporte->estado = 'FINALIZADO';
            $reporte->save();
        });

        return redirect()->route('ordenes.index')->with(
            'success', "Orden #{$orden->id_orden} finalizada correctamente. El reporte relacionado también quedó finalizado."
        );
    }

    private function verificarResponsable(Request $request, OrdenTrabajo $orden): void
    {
        $this->verificarAcceso($request);
        abort_unless(
            $request->user()->rol === 'AUTORIDAD'
                || (int) $request->user()->id_usuario === (int) $orden->usuario_id_usuario,
            403,
            'Solo la autoridad o el supervisor asignado pueden actualizar esta orden.'
        );
    }

    private function bloquearOrden(Request $request, OrdenTrabajo $orden): array
    {
        $actual = OrdenTrabajo::whereKey($orden->id_orden)->lockForUpdate()->get()->firstOrFail();
        $this->verificarResponsable($request, $actual);
        if ($actual->estado === 'FINALIZADA') {
            throw ValidationException::withMessages([
                'orden' => 'Esta orden ya está finalizada y no puede modificarse.',
            ]);
        }

        $reporte = Reporte::whereKey($actual->reporte_id_reporte)->lockForUpdate()->get()->firstOrFail();
        if (! in_array($reporte->estado, ['VALIDADO', 'EN REPARACION'], true)) {
            throw ValidationException::withMessages([
                'orden' => 'El estado del reporte no permite actualizar esta orden.',
            ]);
        }

        return [$actual, $reporte];
    }
}
