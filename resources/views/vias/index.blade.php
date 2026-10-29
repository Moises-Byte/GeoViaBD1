@extends('layouts.app')

@php
    $title = 'Vías';

    $colores = [
        'ALTO' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
        'MEDIO' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'BAJO' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    ];
@endphp

@section('actions')
    @if ($puedeGestionar)
        <a href="{{ route('vias.create') }}" class="gv-primary-button">Nueva vía</a>
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

        <form method="GET" action="{{ route('vias.index') }}" class="flex flex-wrap items-center gap-2">
            <label for="buscar" class="sr-only">Buscar vía</label>
            <input id="buscar" name="buscar" type="search" value="{{ $buscar }}" placeholder="Buscar por nombre…"
                   class="w-full rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600 sm:w-72">
            <button type="submit" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Buscar</button>
            @if ($buscar !== '')
                <a href="{{ route('vias.index') }}" class="text-sm font-semibold text-emerald-700 underline">Quitar búsqueda</a>
            @endif
        </form>

        <section class="gv-card">
            <div class="gv-card-header">
                <h2 class="gv-title">{{ $vias->total() }} {{ $vias->total() === 1 ? 'vía' : 'vías' }}</h2>
                <p class="gv-muted-xs mt-0.5">Catálogo de vías que los vecinos pueden elegir al crear un reporte.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="gv-table">
                    <thead class="gv-table-head">
                        <tr>
                            <th scope="col" class="gv-table-cell">Vía</th>
                            <th scope="col" class="gv-table-cell">Tráfico</th>
                            <th scope="col" class="gv-table-cell">Último mantenimiento</th>
                            <th scope="col" class="gv-table-cell">Reportes</th>
                            @if ($puedeGestionar)
                                <th scope="col" class="gv-table-cell"><span class="sr-only">Acciones</span></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($vias as $via)
                            @php $cantidad = (int) ($reportesPorVia[$via->id_via] ?? 0); @endphp
                            <tr>
                                <td class="gv-table-cell font-semibold text-slate-800">{{ $via->nombre_via }}</td>
                                <td class="gv-table-cell">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $colores[$via->nivel_trafico] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20' }}">
                                        {{ ucfirst(mb_strtolower($via->nivel_trafico)) }}
                                    </span>
                                </td>
                                <td class="gv-table-cell whitespace-nowrap">
                                    {{ $via->fecha_ultimo_mantenimiento?->format('d/m/Y') ?? 'Sin registro' }}
                                </td>
                                <td class="gv-table-cell">{{ $cantidad }}</td>
                                @if ($puedeGestionar)
                                    <td class="gv-table-cell">
                                        <div class="flex items-center justify-end gap-4">
                                            <a href="{{ route('vias.edit', $via) }}" class="font-semibold text-emerald-700 underline">Editar</a>
                                            @if ($cantidad === 0)
                                                <form method="POST" action="{{ route('vias.destroy', $via) }}"
                                                      data-confirm="¿Eliminar la vía {{ $via->nombre_via }}? Esta acción no se puede deshacer."
                                                      onsubmit="return confirm(this.dataset.confirm)">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-semibold text-rose-700 underline">Eliminar</button>
                                                </form>
                                            @else
                                                <span class="cursor-not-allowed text-slate-400" title="Tiene reportes asociados">Eliminar</span>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $puedeGestionar ? 5 : 4 }}" class="px-5 py-10 text-center text-sm text-slate-500">
                                    {{ $buscar !== '' ? 'No hay vías que coincidan con la búsqueda.' : 'Aún no hay vías registradas.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($vias->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $vias->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection