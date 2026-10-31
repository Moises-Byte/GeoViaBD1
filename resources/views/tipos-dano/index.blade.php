@extends('layouts.app')

@php
    $title = 'Tipos de daño';
@endphp

@section('actions')
    @if ($puedeGestionar)
        <a href="{{ route('tipos-dano.create') }}" class="gv-primary-button">
            Nuevo tipo de daño
        </a>
    @endif
@endsection

@section('slot')
    <div class="space-y-6">
        @if (session('success'))
            <div role="status"
                 class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('tipos-dano.index') }}"
              class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:w-80">
                <x-input-label for="buscar" value="Buscar por nombre" />

                <input id="buscar"
                       name="buscar"
                       type="search"
                       class="gv-auth-input"
                       value="{{ $buscar }}">
            </div>

            <button type="submit" class="gv-primary-button">
                Buscar
            </button>

            @if ($buscar !== '')
                <a href="{{ route('tipos-dano.index') }}"
                   class="gv-auth-link text-sm">
                    Limpiar
                </a>
            @endif
        </form>

        <div class="overflow-x-auto border border-slate-200 bg-white">
            <table class="gv-table">
                <thead class="gv-table-head">
                <tr>
                    <th scope="col" class="gv-table-cell">Nombre</th>
                    <th scope="col" class="gv-table-cell">Descripción</th>
                    <th scope="col" class="gv-table-cell">Estado</th>
                    @if ($puedeGestionar)
                        <th scope="col" class="gv-table-cell">Acciones</th>
                    @endif
                </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                @forelse ($tipos as $tipo)
                    <tr>
                        <td class="gv-table-cell">
                            <p class="max-w-xs break-words font-semibold">
                                {{ $tipo->nombre }}
                            </p>
                        </td>

                        <td class="gv-table-cell">
                            <p class="max-w-md whitespace-pre-line break-words">{{ $tipo->descripcion ?? 'Sin descripción' }}</p>
                        </td>

                        <td class="gv-table-cell">
                                <span class="whitespace-nowrap {{ $tipo->activo === 'S' ? 'text-emerald-700' : 'text-slate-500' }}">
                                    {{ $tipo->activo === 'S' ? 'Activo' : 'Inactivo' }}
                                </span>
                        </td>

                        @if ($puedeGestionar)
                            <td class="gv-table-cell">
                                <a href="{{ route('tipos-dano.edit', $tipo) }}"
                                   class="gv-auth-link text-sm">
                                    Editar
                                </a>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $puedeGestionar ? 4 : 3 }}"
                            class="px-5 py-8 text-center text-sm text-slate-500">
                            No se encontraron tipos de daño.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $tipos->links() }}
    </div>
@endsection
