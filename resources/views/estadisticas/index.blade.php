@extends('layouts.app')

@php
    $title = 'Estadísticas de gestión';
@endphp

@section('actions')
    <a href="{{ route('estadisticas.excel') }}" class="gv-primary-button">Descargar Excel</a>
    <a href="{{ route('estadisticas.index') }}" class="gv-primary-button">Actualizar datos</a>
@endsection

@section('slot')
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Indicadores de gestión">
            <article class="gv-card gv-card-body">
                <h2 class="text-sm font-medium text-slate-500">Reportes registrados</h2>
                <p class="gv-stat-number">{{ number_format($totalReportes) }}</p>
                <p class="gv-muted-xs mt-3">Total de reportes en todos los estados.</p>
            </article>
            <article class="gv-card gv-card-body">
                <h2 class="text-sm font-medium text-slate-500">Reportes finalizados</h2>
                <p class="gv-stat-number">{{ number_format($porEstado['FINALIZADO']) }}</p>
                <p class="gv-muted-xs mt-3">Reparaciones con el reporte cerrado.</p>
            </article>
            <article class="gv-card gv-card-body">
                <h2 class="text-sm font-medium text-slate-500">Tiempo promedio de resolución</h2>
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
            <article class="gv-card gv-card-body">
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

        <section class="grid gap-6 xl:grid-cols-2">
            @include('estadisticas.partials.estados')

            <div class="gv-card">
                <div class="gv-card-header">
                    <h2 class="gv-title">Reportes por vía</h2>
                    <p class="gv-muted-xs mt-1">Ordenados por cantidad de reportes, incluyendo vías sin reportes.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="gv-table">
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
                                    <td class="gv-table-cell font-semibold">{{ (int) $via->reportes_count }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="px-5 py-8 text-center text-slate-500">Todavía no hay vías registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <p class="gv-muted-xs">Los indicadores se consultan al abrir o actualizar esta página. Cuadrilla más activa: mayor cantidad total de órdenes asignadas.</p>
    </div>
@endsection
