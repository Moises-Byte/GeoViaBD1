@extends('layouts.app')

@php
    $title = 'Prioridad de reportes';
    $esAutoridad = Auth::user()->rol === 'AUTORIDAD';
@endphp

@section('actions')
    @if ($esAutoridad)
        <form method="POST" action="{{ route('prioridades.recalcular') }}">
            @csrf

            <button type="submit" class="gv-primary-button">
                Recalcular prioridades
            </button>
        </form>
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

        <section class="gv-card gv-card-body">
            <h2 class="gv-title">Reportes validados</h2>

            <p class="mt-2 text-sm text-slate-600">
                Ordenados del puntaje más alto al más bajo.
                En caso de empate, aparece primero el más antiguo.
            </p>

            <p class="mt-2 text-sm text-slate-600">
                Puntaje de 0 a 100: reportes en la misma vía hasta 40 puntos,
                tiempo sin mantenimiento hasta 30 y tráfico hasta 30.
            </p>

            @if ($esAutoridad)
                <p class="mt-2 text-sm text-slate-500">
                    Pulsa “Recalcular prioridades” para actualizar los
                    puntajes con los datos actuales.
                </p>
            @endif
        </section>

        <section class="gv-card">
            <div class="gv-card-header">
                <h2 class="gv-title">
                    {{ $reportes->total() }} reportes validados
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="gv-table">
                    <thead class="gv-table-head">
                    <tr>
                        <th scope="col" class="gv-table-cell">Reporte</th>
                        <th scope="col" class="gv-table-cell">Descripción</th>
                        <th scope="col" class="gv-table-cell">Vía</th>
                        <th scope="col" class="gv-table-cell">Tipo de daño</th>
                        <th scope="col" class="gv-table-cell">Tráfico</th>
                        <th scope="col" class="gv-table-cell">Fecha</th>
                        <th scope="col" class="gv-table-cell">Puntaje</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                    @forelse ($reportes as $reporte)
                        <tr>
                            <td class="gv-table-cell">
                                #{{ $reporte->id_reporte }}
                            </td>

                            <td class="gv-table-cell">
                                <p class="max-w-md whitespace-pre-line break-words">{{ $reporte->descripcion }}</p>
                            </td>

                            <td class="gv-table-cell">
                                {{ $reporte->via?->nombre_via ?? 'Sin vía' }}
                            </td>

                            <td class="gv-table-cell">
                                {{ $reporte->tipoDano?->nombre ?? 'Sin tipo' }}
                            </td>

                            <td class="gv-table-cell">
                                {{ $reporte->via?->nivel_trafico ?? 'Sin dato' }}
                            </td>

                            <td class="gv-table-cell whitespace-nowrap">
                                {{ $reporte->fecha_reporte?->format('d/m/Y') }}
                            </td>

                            <td class="gv-table-cell whitespace-nowrap font-bold text-emerald-700">
                                {{ $reporte->prioridad }} / 100
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"
                                class="px-5 py-8 text-center text-slate-500">
                                Todavía no hay reportes validados.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if ($reportes->hasPages())
                <div class="p-4">
                    {{ $reportes->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
