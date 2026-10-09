@extends('layouts.app')

@php
    $title = 'Estadísticas de gestión';
    $nombresEstado = [
        'PENDIENTE' => 'Pendientes',
        'VALIDADO' => 'Validados',
        'RECHAZADO' => 'Rechazados',
        'EN REPARACION' => 'En reparación',
        'FINALIZADO' => 'Finalizados',
    ];
    $coloresEstado = [
        'PENDIENTE' => 'bg-amber-500',
        'VALIDADO' => 'bg-emerald-500',
        'RECHAZADO' => 'bg-rose-500',
        'EN REPARACION' => 'bg-blue-500',
        'FINALIZADO' => 'bg-teal-700',
    ];
    $maximoReportes = max(1, (int) $porEstado->max());
@endphp

@section('actions')
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
            <div class="gv-card">
                <div class="gv-card-header">
                    <h2 class="gv-title">Reportes por estado</h2>
                    <p class="gv-muted mt-1">{{ $totalReportes }} {{ $totalReportes === 1 ? 'reporte en total' : 'reportes en total' }}.</p>
                </div>
                <div class="gv-card-body space-y-5">
                    @foreach ($porEstado as $estado => $cantidad)
                        @php
                            $porcentaje = $totalReportes > 0 ? ($cantidad / $totalReportes) * 100 : 0;
                            $anchoBarra = ($cantidad / $maximoReportes) * 100;
                            $porcentajeTexto = rtrim(rtrim(number_format($porcentaje, 1, '.', ''), '0'), '.');
                            $tooltipId = 'porcentaje-estado-'.$loop->index;
                        @endphp
                        <div class="grid grid-cols-2 items-center gap-x-4 gap-y-2 sm:grid-cols-[9rem_minmax(0,1fr)_7rem]">
                            <span class="text-sm font-medium text-slate-700 sm:text-base">{{ $nombresEstado[$estado] }}</span>
                            <button type="button" aria-label="{{ $nombresEstado[$estado] }}: {{ $cantidad }} {{ $cantidad === 1 ? 'reporte' : 'reportes' }}"
                                    aria-describedby="{{ $tooltipId }}"
                                    class="group relative col-span-2 row-start-2 flex h-8 w-full items-center rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:col-span-1 sm:row-start-auto">
                                <span aria-hidden="true" class="block h-5 w-full overflow-hidden rounded-lg bg-slate-100">
                                    <span class="block h-full rounded-lg {{ $coloresEstado[$estado] }}" style="width: {{ $anchoBarra }}%"></span>
                                </span>
                                <span id="{{ $tooltipId }}" role="tooltip"
                                      class="pointer-events-none invisible absolute bottom-full left-1/2 z-10 mb-2 -translate-x-1/2 whitespace-nowrap rounded-lg bg-slate-900 px-3 py-2 text-xs font-medium text-white shadow-lg group-hover:visible group-focus:visible">
                                    {{ $porcentajeTexto }}% del total
                                </span>
                            </button>
                            <span class="col-start-2 row-start-1 text-right text-sm font-bold text-slate-900 sm:col-start-3 sm:row-start-auto sm:text-base">
                                {{ $cantidad }} {{ $cantidad === 1 ? 'reporte' : 'reportes' }}
                            </span>
                        </div>
                    @endforeach
                    @if ($totalReportes === 0)
                        <p class="gv-muted">Todavía no hay reportes registrados.</p>
                    @endif
                    @if ($totalReportes > 0)
                        <div aria-hidden="true" class="hidden grid-cols-[9rem_minmax(0,1fr)_7rem] gap-x-4 sm:grid">
                            <div class="col-start-2 flex justify-between border-t border-slate-200 pt-2 text-xs text-slate-400">
                                <span>0</span><span>{{ $maximoReportes }}</span>
                            </div>
                        </div>
                    @endif
                    <p class="gv-muted-xs">Cada barra representa la cantidad de reportes. Pasa el mouse sobre una barra para ver su porcentaje del total; también puedes seleccionarla con Tab o tocarla.</p>
                </div>
            </div>

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
