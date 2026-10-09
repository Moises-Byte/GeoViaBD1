@extends('layouts.app')

@php
    $title = 'Órdenes de trabajo';
@endphp

@section('actions')
    <a href="{{ route('ordenes.create') }}" class="gv-primary-button">Nueva orden</a>
@endsection

@section('slot')
    <div class="space-y-6">
        @if (session('success'))
            <div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="gv-card">
            <div class="gv-card-header">
                <h2 class="gv-title">{{ $ordenes->total() }} órdenes registradas</h2>
                <p class="gv-muted mt-1">Asignaciones de cuadrillas y supervisores para atender reportes validados.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="gv-table">
                    <thead class="gv-table-head">
                        <tr>
                            <th scope="col" class="gv-table-cell">Orden</th>
                            <th scope="col" class="gv-table-cell">Reporte / vía</th>
                            <th scope="col" class="gv-table-cell">Cuadrilla</th>
                            <th scope="col" class="gv-table-cell">Supervisor</th>
                            <th scope="col" class="gv-table-cell">Asignación</th>
                            <th scope="col" class="gv-table-cell">Estado</th>
                            <th scope="col" class="gv-table-cell">Avance</th>
                            <th scope="col" class="gv-table-cell">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($ordenes as $orden)
                            <tr>
                                <td class="gv-table-cell font-semibold">#{{ $orden->id_orden }}</td>
                                <td class="gv-table-cell">
                                    <p>#{{ $orden->reporte_id_reporte }} · {{ $orden->reporte?->descripcion }}</p>
                                    <p class="gv-muted-xs mt-1">{{ $orden->reporte?->via?->nombre_via ?? 'Sin vía' }}</p>
                                </td>
                                <td class="gv-table-cell">{{ $orden->cuadrilla?->nombre ?? 'Sin cuadrilla' }}</td>
                                <td class="gv-table-cell">{{ $orden->supervisor?->nombre ?? 'Sin supervisor' }}</td>
                                <td class="gv-table-cell whitespace-nowrap">{{ $orden->fecha_asignacion?->format('d/m/Y') }}</td>
                                <td class="gv-table-cell">{{ $orden->estado }}</td>
                                <td class="gv-table-cell">{{ $orden->avance }}%</td>
                                <td class="gv-table-cell max-w-xs break-words">{{ $orden->observaciones ?: 'Sin observaciones' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-slate-500">
                                    Todavía no hay órdenes de trabajo. Pulsa “Nueva orden” para crear la primera.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($ordenes->hasPages())
                <div class="p-4">{{ $ordenes->links() }}</div>
            @endif
        </section>
    </div>
@endsection
