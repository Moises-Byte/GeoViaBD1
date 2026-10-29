<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AutorizaPorRol;
use App\Models\Reporte;
use App\Models\Via;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ViaController extends Controller
{
    use AutorizaPorRol;

    private const NIVELES = ['ALTO', 'MEDIO', 'BAJO'];

    public function index(Request $request): View
    {
        $this->exigirConsulta($request);

        $buscar = trim((string) $request->query('buscar', ''));

        $vias = Via::query()
            ->when($buscar !== '', fn ($q) => $q->whereRaw('UPPER(nombre_via) LIKE ?', ['%'.mb_strtoupper($buscar).'%']))
            ->orderBy('nombre_via')
            ->paginate(10)
            ->withQueryString();

        $reportesPorVia = Reporte::query()
            ->whereIn('via_id_via', $vias->pluck('id_via'))
            ->select('via_id_via')
            ->selectRaw('COUNT(*) AS cantidad')
            ->groupBy('via_id_via')
            ->pluck('cantidad', 'via_id_via');

        return view('vias.index', [
            'vias' => $vias,
            'reportesPorVia' => $reportesPorVia,
            'buscar' => $buscar,
            'puedeGestionar' => $this->puedeGestionar($request),
        ]);
    }

    public function create(Request $request): View
    {
        $this->exigirGestion($request);

        return view('vias.form', ['via' => null, 'niveles' => self::NIVELES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->exigirGestion($request);

        $via = Via::create($this->validar($request));

        return redirect()->route('vias.index')->with('success', "Se registró la vía «{$via->nombre_via}».");
    }

    public function edit(Request $request, Via $via): View
    {
        $this->exigirGestion($request);

        return view('vias.form', ['via' => $via, 'niveles' => self::NIVELES]);
    }

    public function update(Request $request, Via $via): RedirectResponse
    {
        $this->exigirGestion($request);

        $via->fill($this->validar($request, $via));
        $recalcular = $via->isDirty(['nivel_trafico', 'fecha_ultimo_mantenimiento']);
        $via->save();

        $mensaje = "Se actualizó la vía «{$via->nombre_via}».";
        if ($recalcular) {
            $mensaje .= ' Cambiaste datos que influyen en la prioridad: recalcula las prioridades para ver el efecto.';
        }

        return redirect()->route('vias.index')->with('success', $mensaje);
    }

    public function destroy(Request $request, Via $via): RedirectResponse
    {
        $this->exigirGestion($request);

        if ($via->reportes()->exists()) {
            return redirect()->route('vias.index')->withErrors([
                'eliminar' => "No se puede eliminar «{$via->nombre_via}» porque ya tiene reportes asociados.",
            ]);
        }

        try {
            $via->delete();
        } catch (QueryException $e) {
            // Por si alguien crea un reporte justo entre la comprobación y el borrado.
            report($e);

            return redirect()->route('vias.index')->withErrors([
                'eliminar' => "No se pudo eliminar «{$via->nombre_via}». Es posible que tenga datos relacionados.",
            ]);
        }

        return redirect()->route('vias.index')->with('success', "Se eliminó la vía «{$via->nombre_via}».");
    }

    private function validar(Request $request, ?Via $via = null): array
    {
        return $request->validate([
            'nombre_via' => [
                'required',
                'string',
                'max:150',
                Rule::unique('via', 'nombre_via')->ignore($via?->id_via, 'id_via'),
            ],
            'nivel_trafico' => ['required', Rule::in(self::NIVELES)],
            'fecha_ultimo_mantenimiento' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
        ], [
            'nombre_via.required' => 'Escribe el nombre de la vía.',
            'nombre_via.max' => 'El nombre no puede superar 150 caracteres.',
            'nombre_via.unique' => 'Ya existe una vía con ese nombre.',
            'nivel_trafico.required' => 'Selecciona el nivel de tráfico.',
            'nivel_trafico.in' => 'El nivel de tráfico debe ser Alto, Medio o Bajo.',
            'fecha_ultimo_mantenimiento.date_format' => 'La fecha de mantenimiento no es válida.',
            'fecha_ultimo_mantenimiento.before_or_equal' => 'La fecha de mantenimiento no puede ser futura.',
        ]);
    }
}