<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AutorizaPorRol;
use App\Models\Cuadrilla;
use App\Models\OrdenTrabajo;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CuadrillaController extends Controller
{
    use AutorizaPorRol;

    public function index(Request $request): View
    {
        $this->exigirConsulta($request);

        $buscar = trim((string) $request->query('buscar', ''));

        $cuadrillas = Cuadrilla::query()
            ->when($buscar !== '', function ($q) use ($buscar) {
                $patron = '%'.mb_strtoupper($buscar).'%';
                $q->where(function ($sub) use ($patron) {
                    $sub->whereRaw('UPPER(nombre) LIKE ?', [$patron])
                        ->orWhereRaw('UPPER(responsable) LIKE ?', [$patron]);
                });
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $ordenesPorCuadrilla = OrdenTrabajo::query()
            ->whereIn('cuadrilla_id_cuadrilla', $cuadrillas->pluck('id_cuadrilla'))
            ->select('cuadrilla_id_cuadrilla')
            ->selectRaw('COUNT(*) AS cantidad')
            ->groupBy('cuadrilla_id_cuadrilla')
            ->pluck('cantidad', 'cuadrilla_id_cuadrilla');

        return view('cuadrillas.index', [
            'cuadrillas' => $cuadrillas,
            'ordenesPorCuadrilla' => $ordenesPorCuadrilla,
            'buscar' => $buscar,
            'puedeGestionar' => $this->puedeGestionar($request),
        ]);
    }

    public function create(Request $request): View
    {
        $this->exigirGestion($request);

        return view('cuadrillas.form', ['cuadrilla' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->exigirGestion($request);

        $cuadrilla = Cuadrilla::create($this->validar($request));

        return redirect()->route('cuadrillas.index')
            ->with('success', "Se registró la cuadrilla «{$cuadrilla->nombre}».");
    }

    public function edit(Request $request, Cuadrilla $cuadrilla): View
    {
        $this->exigirGestion($request);

        return view('cuadrillas.form', ['cuadrilla' => $cuadrilla]);
    }

    public function update(Request $request, Cuadrilla $cuadrilla): RedirectResponse
    {
        $this->exigirGestion($request);

        $cuadrilla->update($this->validar($request, $cuadrilla));

        return redirect()->route('cuadrillas.index')
            ->with('success', "Se actualizó la cuadrilla «{$cuadrilla->nombre}».");
    }

    public function destroy(Request $request, Cuadrilla $cuadrilla): RedirectResponse
    {
        $this->exigirGestion($request);

        if ($cuadrilla->ordenesTrabajo()->exists()) {
            return redirect()->route('cuadrillas.index')->withErrors([
                'eliminar' => "No se puede eliminar «{$cuadrilla->nombre}» porque tiene órdenes de trabajo asociadas.",
            ]);
        }

        try {
            $cuadrilla->delete();
        } catch (QueryException $e) {
            report($e);

            return redirect()->route('cuadrillas.index')->withErrors([
                'eliminar' => "No se pudo eliminar «{$cuadrilla->nombre}». Es posible que tenga datos relacionados.",
            ]);
        }

        return redirect()->route('cuadrillas.index')
            ->with('success', "Se eliminó la cuadrilla «{$cuadrilla->nombre}».");
    }

    private function validar(Request $request, ?Cuadrilla $cuadrilla = null): array
    {
        return $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('cuadrilla', 'nombre')->ignore($cuadrilla?->id_cuadrilla, 'id_cuadrilla'),
            ],
            'responsable' => ['required', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
        ], [
            'nombre.required' => 'Escribe el nombre de la cuadrilla.',
            'nombre.max' => 'El nombre no puede superar 100 caracteres.',
            'nombre.unique' => 'Ya existe una cuadrilla con ese nombre.',
            'responsable.required' => 'Indica quién es el responsable.',
            'responsable.max' => 'El nombre del responsable no puede superar 100 caracteres.',
            'telefono.max' => 'El teléfono no puede superar 20 caracteres.',
            'telefono.regex' => 'El teléfono solo puede llevar números, espacios, +, - y paréntesis (mínimo 7 dígitos).',
        ]);
    }
}