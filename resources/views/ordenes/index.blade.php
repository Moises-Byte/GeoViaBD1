@extends('layouts.app')

@php
    $esAutoridad = Auth::user()->rol === 'AUTORIDAD';
    $title = $esAutoridad ? 'Órdenes de trabajo' : 'Mis órdenes de trabajo';
@endphp

@section('actions')
    @if ($esAutoridad)
        <a href="{{ route('ordenes.create') }}" class="gv-primary-button">
            Nueva orden
        </a>
    @endif
@endsection

@section('slot')
    <div class="space-y-6">
        @if (session('success'))
            <div role="status"
                 class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="gv-card">
            <div class="gv-card-header">
                <h2 class="gv-title">
                    {{ $ordenes->total() }} órdenes
                    {{ $esAutoridad ? 'registradas' : 'asignadas a tu cuenta' }}
                </h2>

                <p class="gv-muted mt-1">
                    {{ $esAutoridad
                        ? 'Asignaciones de cuadrillas y supervisores para atender reportes validados.'
                        : 'Seguimiento de las reparaciones que tienes asignadas.' }}
                </p>
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
                        <th scope="col" class="gv-table-cell">Finalización</th>
                        <th scope="col" class="gv-table-cell">Estado</th>
                        <th scope="col" class="gv-table-cell">Avance</th>
                        <th scope="col" class="gv-table-cell">Observaciones</th>
                        <th scope="col" class="gv-table-cell">Acciones</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                    @forelse ($ordenes as $orden)
                        @php
                            $puedeActualizar = $esAutoridad
                                || (
                                    Auth::user()->rol === 'SUPERVISOR'
                                    && (int) Auth::user()->id_usuario
                                        === (int) $orden->usuario_id_usuario
                                );
                        @endphp

                        <tr>
                            <td class="gv-table-cell font-semibold">
                                #{{ $orden->id_orden }}
                            </td>

                            <td class="gv-table-cell">
                                <p>
                                    #{{ $orden->reporte_id_reporte }}
                                    · {{ $orden->reporte?->descripcion }}
                                </p>
                                <p class="gv-muted-xs mt-1">
                                    {{ $orden->reporte?->via?->nombre_via ?? 'Sin vía' }}
                                </p>
                            </td>

                            <td class="gv-table-cell">
                                {{ $orden->cuadrilla?->nombre ?? 'Sin cuadrilla' }}
                            </td>

                            <td class="gv-table-cell">
                                {{ $orden->supervisor?->nombre ?? 'Sin supervisor' }}
                            </td>

                            <td class="gv-table-cell whitespace-nowrap">
                                {{ $orden->fecha_asignacion?->format('d/m/Y') }}
                            </td>

                            <td class="gv-table-cell whitespace-nowrap">
                                {{ $orden->fecha_finalizacion?->format('d/m/Y') ?? 'Pendiente' }}
                            </td>

                            <td class="gv-table-cell">
                                {{ $orden->estado }}
                            </td>

                            <td class="gv-table-cell">
                                {{ $orden->avance }}%
                            </td>

                            <td class="gv-table-cell">
                                <p class="max-w-xs whitespace-pre-line break-words">{{ $orden->observaciones ?: 'Sin observaciones' }}</p>
                            </td>

                            <td class="gv-table-cell">
                                @if ($puedeActualizar)
                                    <a href="{{ route('ordenes.edit', $orden) }}"
                                       class="whitespace-nowrap font-semibold text-emerald-700 underline">
                                        {{ $orden->estado === 'FINALIZADA' ? 'Ver cierre' : 'Actualizar avance' }}
                                    </a>
                                @else
                                    <span class="gv-muted-xs">Sin permiso de edición</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10"
                                class="px-5 py-8 text-center text-slate-500">
                                No hay órdenes de trabajo disponibles para tu cuenta.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if ($ordenes->hasPages())
                <div class="p-4">
                    {{ $ordenes->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
