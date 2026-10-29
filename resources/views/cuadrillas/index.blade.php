@extends('layouts.app')

@php
    $title = 'Cuadrillas';
@endphp

@section('actions')
    @if ($puedeGestionar)
        <a href="{{ route('cuadrillas.create') }}" class="gv-primary-button">Nueva cuadrilla</a>
    @endif
@endsection

@section('slot')
    <div class="space-y-6">
        <x-alerta-exito />

        @if ($errors->has('eliminar'))
            <div role="alert" class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                {{ $errors->first('eliminar') }}
            </div>
        @endif

        <form method="GET" action="{{ route('cuadrillas.index') }}" class="flex flex-wrap items-center gap-2">
            <label for="buscar" class="sr-only">Buscar cuadrilla</label>
            <input id="buscar" name="buscar" type="search" value="{{ $buscar }}" placeholder="Buscar por cuadrilla o responsable…"
                   class="w-full rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600 sm:w-72">
            <button type="submit" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Buscar</button>
            @if ($buscar !== '')
                <a href="{{ route('cuadrillas.index') }}" class="text-sm font-semibold text-emerald-700 underline">Quitar búsqueda</a>
            @endif
        </form>

        <section class="gv-card">
            <div class="gv-card-header">
                <h2 class="gv-title">{{ $cuadrillas->total() }} {{ $cuadrillas->total() === 1 ? 'cuadrilla' : 'cuadrillas' }}</h2>
                <p class="gv-muted-xs mt-0.5">Equipos de mantenimiento que se asignan a las órdenes de trabajo.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="gv-table">
                    <thead class="gv-table-head">
                        <tr>
                            <th scope="col" class="gv-table-cell">Cuadrilla</th>
                            <th scope="col" class="gv-table-cell">Responsable</th>
                            <th scope="col" class="gv-table-cell">Teléfono</th>
                            <th scope="col" class="gv-table-cell">Órdenes</th>
                            @if ($puedeGestionar)
                                <th scope="col" class="gv-table-cell"><span class="sr-only">Acciones</span></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($cuadrillas as $cuadrilla)
                            @php $cantidad = (int) ($ordenesPorCuadrilla[$cuadrilla->id_cuadrilla] ?? 0); @endphp
                            <tr>
                                <td class="gv-table-cell font-semibold text-slate-800">{{ $cuadrilla->nombre }}</td>
                                <td class="gv-table-cell">{{ $cuadrilla->responsable }}</td>
                                <td class="gv-table-cell whitespace-nowrap">{{ $cuadrilla->telefono ?: '—' }}</td>
                                <td class="gv-table-cell">{{ $cantidad }}</td>
                                @if ($puedeGestionar)
                                    <td class="gv-table-cell">
                                        <div class="flex items-center justify-end gap-4">
                                            <a href="{{ route('cuadrillas.edit', $cuadrilla) }}" class="font-semibold text-emerald-700 underline">Editar</a>
                                            @if ($cantidad === 0)
                                                <form method="POST" action="{{ route('cuadrillas.destroy', $cuadrilla) }}"
                                                      data-confirm="¿Eliminar la cuadrilla {{ $cuadrilla->nombre }}? Esta acción no se puede deshacer."
                                                      onsubmit="return confirm(this.dataset.confirm)">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-semibold text-rose-700 underline">Eliminar</button>
                                                </form>
                                            @else
                                                <span class="cursor-not-allowed text-slate-400" title="Tiene órdenes de trabajo asociadas">Eliminar</span>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $puedeGestionar ? 5 : 4 }}" class="px-5 py-10 text-center text-sm text-slate-500">
                                    {{ $buscar !== '' ? 'No hay cuadrillas que coincidan con la búsqueda.' : 'Aún no hay cuadrillas registradas.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($cuadrillas->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $cuadrillas->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection