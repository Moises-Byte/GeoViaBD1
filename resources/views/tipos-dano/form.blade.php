@extends('layouts.app')

@php
    $editando = $tipo->exists;
    $title = $editando ? 'Editar tipo de daño' : 'Nuevo tipo de daño';
@endphp

@section('actions')
    <a href="{{ route('tipos-dano.index') }}"
       class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
        Volver al listado
    </a>
@endsection

@section('slot')
    <div class="w-full max-w-xl">
        <form method="POST"
              action="{{ $editando ? route('tipos-dano.update', $tipo) : route('tipos-dano.store') }}"
              class="space-y-5">
            @csrf

            @if ($editando)
                @method('PUT')
            @endif

            <div>
                <x-input-label for="nombre" value="Nombre" />

                <input id="nombre"
                       name="nombre"
                       type="text"
                       class="gv-auth-input"
                       value="{{ old('nombre', $tipo->nombre) }}"
                       maxlength="50"
                       required
                       autofocus>

                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="descripcion" value="Descripción (opcional)" />

                <textarea id="descripcion"
                          name="descripcion"
                          rows="4"
                          maxlength="255"
                          class="gv-auth-input">{{ old('descripcion', $tipo->descripcion) }}</textarea>

                <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
            </div>

            <div>
                <input type="hidden" name="activo" value="0">

                <label for="activo" class="inline-flex items-center gap-2">
                    <input id="activo"
                           name="activo"
                           type="checkbox"
                           value="1"
                           class="rounded border-slate-300 text-emerald-700 focus:ring-emerald-600"
                        @checked((string) old('activo', $tipo->activo === 'S' ? '1' : '0') === '1')>

                    <span class="text-sm font-medium text-slate-700">
                        Activo
                    </span>
                </label>

                <p class="mt-2 text-sm text-slate-500">
                    Los tipos inactivos no aparecen al crear reportes nuevos.
                </p>

                <x-input-error :messages="$errors->get('activo')" class="mt-2" />
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <button type="submit" class="gv-primary-button">
                    {{ $editando ? 'Guardar cambios' : 'Crear tipo de daño' }}
                </button>

                <a href="{{ route('tipos-dano.index') }}"
                   class="gv-auth-link text-sm">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
