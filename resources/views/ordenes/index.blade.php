@extends('layouts.app')

@php
    $title = 'Órdenes de trabajo';
    $estadosOrden = ['PENDIENTE' => 'Pendiente', 'EN PROCESO' => 'En proceso', 'FINALIZADA' => 'Finalizada'];
@endphp

@section('actions')
    <a href="{{ route('ordenes.create') }}" class="gv-primary-button">+ Nueva orden</a>
@endsection

@section('slot')
    <div class="space-y-6">
        <x-alerta-exito />
        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="GET" action="{{ route('ordenes.index') }}" class="gv-card gv-orders-filters">
            <div class="gv-crew-search-field">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
                <label for="buscar-orden" class="sr-only">Buscar por orden, vía o descripción</label>
                <input id="buscar-orden" name="buscar" type="search" maxlength="150" value="{{ $buscar }}" placeholder="Buscar por orden, vía o descripción" class="rounded-lg border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
            </div>
            <label class="sr-only" for="estado-orden">Estado</label>
            <select id="estado-orden" name="estado" class="rounded-lg border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                <option value="">Todos los estados</option>
                @foreach ($estadosOrden as $valor => $nombre)
                    <option value="{{ $valor }}" @selected($estadoSeleccionado === $valor)>{{ $nombre }}</option>
                @endforeach
            </select>
            <label class="sr-only" for="cuadrilla-orden">Cuadrilla</label>
            <select id="cuadrilla-orden" name="cuadrilla" class="rounded-lg border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                <option value="">Todas las cuadrillas</option>
                @foreach ($cuadrillas as $cuadrilla)
                    <option value="{{ $cuadrilla->id_cuadrilla }}" @selected($cuadrillaSeleccionada?->id_cuadrilla == $cuadrilla->id_cuadrilla)>{{ $cuadrilla->nombre }}</option>
                @endforeach
            </select>
            <button type="submit" class="gv-primary-button">Buscar</button>
            <a href="{{ route('ordenes.index') }}" class="gv-crew-outline">Ver todas</a>
        </form>
        @if ($cuadrillaSeleccionada)
            <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                <p class="font-semibold text-slate-700">Trabajos de {{ $cuadrillaSeleccionada->nombre }}</p>
                <a href="{{ route('cuadrillas.index') }}" class="font-semibold text-emerald-700 underline">Volver a cuadrillas</a>
            </div>
        @endif
        <section class="gv-card gv-orders-card">
            <div class="gv-card-header">
                <h2 class="gv-title">Órdenes registradas</h2>
                <p class="gv-muted-xs mt-1">{{ $ordenes->total() }} {{ $ordenes->total() === 1 ? 'orden' : 'órdenes' }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="gv-table gv-orders-table">
                    <thead class="gv-table-head">
                        <tr>
                            <th scope="col" class="gv-table-cell">Orden / vía</th>
                            <th scope="col" class="gv-table-cell">Asignación</th>
                            <th scope="col" class="gv-table-cell">Estado</th>
                            <th scope="col" class="gv-table-cell">Avance</th>
                            <th scope="col" class="gv-table-cell">Fecha de asignación</th>
                            <th scope="col" class="gv-table-cell">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($ordenes as $orden)
                            <tr>
                                <td class="gv-table-cell">
                                    <p class="font-bold text-slate-900">#{{ $orden->id_orden }}</p>
                                    <p class="gv-order-description" title="{{ $orden->reporte?->descripcion }}">{{ $orden->reporte?->descripcion ?? 'Sin reporte' }}</p>
                                    <p class="gv-muted-xs mt-1">{{ $orden->reporte?->via?->nombre_via ?? 'Sin vía' }}</p>
                                </td>
                                <td class="gv-table-cell">
                                    <p class="font-medium">{{ $orden->cuadrilla?->nombre ?? 'Sin cuadrilla' }}</p>
                                    <p class="gv-muted-xs mt-1">{{ $orden->supervisor?->nombre ?? 'Sin supervisor' }}</p>
                                </td>
                                <td class="gv-table-cell">
                                    <span class="gv-order-status {{ $orden->estado === 'FINALIZADA' ? 'gv-order-done' : ($orden->estado === 'EN PROCESO' ? 'gv-order-working' : 'gv-order-pending') }}">{{ $estadosOrden[$orden->estado] ?? $orden->estado }}</span>
                                </td>
                                <td class="gv-table-cell">
                                    <div class="gv-order-progress">
                                        <span class="font-semibold">{{ (int) $orden->avance }}%</span>
                                        <div class="gv-order-progress-track" role="progressbar" aria-label="Avance de la orden {{ $orden->id_orden }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ (int) $orden->avance }}">
                                            <span style="width: {{ max(0, min(100, (int) $orden->avance)) }}%"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="gv-table-cell whitespace-nowrap">{{ $orden->fecha_asignacion?->format('d/m/Y') }}</td>
                                <td class="gv-table-cell">
                                    @if (Auth::user()->rol === 'AUTORIDAD' || (int) Auth::user()->id_usuario === (int) $orden->usuario_id_usuario)
                                        <a href="{{ route('ordenes.edit', $orden) }}" class="gv-crew-outline gv-order-action">{{ $orden->estado === 'FINALIZADA' ? 'Ver detalle' : 'Actualizar avance' }}</a>
                                    @else
                                        <span class="gv-muted-xs">Otro supervisor</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">{{ $buscar !== '' || $estadoSeleccionado !== '' || $cuadrillaSeleccionada ? 'No hay órdenes que coincidan con los filtros seleccionados.' : 'Todavía no hay órdenes de trabajo registradas.' }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="gv-orders-footer">
                <p class="gv-muted-xs">Mostrando {{ $ordenes->firstItem() ?? 0 }}–{{ $ordenes->lastItem() ?? 0 }} de {{ $ordenes->total() }} órdenes</p>
                @if ($ordenes->hasPages()) {{ $ordenes->links() }} @endif
            </div>
        </section>
    </div>
@endsection
