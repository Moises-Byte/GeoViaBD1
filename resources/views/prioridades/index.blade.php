@extends('layouts.app')

@php $title = 'Prioridades de atención'; @endphp

@section('actions')
    <form method="POST" action="{{ route('prioridades.recalcular') }}">
        @csrf
        <button type="submit" class="gv-primary-button">Recalcular prioridades</button>
    </form>
@endsection

@section('slot')
    <div class="gv-priorities space-y-6">
        <x-alerta-exito />
        @if ($errors->any())
            <p role="alert" class="rounded-lg bg-rose-50 p-4 text-sm text-rose-800">{{ $errors->first() }}</p>
        @endif
        <section class="gv-priority-explanation" aria-labelledby="calculo-prioridad">
            <h2 id="calculo-prioridad">¿Cómo se calcula la prioridad?</h2>
            <div class="gv-priority-factors">
                <div><h3>Reportes en la vía</h3><p>4 puntos por reporte, hasta 40. No cuentan los rechazados.</p></div>
                <div><h3>Sin mantenimiento</h3><p>Aumenta con los días transcurridos, hasta 30 puntos al cumplir 365 días. Sin fecha registrada: 0 puntos.</p></div>
                <div><h3>Tráfico</h3><p>Alto suma 30 puntos; medio, 20; bajo, 10.</p></div>
            </div>
            <p class="gv-priority-formula">Se suman y redondean los tres factores. A mayor puntaje, mayor prioridad de atención.</p>
        </section>
        <form method="GET" action="{{ route('prioridades.index') }}" class="gv-priority-search">
            <div class="gv-crew-search-field">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
                <label for="buscar-prioridad" class="sr-only">Buscar por vía o descripción</label>
                <input id="buscar-prioridad" name="buscar" type="search" maxlength="150" value="{{ $buscar }}" placeholder="Buscar por vía o descripción" class="rounded-lg border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
            </div>
            <button type="submit" class="gv-primary-button">Buscar</button>
            <a href="{{ route('prioridades.index') }}" class="gv-crew-outline">Ver todos</a>
        </form>
        <section class="gv-card gv-priority-card">
            <div class="gv-card-header flex items-center justify-between gap-3">
                <h2 class="gv-title">Reportes validados</h2>
                <span class="gv-muted-xs">{{ $reportes->total() }} {{ $reportes->total() === 1 ? 'reporte' : 'reportes' }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="gv-table gv-priority-table">
                    <thead><tr>
                        <th scope="col">Orden</th><th scope="col">Reporte y vía</th><th scope="col">Prioridad</th><th scope="col">Factores</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($reportes as $reporte)
                            @php
                                $posicion = $reportes->firstItem() + $loop->index;
                                $cantidad = (int) ($cantidadesPorVia[$reporte->via_id_via] ?? 0);
                            @endphp
                            <tr>
                                <td><span class="gv-priority-rank">{{ str_pad($posicion, 2, '0', STR_PAD_LEFT) }}</span></td>
                                <td>
                                    <p class="font-semibold text-slate-900">#{{ $reporte->id_reporte }} · {{ $reporte->descripcion }}</p>
                                    <p class="gv-muted mt-1">{{ $reporte->via?->nombre_via ?? 'Sin vía' }}</p>
                                    <p class="gv-muted-xs mt-1">{{ $reporte->tipoDano?->nombre ?? 'Sin tipo' }} · {{ $reporte->fecha_reporte?->format('d/m/Y') }}</p>
                                </td>
                                <td>
                                    <p class="gv-priority-score"><strong>{{ $reporte->prioridad }}</strong><span>/ 100</span></p>
                                    <div class="gv-priority-track" role="meter" aria-label="Puntaje de prioridad del reporte {{ $reporte->id_reporte }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ (int) $reporte->prioridad }}">
                                        <span style="width: {{ max(0, min(100, (int) $reporte->prioridad)) }}%"></span>
                                    </div>
                                </td>
                                <td>
                                    <p><strong>Tráfico:</strong> {{ mb_strtolower($reporte->via?->nivel_trafico ?? 'Sin dato') }}</p>
                                    <p>{{ $cantidad }} {{ $cantidad === 1 ? 'reporte en la vía' : 'reportes en la vía' }}</p>
                                    <p class="gv-muted-xs mt-1">Mantenimiento: {{ $reporte->via?->fecha_ultimo_mantenimiento?->format('d/m/Y') ?? 'Sin fecha registrada' }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="gv-priority-empty">{{ $buscar !== '' ? 'No hay reportes validados que coincidan con la búsqueda.' : 'Todavía no hay reportes validados.' }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="gv-priority-note">Puntajes del último cálculo guardado. En caso de empate, se atiende primero el reporte más antiguo.</p>
            <div class="gv-orders-footer">
                <p class="gv-muted-xs">Mostrando {{ $reportes->firstItem() ?? 0 }}–{{ $reportes->lastItem() ?? 0 }} de {{ $reportes->total() }} reportes{{ $buscar !== '' ? ' filtrados' : '' }}</p>
                @if ($reportes->hasPages()) {{ $reportes->links() }} @endif
            </div>
        </section>
    </div>
@endsection
