@extends('layouts.app')

@php
    $title = 'Mi panel';

    $tarjetas = [
        ['etiqueta' => 'Enviados', 'valor' => $total, 'nota' => 'Total de reportes que has creado'],
        ['etiqueta' => 'En revisión', 'valor' => (int) ($porEstado['PENDIENTE'] ?? 0), 'nota' => 'Esperando validación municipal'],
        ['etiqueta' => 'En atención', 'valor' => (int) ($porEstado['VALIDADO'] ?? 0) + (int) ($porEstado['EN REPARACION'] ?? 0), 'nota' => 'Validados o en reparación'],
        ['etiqueta' => 'Resueltos', 'valor' => (int) ($porEstado['FINALIZADO'] ?? 0), 'nota' => 'Daños ya reparados'],
    ];
@endphp

@section('actions')
    <a href="{{ route('reportes.create') }}" class="hidden sm:inline-flex gv-primary-button">
        Nuevo reporte
    </a>
@endsection

@section('slot')
    <div class="space-y-6">
        <x-alerta-exito />

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($tarjetas as $tarjeta)
                <article class="gv-card gv-card-body">
                    <p class="text-sm font-medium text-slate-500">{{ $tarjeta['etiqueta'] }}</p>
                    <p class="gv-stat-number">{{ $tarjeta['valor'] }}</p>
                    <p class="mt-3 text-xs text-slate-400">{{ $tarjeta['nota'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="gv-card">
            <div class="gv-card-header flex items-center justify-between gap-3">
                <div>
                    <h2 class="gv-title">Mis reportes recientes</h2>
                    <p class="gv-muted-xs mt-0.5">Sigue el avance de la reparación en tiempo real.</p>
                </div>
                @if ($total > 0)
                    <a href="{{ route('reportes.index') }}" class="text-sm font-semibold text-emerald-700 underline">Ver todos</a>
                @endif
            </div>

            @forelse ($recientes as $reporte)
                @php $orden = $reporte->ordenesTrabajo->sortByDesc('id_orden')->first(); @endphp
                <a href="{{ route('reportes.show', $reporte) }}"
                   class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 transition last:border-b-0 hover:bg-slate-50">
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-800">
                            {{ $reporte->tipoDano?->nombre ?? 'Daño vial' }} · {{ $reporte->via?->nombre_via ?? 'Vía sin nombre' }}
                        </p>
                        <p class="mt-1 truncate gv-muted">
                            #{{ $reporte->id_reporte }} · {{ $reporte->fecha_reporte?->format('d/m/Y') }}
                            @if ($orden) · Avance {{ (int) $orden->avance }}% @endif
                        </p>
                    </div>
                    <x-estado-badge :estado="$reporte->estado" />
                </a>
            @empty
                <div class="px-5 py-12 text-center">
                    <p class="font-semibold text-slate-800">Aún no has enviado reportes</p>
                    <p class="mt-1 gv-muted">¿Viste un bache o una calle dañada? Márcalo en el mapa y la municipalidad lo atenderá.</p>
                    <a href="{{ route('reportes.create') }}" class="gv-primary-button mt-5 inline-flex">Crear mi primer reporte</a>
                </div>
            @endforelse
        </section>
    </div>
@endsection