<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AutorizaPorRol;
use App\Models\Inspeccion;
use App\Models\Reporte;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InspeccionController extends Controller
{
    use AutorizaPorRol;

    private const RESULTADOS = ['APROBADO', 'RECHAZADO'];

    public function index(Request $request): View
    {
        $this->exigirConsulta($request);

        $resultado = $request->query('resultado');
        $resultado = in_array($resultado, self::RESULTADOS, true) ? $resultado : null;

        $inspecciones = Inspeccion::with(['reporte.via', 'reporte.tipoDano', 'inspector'])
            ->when($resultado, fn ($q) => $q->where('resultado', $resultado))
            ->orderByDesc('fecha_inspeccion')
            ->orderByDesc('id_inspeccion')
            ->paginate(10)
            ->withQueryString();

        return view('inspecciones.index', [
            'inspecciones' => $inspecciones,
            'resultadoActual' => $resultado,
            'pendientes' => Reporte::where('estado', 'PENDIENTE')->count(),
            'puedeGestionar' => $this->puedeGestionar($request),
        ]);
    }

    public function create(Request $request): View
    {
        $this->exigirGestion($request);

        $pendientes = Reporte::with(['via', 'tipoDano', 'usuario'])
            ->where('estado', 'PENDIENTE')
            ->orderBy('fecha_reporte')
            ->orderBy('id_reporte')
            ->get();

        return view('inspecciones.form', [
            'inspeccion' => null,
            'pendientes' => $pendientes,
            'reporteSeleccionado' => $request->query('reporte'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->exigirGestion($request);

        $datos = $this->validar($request, true);

        $inspeccion = DB::transaction(function () use ($request, $datos) {
            // Bloquear el reporte evita dos inspecciones simultáneas sobre el mismo reporte.
            $reporte = Reporte::whereKey($datos['reporte_id_reporte'])
                ->lockForUpdate()
                ->get()
                ->first();

            if (! $reporte || $reporte->estado !== 'PENDIENTE') {
                throw ValidationException::withMessages([
                    'reporte_id_reporte' => 'Solo se pueden inspeccionar reportes pendientes.',
                ]);
            }

            $inspeccion = Inspeccion::create([
                'reporte_id_reporte' => $reporte->id_reporte,
                'usuario_id_usuario' => $request->user()->id_usuario,
                'fecha_inspeccion' => now(),
                'resultado' => $datos['resultado'],
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            $this->aplicarEstado($reporte, $this->estadoPara($datos['resultado']));

            return $inspeccion;
        });

        $texto = $inspeccion->resultado === 'APROBADO'
            ? 'El reporte quedó validado y ya puede recibir una orden de trabajo.'
            : 'El reporte quedó rechazado.';

        return redirect()->route('inspecciones.show', $inspeccion)
            ->with('success', "Inspección #{$inspeccion->id_inspeccion} registrada. {$texto}");
    }

    public function show(Request $request, Inspeccion $inspeccion): View
    {
        $this->exigirConsulta($request);

        $inspeccion->load(['reporte.via', 'reporte.tipoDano', 'reporte.usuario', 'inspector']);

        return view('inspecciones.show', [
            'inspeccion' => $inspeccion,
            'motivoBloqueo' => $this->motivoBloqueo($inspeccion),
            'puedeGestionar' => $this->puedeGestionar($request),
        ]);
    }

    public function edit(Request $request, Inspeccion $inspeccion): View|RedirectResponse
    {
        $this->exigirGestion($request);

        $inspeccion->load(['reporte.via', 'reporte.tipoDano', 'reporte.usuario']);

        if ($motivo = $this->motivoBloqueo($inspeccion)) {
            return redirect()->route('inspecciones.show', $inspeccion)->withErrors(['inspeccion' => $motivo]);
        }

        return view('inspecciones.form', [
            'inspeccion' => $inspeccion,
            'pendientes' => collect(),
            'reporteSeleccionado' => null,
        ]);
    }

    public function update(Request $request, Inspeccion $inspeccion): RedirectResponse
    {
        $this->exigirGestion($request);

        $datos = $this->validar($request, false);

        DB::transaction(function () use ($inspeccion, $datos) {
            $reporte = Reporte::whereKey($inspeccion->reporte_id_reporte)
                ->lockForUpdate()
                ->get()
                ->firstOrFail();

            $inspeccion->setRelation('reporte', $reporte);

            if ($motivo = $this->motivoBloqueo($inspeccion)) {
                throw ValidationException::withMessages(['inspeccion' => $motivo]);
            }

            $inspeccion->resultado = $datos['resultado'];
            $inspeccion->observaciones = $datos['observaciones'] ?? null;
            $inspeccion->save();

            $this->aplicarEstado($reporte, $this->estadoPara($datos['resultado']));
        });

        return redirect()->route('inspecciones.show', $inspeccion)
            ->with('success', 'Inspección actualizada. El estado del reporte se ajustó al nuevo resultado.');
    }

    public function destroy(Request $request, Inspeccion $inspeccion): RedirectResponse
    {
        $this->exigirGestion($request);

        $reporteId = $inspeccion->reporte_id_reporte;

        DB::transaction(function () use ($inspeccion, $reporteId) {
            $reporte = Reporte::whereKey($reporteId)->lockForUpdate()->get()->firstOrFail();
            $inspeccion->setRelation('reporte', $reporte);

            if ($motivo = $this->motivoBloqueo($inspeccion)) {
                throw ValidationException::withMessages(['inspeccion' => $motivo]);
            }

            $inspeccion->delete();

            // El reporte vuelve al resultado de la inspección anterior, o a pendiente si no hay otra.
            $anterior = Inspeccion::where('reporte_id_reporte', $reporteId)
                ->orderByDesc('id_inspeccion')
                ->get()
                ->first();

            $this->aplicarEstado($reporte, $anterior ? $this->estadoPara($anterior->resultado) : 'PENDIENTE');
        });

        return redirect()->route('inspecciones.index')
            ->with('success', 'Inspección eliminada. El reporte volvió a su estado anterior.');
    }

    private function validar(Request $request, bool $conReporte): array
    {
        $reglas = [
            'resultado' => ['required', Rule::in(self::RESULTADOS)],
            'observaciones' => ['required_if:resultado,RECHAZADO', 'nullable', 'string', 'max:500'],
        ];

        if ($conReporte) {
            $reglas['reporte_id_reporte'] = ['required', 'integer', Rule::exists('reporte', 'id_reporte')];
        }

        return $request->validate($reglas, [
            'reporte_id_reporte.required' => 'Selecciona el reporte a inspeccionar.',
            'reporte_id_reporte.exists' => 'El reporte seleccionado no existe.',
            'resultado.required' => 'Indica si apruebas o rechazas el reporte.',
            'resultado.in' => 'El resultado debe ser Aprobado o Rechazado.',
            'observaciones.required_if' => 'Explica el motivo del rechazo en las observaciones.',
            'observaciones.max' => 'Las observaciones no pueden superar 500 caracteres.',
        ]);
    }

    private function estadoPara(string $resultado): string
    {
        return $resultado === 'APROBADO' ? 'VALIDADO' : 'RECHAZADO';
    }

    /**
     * La prioridad solo tiene sentido en reportes validados; se reinicia en los demás casos.
     */
    private function aplicarEstado(Reporte $reporte, string $estado): void
    {
        $reporte->estado = $estado;

        if ($estado !== 'VALIDADO') {
            $reporte->prioridad = 0;
        }

        $reporte->save();
    }

    /**
     * Una inspección solo se puede cambiar o borrar mientras no haya trabajo
     * en curso y sea la más reciente del reporte; así el estado siempre es coherente.
     */
    private function motivoBloqueo(Inspeccion $inspeccion): ?string
    {
        $reporte = $inspeccion->reporte;

        if (! $reporte) {
            return 'La inspección no tiene un reporte asociado.';
        }

        if ($reporte->ordenesTrabajo()->exists()) {
            return 'El reporte ya tiene una orden de trabajo, por eso la inspección no se puede modificar.';
        }

        if (! in_array($reporte->estado, ['VALIDADO', 'RECHAZADO'], true)) {
            return 'El estado actual del reporte no permite modificar esta inspección.';
        }

        $ultima = Inspeccion::where('reporte_id_reporte', $reporte->id_reporte)->max('id_inspeccion');

        if ((int) $ultima !== (int) $inspeccion->id_inspeccion) {
            return 'Solo se puede modificar la inspección más reciente de cada reporte.';
        }

        return null;
    }
}