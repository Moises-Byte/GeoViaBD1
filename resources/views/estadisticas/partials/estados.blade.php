@php

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
