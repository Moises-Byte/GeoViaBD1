@extends('layouts.app')

@php
    $editando = $cuadrilla !== null;
    $title = $editando ? 'Editar cuadrilla' : 'Nueva cuadrilla';
    $clase = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600';
@endphp

@section('actions')
    <a href="{{ route('cuadrillas.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
        Volver
    </a>
@endsection

@section('slot')
    <form method="POST"
          action="{{ $editando ? route('cuadrillas.update', $cuadrilla) : route('cuadrillas.store') }}"
          class="mx-auto max-w-2xl"
          novalidate>
        @csrf
        @if ($editando)
            @method('PUT')
        @endif

        <section class="gv-card">
            <div class="gv-card-header">
                <h2 class="gv-title">{{ $editando ? 'Datos de la cuadrilla' : 'Registrar una cuadrilla' }}</h2>
                <p class="gv-muted-xs mt-0.5">Estas cuadrillas aparecen al crear órdenes de trabajo.</p>
            </div>

            <div class="space-y-5 p-5">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-slate-700">Nombre de la cuadrilla</label>
                    <input id="nombre" name="nombre" type="text" maxlength="100" required
                           value="{{ old('nombre', $cuadrilla?->nombre) }}"
                           placeholder="Ej.: Cuadrilla Norte"
                           class="{{ $clase }}">
                    @error('nombre')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="responsable" class="block text-sm font-medium text-slate-700">Responsable</label>
                    <input id="responsable" name="responsable" type="text" maxlength="100" required
                           value="{{ old('responsable', $cuadrilla?->responsable) }}"
                           class="{{ $clase }}">
                    @error('responsable')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telefono" class="block text-sm font-medium text-slate-700">
                        Teléfono <span class="font-normal text-slate-400">(opcional)</span>
                    </label>
                    <input id="telefono" name="telefono" type="tel" maxlength="20"
                           value="{{ old('telefono', $cuadrilla?->telefono) }}"
                           placeholder="Ej.: 5555-1234"
                           class="{{ $clase }}">
                    @error('telefono')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-5 py-4">
                <a href="{{ route('cuadrillas.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Cancelar</a>
                <button type="submit" class="gv-primary-button">{{ $editando ? 'Guardar cambios' : 'Registrar cuadrilla' }}</button>
            </div>
        </section>
    </form>
@endsection