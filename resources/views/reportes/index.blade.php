@extends('layouts.app')

@php
    $title = $esMunicipal ? 'Reportes viales' : 'Mis reportes';
@endphp

@section('actions')
    <a href="{{ route('reportes.create') }}" class="gv-primary-button">Nuevo reporte</a>
@endsection

@section('slot')
    <div class="space-y-6">
        <x-alerta-exito />

        <form method="GET" action="{{ route('reportes.index') }}" class="flex flex-wrap items-center gap-2">
            <label for="estado" class="text-sm font-medium text-slate-600">Estado:</label>
            <select id="estado" name="estado" onchange="this.form.submit()"
                    class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
                <option value="">Todos</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado }}" @selected($estadoActual === $estado)>{{ ucfirst(mb_strtolower($estado)) }}</option>
                @endforeach
            </select>
            <noscript><button type="submit" class="gv-primary-button">Filtrar</button></noscript>
            @if ($estadoActual)
                <a href="{{ route('reportes.index') }}" class="text-sm font-semibold text-emerald-700 underline">Quitar filtro</a>
            @endif
        </form>

        <section class="gv-card">
            <div class="gv-card-header">
                <h2 class="gv-title">{{ $reportes->total() }} {{ $reportes->total() === 1 ? 'reporte' : 'reportes' }}</h2>
                <p class="gv-muted-xs mt-0.5">
                    {{ $esMunicipal ? 'Todos los reportes registrados por los vecinos.' : 'Consulta el estado de lo que has reportado.' }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="gv-table">
                    <thead class="gv-table-head">
                        <tr>
                            <th scope="col" class="gv-table-cell">Reporte</th>
                            <th scope="col" class="gv-table-cell">Vía</th>
                            <th scope="col" class="gv-table-cell">Tipo de daño</th>
                            @if ($esMunicipal)
                                <th scope="col" class="gv-table-cell">Vecino</th>
                            @endif
                            <th scope="col" class="gv-table-cell">Fecha</th>
                            <th scope="col" class="gv-table-cell">Estado</th>
                            <th scope="col" class="gv-table-cell">Avance</th>
                            <th scope="col" class="gv-table-cell"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($reportes as $reporte)
                            @php $orden = $reporte->ordenesTrabajo->sortByDesc('id_orden')->first(); @endphp
                            <tr>
                                <td class="gv-table-cell font-semibold text-slate-800">#{{ $reporte->id_reporte }}</td>
                                <td class="gv-table-cell">{{ $reporte->via?->nombre_via ?? '—' }}</td>
                                <td class="gv-table-cell">{{ $reporte->tipoDano?->nombre ?? '—' }}</td>
                                @if ($esMunicipal)
                                    <td class="gv-table-cell">{{ $reporte->usuario?->nombre ?? '—' }}</td>
                                @endif
                                <td class="gv-table-cell whitespace-nowrap">{{ $reporte->fecha_reporte?->format('d/m/Y') }}</td>
                                <td class="gv-table-cell"><x-estado-badge :estado="$reporte->estado" /></td>
                                <td class="gv-table-cell">
                                    @if ($orden)
                                        <div class="flex items-center gap-2">
                                            <div class="h-2 w-20 overflow-hidden rounded-full bg-slate-100">
                                                <div class="h-2 rounded-full bg-emerald-500" style="width: {{ (int) $orden->avance }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold text-slate-600">{{ (int) $orden->avance }}%</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">Sin orden</span>
                                    @endif
                                </td>
                                <td class="gv-table-cell text-right">
                                    <a href="{{ route('reportes.show', $reporte) }}" class="whitespace-nowrap font-semibold text-emerald-700 underline">Ver detalle</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $esMunicipal ? 8 : 7 }}" class="px-5 py-10 text-center text-sm text-slate-500">
                                    @if ($estadoActual)
                                        No hay reportes con ese estado.
                                    @else
                                        Aún no hay reportes.
                                        <a href="{{ route('reportes.create') }}" class="font-semibold text-emerald-700 underline">Crea el primero</a>.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($reportes->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $reportes->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection