@extends('layouts.app')

@php
    $title = 'Dashboard municipal';
@endphp

@section('actions')
    <a href="{{ route('estadisticas.index') }}" class="gv-primary-button">Ver estadísticas</a>
@endsection

@section('slot')
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" aria-label="Indicadores de gestión municipal">
            <article class="gv-card gv-card-body">
                <h2 class="text-sm font-medium text-slate-500">Reportes registrados</h2>
                <p class="gv-stat-number">{{ number_format($totalReportes) }}</p>
                <p class="gv-muted-xs mt-3">Todos los estados.</p>
            </article>
            <article class="gv-card gv-card-body">
                <h2 class="text-sm font-medium text-slate-500">Reportes pendientes</h2>
                <p class="gv-stat-number">{{ $porEstado['PENDIENTE'] }}</p>
                <p class="gv-muted-xs mt-3">Esperando validación municipal.</p>
            </article>
            <article class="gv-card gv-card-body">
                <h2 class="text-sm font-medium text-slate-500">Órdenes activas</h2>
                <p class="gv-stat-number">{{ $ordenesActivas }}</p>
                <p class="gv-muted-xs mt-3">Pendientes o en proceso.</p>
            </article>
            <article class="gv-card gv-card-body">
                <h2 class="text-sm font-medium text-slate-500">Reportes finalizados</h2>
                <p class="gv-stat-number">{{ $porEstado['FINALIZADO'] }}</p>
                <p class="gv-muted-xs mt-3">Reparaciones con el reporte cerrado.</p>
            </article>
            <article class="gv-card gv-card-body">
                <h2 class="text-sm font-medium text-slate-500">Tiempo promedio de resolución</h2>
                <p class="gv-stat-number">
                    {{ $tiempoPromedio === null ? '—' : number_format($tiempoPromedio, 2) }}
                    @if ($tiempoPromedio !== null)<span class="text-sm font-medium text-slate-500">días</span>@endif
                </p>
                <p class="gv-muted-xs mt-3">{{ $tiempoPromedio === null ? 'Aún no hay cierres con fechas válidas.' : 'Desde el reporte hasta el cierre de la reparación.' }}</p>
            </article>
            <article class="gv-card gv-card-body">
                <h2 class="text-sm font-medium text-slate-500">Cuadrilla más activa</h2>
                <p class="mt-3 text-lg font-semibold text-slate-900">{{ $cuadrillaMasActiva?->nombre ?? 'Sin asignaciones' }}</p>
                <p class="gv-muted-xs mt-3">{{ $cuadrillaMasActiva ? ((int) $cuadrillaMasActiva->ordenes_trabajo_count).' órdenes asignadas en total.' : 'Todavía no se han asignado órdenes.' }}</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            @include('estadisticas.partials.estados')

            <div class="gv-card">
                <div class="gv-card-header flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="gv-title">Reportes con mayor prioridad</h2>
                        <p class="gv-muted-xs mt-1">Validados y todavía sin orden asignada.</p>
                    </div>
                    <a href="{{ route('prioridades.index') }}" class="text-sm font-semibold text-emerald-700 underline">Ver prioridades</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($reportesPrioritarios as $reporte)
                        <div class="flex items-start justify-between gap-4 px-5 py-4">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800">#{{ $reporte->id_reporte }} · {{ $reporte->descripcion }}</p>
                                <p class="gv-muted mt-1">{{ $reporte->via?->nombre_via }} · {{ $reporte->tipoDano?->nombre }}</p>
                                <p class="gv-muted-xs mt-1">Reportado: {{ $reporte->fecha_reporte?->format('d/m/Y') }}</p>
                            </div>
                            <span class="whitespace-nowrap rounded-lg bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-700">{{ $reporte->prioridad }}/100</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-sm text-slate-500">No hay reportes validados pendientes de asignar a una orden.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="gv-card">
            <div class="gv-card-header flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="gv-title">Órdenes de trabajo recientes</h2>
                    <p class="gv-muted-xs mt-1">Últimas cinco órdenes por fecha de asignación.</p>
                </div>
                <a href="{{ route('ordenes.index') }}" class="text-sm font-semibold text-emerald-700 underline">Ver todas las órdenes</a>
            </div>
            <div class="overflow-x-auto">
                <table class="gv-table">
                    <thead class="gv-table-head">
                        <tr>
                            <th scope="col" class="gv-table-cell">Orden</th>
                            <th scope="col" class="gv-table-cell">Reporte / vía</th>
                            <th scope="col" class="gv-table-cell">Cuadrilla</th>
                            <th scope="col" class="gv-table-cell">Supervisor</th>
                            <th scope="col" class="gv-table-cell">Estado</th>
                            <th scope="col" class="gv-table-cell">Avance</th>
                            <th scope="col" class="gv-table-cell">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($ordenesRecientes as $orden)
                            <tr>
                                <td class="gv-table-cell font-semibold">#{{ $orden->id_orden }}</td>
                                <td class="gv-table-cell">{{ $orden->reporte?->descripcion }}<p class="gv-muted-xs mt-1">{{ $orden->reporte?->via?->nombre_via }}</p></td>
                                <td class="gv-table-cell">{{ $orden->cuadrilla?->nombre }}</td>
                                <td class="gv-table-cell">{{ $orden->supervisor?->nombre }}</td>
                                <td class="gv-table-cell">{{ $orden->estado }}</td>
                                <td class="gv-table-cell font-semibold">{{ $orden->avance }}%</td>
                                <td class="gv-table-cell">
                                    @if (Auth::user()->rol === 'AUTORIDAD' || (int) Auth::user()->id_usuario === (int) $orden->usuario_id_usuario)
                                        <a href="{{ route('ordenes.edit', $orden) }}" class="whitespace-nowrap font-semibold text-emerald-700 underline">{{ $orden->estado === 'FINALIZADA' ? 'Ver cierre' : 'Actualizar avance' }}</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-8 text-center text-slate-500">Todavía no hay órdenes registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        <p class="gv-muted-xs">Los datos se consultan al abrir o actualizar el dashboard. Los puntajes de prioridad corresponden al último cálculo guardado.</p>
    </div>
@endsection
