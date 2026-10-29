@extends('layouts.app')

@php
    $editando = $via !== null;
    $title = $editando ? 'Editar vía' : 'Nueva vía';
    $clase = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600';
@endphp

@section('actions')
    <a href="{{ route('vias.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
        Volver
    </a>
@endsection

@section('slot')
    <form method="POST"
          action="{{ $editando ? route('vias.update', $via) : route('vias.store') }}"
          class="mx-auto max-w-2xl"
          novalidate>
        @csrf
        @if ($editando)
            @method('PUT')
        @endif

        <section class="gv-card">
            <div class="gv-card-header">
                <h2 class="gv-title">{{ $editando ? 'Datos de la vía' : 'Registrar una vía' }}</h2>
                <p class="gv-muted-xs mt-0.5">El nivel de tráfico y la fecha de mantenimiento influyen en la prioridad de los reportes.</p>
            </div>

            <div class="space-y-5 p-5">
                <div>
                    <label for="nombre_via" class="block text-sm font-medium text-slate-700">Nombre de la vía</label>
                    <input id="nombre_via" name="nombre_via" type="text" maxlength="150" required
                           value="{{ old('nombre_via', $via?->nombre_via) }}"
                           placeholder="Ej.: 4a Avenida, zona 1"
                           class="{{ $clase }}">
                    @error('nombre_via')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nivel_trafico" class="block text-sm font-medium text-slate-700">Nivel de tráfico</label>
                    <select id="nivel_trafico" name="nivel_trafico" required class="{{ $clase }}">
                        <option value="">Selecciona…</option>
                        @foreach ($niveles as $nivel)
                            <option value="{{ $nivel }}" @selected(old('nivel_trafico', $via?->nivel_trafico) === $nivel)>
                                {{ ucfirst(mb_strtolower($nivel)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('nivel_trafico')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_ultimo_mantenimiento" class="block text-sm font-medium text-slate-700">
                        Último mantenimiento <span class="font-normal text-slate-400">(opcional)</span>
                    </label>
                    <input id="fecha_ultimo_mantenimiento" name="fecha_ultimo_mantenimiento" type="date"
                           max="{{ now()->format('Y-m-d') }}"
                           value="{{ old('fecha_ultimo_mantenimiento', $via?->fecha_ultimo_mantenimiento?->format('Y-m-d')) }}"
                           class="{{ $clase }}">
                    <p class="mt-1 text-xs text-slate-400">Si no se conoce, déjala vacía: no suma puntos de antigüedad.</p>
                    @error('fecha_ultimo_mantenimiento')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-5 py-4">
                <a href="{{ route('vias.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Cancelar</a>
                <button type="submit" class="gv-primary-button">{{ $editando ? 'Guardar cambios' : 'Registrar vía' }}</button>
            </div>
        </section>
    </form>
@endsection