@extends('layouts.app')

@php
    $title = 'Inspecciones';

    $colores = [
        'APROBADO' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'RECHAZADO' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
    ];
@endphp

@section('actions')
    @if ($puedeGestionar)
        <a href="{{ route('inspecciones.create') }}" class="gv-primary-button">Registrar inspección</a>
    @endif
@endsection

@section('slot')
    <div class="space-y-6">
        <x-alerta-exito />

        <section class="gv-card gv-card-body flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-slate-500">Reportes pendientes de inspección</p>
                <p class="gv-stat-number">{{ $pendientes }}</p>
            </div>
            @if ($puedeGestionar && $pendientes > 0)
                <a href="{{ route('inspecciones.create') }}" class="text-sm font-semibold text-emerald-700 underline">Inspeccionar ahora</a>
            @endif
        </section>

        <form method="GET" action="{{ route('inspecciones.index') }}" class="flex flex-wrap items-center gap-2">
            <label for="resultado" class="text-sm font-medium text-slate-600">Resultado:</label>
            <select id="resultado" name="resultado" onchange="this.form.submit()"
                    class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
                <option value="">Todos</option>
                <option value="APROBADO" @selected($resultadoActual === 'APROBADO')>Aprobado</option>
                <option value="RECHAZADO" @selected($resultadoActual === 'RECHAZADO')>Rechazado</option>
            </select>
            <noscript><button type="submit" class="gv-primary-button">Filtrar</button></noscript>
            @if ($resultadoActual)
                <a href="{{ route('inspecciones.index') }}" class="text-sm font-semibold text-emerald-700 underline">Quitar filtro</a>
            @endif
        </form>

        <section class="gv-card">
            <div class="gv-card-header">
                <h2 class="gv-title">{{ $inspecciones->total() }} {{ $inspecciones->total() === 1 ? 'inspección' : 'inspecciones' }}</h2>
                <p class="gv-muted-xs mt-0.5">Cada inspección valida o rechaza un reporte de los vecinos.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="gv-table">
                    <thead class="gv-table-head">
                        <tr>
                            <th scope="col" class="gv-table-cell">Inspección</th>
                            <th scope="col" class="gv-table-cell">Reporte</th>
                            <th scope="col" class="gv-table-cell">Vía</th>
                            <th scope="col" class="gv-table-cell">Inspector</th>
                            <th scope="col" class="gv-table-cell">Fecha</th>
                            <th scope="col" class="gv-table-cell">Resultado</th>
                            <th scope="col" class="gv-table-cell"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($inspecciones as $inspeccion)
                            <tr>
                                <td class="gv-table-cell font-semibold text-slate-800">#{{ $inspeccion->id_inspeccion }}</td>
                                <td class="gv-table-cell">
                                    <a href="{{ route('reportes.show', $inspeccion->reporte_id_reporte) }}" class="font-semibold text-emerald-700 underline">#{{ $inspeccion->reporte_id_reporte }}</a>
                                    <span class="text-slate-500">· {{ $inspeccion->reporte?->tipoDano?->nombre ?? '—' }}</span>
                                </td>
                                <td class="gv-table-cell">{{ $inspeccion->reporte?->via?->nombre_via ?? '—' }}</td>
                                <td class="gv-table-cell">{{ $inspeccion->inspector?->nombre ?? '—' }}</td>
                                <td class="gv-table-cell whitespace-nowrap">{{ $inspeccion->fecha_inspeccion?->format('d/m/Y') }}</td>
                                <td class="gv-table-cell">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $colores[$inspeccion->resultado] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20' }}">
                                        {{ ucfirst(mb_strtolower($inspeccion->resultado)) }}
                                    </span>
                                </td>
                                <td class="gv-table-cell text-right">
                                    <a href="{{ route('inspecciones.show', $inspeccion) }}" class="whitespace-nowrap font-semibold text-emerald-700 underline">Ver detalle</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">
                                    {{ $resultadoActual ? 'No hay inspecciones con ese resultado.' : 'Aún no hay inspecciones registradas.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($inspecciones->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $inspecciones->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection