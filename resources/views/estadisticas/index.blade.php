@extends('layouts.app')

@php
    $title = 'Estadísticas de gestión';
    $maximoPorVia = max(1, (int) $porVia->max('reportes_count'));
@endphp

@section('actions')
    <a href="{{ route('estadisticas.excel') }}" class="gv-primary-button">Descargar Excel</a>
    <a href="{{ route('estadisticas.index') }}" class="gv-crew-outline">Actualizar</a>
@endsection

@section('slot')
    <div class="gv-statistics space-y-6">
        <section class="gv-statistics-metrics grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Indicadores de gestión">
            <article class="gv-card gv-card-body gv-statistic-metric">
                <span class="gv-statistic-icon" aria-hidden="true">@include('layouts.partials.sidebar-icon', ['icon' => 'reports'])</span>
                <h2 class="text-sm font-medium text-slate-500">Reportes registrados</h2>
                <p class="gv-stat-number">{{ number_format($totalReportes) }}</p>
                <p class="gv-muted-xs mt-3">Total de reportes en todos los estados.</p>
            </article>
            <article class="gv-card gv-card-body gv-statistic-metric">
                <span class="gv-statistic-icon" aria-hidden="true">@include('layouts.partials.sidebar-icon', ['icon' => 'inspection'])</span>
                <h2 class="text-sm font-medium text-slate-500">Reportes finalizados</h2>
                <p class="gv-stat-number">{{ number_format($porEstado['FINALIZADO']) }}</p>
                <p class="gv-muted-xs mt-3">Reparaciones con el reporte cerrado.</p>
            </article>
            <article class="gv-card gv-card-body gv-statistic-metric">
                <span class="gv-statistic-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 3"/></svg></span>
                <h2 class="text-sm font-medium text-slate-500">Promedio de resolución</h2>
                <p class="gv-stat-number">
                    {{ $tiempoPromedio === null ? '—' : number_format($tiempoPromedio, 2) }}
                    @if ($tiempoPromedio !== null)
                        <span class="text-sm font-medium text-slate-500">días</span>
                    @endif
                </p>
                <p class="gv-muted-xs mt-3">
                    {{ $tiempoPromedio === null ? 'Aún no hay cierres con fechas válidas para calcular el promedio.' : 'Desde la fecha del reporte hasta el cierre de la reparación.' }}
                </p>
            </article>
            <article class="gv-card gv-card-body gv-statistic-metric">
                <span class="gv-statistic-icon" aria-hidden="true">@include('layouts.partials.sidebar-icon', ['icon' => 'crews'])</span>
                <h2 class="text-sm font-medium text-slate-500">Cuadrilla más activa</h2>
                <p class="mt-3 text-lg font-semibold text-slate-900">{{ $cuadrillaMasActiva?->nombre ?? 'Sin asignaciones' }}</p>
                <p class="gv-muted-xs mt-3">
                    @if ($cuadrillaMasActiva)
                        {{ (int) $cuadrillaMasActiva->ordenes_trabajo_count }} órdenes asignadas en total.
                    @else
                        Todavía no se han asignado órdenes de trabajo.
                    @endif
                </p>
            </article>
        </section>

        <section class="gv-statistics-panels grid gap-6 xl:grid-cols-2">
            @include('estadisticas.partials.estados')

            <div class="gv-card">
                <div class="gv-card-header">
                    <h2 class="gv-title">Reportes por vía</h2>
                    <p class="gv-muted-xs mt-1">Ordenados por cantidad de reportes, incluyendo vías sin reportes.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="gv-table gv-statistics-roads">
                        <thead class="gv-table-head">
                            <tr>
                                <th scope="col" class="gv-table-cell">Vía</th>
                                <th scope="col" class="gv-table-cell">Reportes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($porVia as $via)
                                <tr>
                                    <td class="gv-table-cell">{{ $via->nombre_via }}</td>
                                    <td class="gv-table-cell">
                                        <div class="gv-road-count">
                                            <span class="gv-road-track" aria-hidden="true"><span style="width: {{ ((int) $via->reportes_count / $maximoPorVia) * 100 }}%"></span></span>
                                            <strong>{{ (int) $via->reportes_count }}</strong>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="px-5 py-8 text-center text-slate-500">Todavía no hay vías registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <p class="gv-muted-xs">Promedio calculado desde la fecha del reporte hasta el cierre de la reparación. Los indicadores se actualizan al abrir esta página. Cuadrilla más activa: mayor cantidad total de órdenes asignadas.</p>
    </div>
@endsection
