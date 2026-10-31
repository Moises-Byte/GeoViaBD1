<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AutorizaPorRol;
use App\Models\TipoDano;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TipoDanoController extends Controller
{
    use AutorizaPorRol;

    public function index(Request $request): View
    {
        $this->exigirConsulta($request);

        $buscar = trim((string) $request->query('buscar', ''));

        $tipos = TipoDano::query()
            ->when($buscar !== '', function ($consulta) use ($buscar) {
                $consulta->whereRaw(
                    'UPPER(nombre) LIKE ?',
                    ['%'.mb_strtoupper($buscar).'%']
                );
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('tipos-dano.index', [
            'tipos' => $tipos,
            'buscar' => $buscar,
            'puedeGestionar' => $this->puedeGestionar($request),
        ]);
    }

    public function create(Request $request): View
    {
        $this->exigirGestion($request);

        return view('tipos-dano.form', [
            'tipo' => new TipoDano(['activo' => 'S']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->exigirGestion($request);

        TipoDano::create($this->validar($request));

        return redirect()->route('tipos-dano.index')
            ->with('success', 'Tipo de daño registrado correctamente.');
    }

    public function edit(Request $request, TipoDano $tipo): View
    {
        $this->exigirGestion($request);

        return view('tipos-dano.form', [
            'tipo' => $tipo,
        ]);
    }

    public function update(Request $request, TipoDano $tipo): RedirectResponse
    {
        $this->exigirGestion($request);

        $tipo->update($this->validar($request, $tipo));

        return redirect()->route('tipos-dano.index')
            ->with('success', 'Tipo de daño actualizado correctamente.');
    }

    private function validar(Request $request, ?TipoDano $tipo = null): array
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tipo_dano', 'nombre')
                    ->ignore($tipo?->getKey(), 'id_tipo_dano'),
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activo' => ['required', 'boolean'],
        ], [
            'nombre.required' => 'Escribe el nombre del tipo de daño.',
            'nombre.max' => 'El nombre no puede superar 50 caracteres.',
            'nombre.unique' => 'Ya existe un tipo de daño con ese nombre.',
            'descripcion.max' => 'La descripción no puede superar 255 caracteres.',
            'activo.required' => 'Indica si el tipo de daño está activo.',
            'activo.boolean' => 'El estado seleccionado no es válido.',
        ]);

        $datos['activo'] = $request->boolean('activo') ? 'S' : 'N';

        return $datos;
    }
}
