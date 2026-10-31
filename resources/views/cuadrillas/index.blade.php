@extends('layouts.app')

@php
    $title = 'Cuadrillas';
@endphp

@section('actions')
    <a href="{{ route('cuadrillas.index') }}" class="gv-crew-outline">Ver todas</a>
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

        <form id="buscar-cuadrillas" method="GET" action="{{ route('cuadrillas.index') }}" class="gv-card gv-crew-search">
            <div class="gv-crew-search-field">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
                <label for="buscar" class="sr-only">Buscar por cuadrilla o responsable</label>
                <input id="buscar" name="buscar" type="search" value="" placeholder="Buscar por cuadrilla o responsable" class="rounded-lg border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
            </div>
            <button type="submit" class="gv-primary-button">Buscar</button>
        </form>

        <section aria-labelledby="directorio-cuadrillas">
            <div class="gv-crew-directory-heading">
                <h2 id="directorio-cuadrillas">Directorio de equipos</h2>
                <p>{{ $cuadrillas->total() }} {{ $cuadrillas->total() === 1 ? 'cuadrilla registrada' : 'cuadrillas registradas' }}</p>
            </div>
            <div class="gv-crew-grid">
                @forelse ($cuadrillas as $cuadrilla)
                    @php $cantidad = (int) ($ordenesPorCuadrilla[$cuadrilla->id_cuadrilla] ?? 0); @endphp
                    <article class="gv-card gv-crew-card">
                        <div class="gv-crew-card-heading">
                            <div class="gv-crew-icon">@include('layouts.partials.sidebar-icon', ['icon' => 'crews'])</div>
                            <h3>{{ $cuadrilla->nombre }}</h3>
                        </div>
                        <dl class="gv-crew-details">
                            <div>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M4 22v-3a8 8 0 0 1 16 0v3Z"/></svg>
                                <div><dt>Responsable</dt><dd>{{ $cuadrilla->responsable }}</dd></div>
                            </div>
                            <div>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linejoin="round" d="m5 3 4 4-2 3c2 4 4 6 7 7l3-2 4 4c-1 3-3 4-6 2C8 18 3 13 2 7c0-2 1-3 3-4Z"/></svg>
                                <div><dt>Teléfono</dt><dd>{{ $cuadrilla->telefono ?: 'Sin teléfono registrado' }}</dd></div>
                            </div>
                        </dl>
                        <div class="gv-crew-count">
                            @include('layouts.partials.sidebar-icon', ['icon' => 'reports'])
                            <span>{{ $cantidad }} {{ $cantidad === 1 ? 'orden asignada' : 'órdenes asignadas' }}</span>
                        </div>
                        @if (in_array(Auth::user()->rol, ['AUTORIDAD', 'SUPERVISOR'], true))
                            <a href="{{ route('ordenes.index', ['cuadrilla' => $cuadrilla->id_cuadrilla]) }}" class="gv-crew-outline gv-crew-work">
                                Ver trabajos <span aria-hidden="true">→</span>
                            </a>
                        @endif
                        @if ($puedeGestionar)
                            <div class="gv-crew-manage">
                                <a href="{{ route('cuadrillas.edit', $cuadrilla) }}" class="font-semibold text-emerald-700 underline">Editar</a>
                                @if ($cantidad === 0)
                                    <form method="POST" action="{{ route('cuadrillas.destroy', $cuadrilla) }}" data-confirm="¿Eliminar la cuadrilla {{ $cuadrilla->nombre }}? Esta acción no se puede deshacer." onsubmit="return confirm(this.dataset.confirm)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-rose-700 underline">Eliminar</button>
                                    </form>
                                @else
                                    <span class="text-slate-400" title="Tiene órdenes de trabajo asociadas">Eliminar</span>
                                @endif
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="gv-card gv-card-body gv-crew-empty">
                        <p>{{ $buscar !== '' ? 'No hay cuadrillas que coincidan con la búsqueda.' : 'Aún no hay cuadrillas registradas.' }}</p>
                        @if ($buscar !== '')
                            <a href="{{ route('cuadrillas.index') }}" class="gv-crew-outline">Ver todas</a>
                        @endif
                    </div>
                @endforelse
            </div>
            @if ($cuadrillas->hasPages())
                <div class="mt-6">{{ $cuadrillas->links() }}</div>
            @endif
        </section>
    </div>
@endsection
