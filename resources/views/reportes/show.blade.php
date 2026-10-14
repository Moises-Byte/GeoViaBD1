@extends('layouts.app')

@php
    $title = 'Reporte #'.$reporte->id_reporte;

    // Pasos del flujo de atención. Un reporte rechazado no sigue el flujo normal.
    $pasos = ['PENDIENTE' => 'Recibido', 'VALIDADO' => 'Validado', 'EN REPARACION' => 'En reparación', 'FINALIZADO' => 'Finalizado'];
    $indiceActual = array_search($reporte->estado, array_keys($pasos), true);
    $rechazado = $reporte->estado === 'RECHAZADO';
@endphp

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('actions')
    <a href="{{ route('reportes.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
        Volver
    </a>
@endsection

@section('slot')
    <div class="mx-auto max-w-5xl space-y-6">
        <x-alerta-exito />

        {{-- Estado y avance --}}
        <section class="gv-card gv-card-body">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="gv-title">{{ $reporte->tipoDano?->nombre ?? 'Daño vial' }} en {{ $reporte->via?->nombre_via ?? 'vía sin nombre' }}</h2>
                    <p class="gv-muted-xs mt-1">
                        Reportado el {{ $reporte->fecha_reporte?->format('d/m/Y') }}
                        @if ($esMunicipal && $reporte->usuario)
                            por {{ $reporte->usuario->nombre }}
                        @endif
                    </p>
                </div>
                <x-estado-badge :estado="$reporte->estado" />
            </div>

            @if ($rechazado)
                <div class="mt-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                    <p class="font-semibold">Este reporte no fue aprobado.</p>
                    <p class="mt-1">{{ $inspeccion?->observaciones ?: 'La autoridad municipal no dejó observaciones.' }}</p>
                </div>
            @else
                <ol class="mt-6 grid grid-cols-4 gap-2" aria-label="Progreso del reporte">
                    @foreach ($pasos as $clave => $etiqueta)
                        @php $hecho = $indiceActual !== false && $loop->index <= $indiceActual; @endphp
                        <li>
                            <div class="h-1.5 rounded-full {{ $hecho ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                            <p class="mt-2 text-xs {{ $hecho ? 'font-semibold text-slate-800' : 'text-slate-400' }}">{{ $etiqueta }}</p>
                        </li>
                    @endforeach
                </ol>
            @endif

            @if ($orden)
                <div class="mt-6 rounded-lg bg-slate-50 p-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-slate-800">Orden de trabajo #{{ $orden->id_orden }}</p>
                        <p class="text-xs text-slate-500">
                            {{ $orden->cuadrilla?->nombre ? 'Cuadrilla: '.$orden->cuadrilla->nombre : '' }}
                        </p>
                    </div>
                    <div class="mt-3 flex items-center gap-3">
                        <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-slate-200"
                             role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ (int) $orden->avance }}">
                            <div class="h-2.5 rounded-full bg-emerald-500" style="width: {{ (int) $orden->avance }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">{{ (int) $orden->avance }}%</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">
                        Asignada el {{ $orden->fecha_asignacion?->format('d/m/Y') }}
                        @if ($orden->fecha_finalizacion)
                            · Finalizada el {{ $orden->fecha_finalizacion->format('d/m/Y') }}
                        @endif
                    </p>
                </div>
            @elseif ($reporte->estado === 'PENDIENTE')
                <p class="mt-5 text-sm text-slate-500">Tu reporte está esperando la revisión de la autoridad municipal.</p>
            @elseif ($reporte->estado === 'VALIDADO')
                <p class="mt-5 text-sm text-slate-500">Tu reporte fue validado y pronto se asignará una cuadrilla.</p>
            @endif
        </section>

        <div class="grid gap-6 lg:grid-cols-5">
            <section class="gv-card lg:col-span-2">
                <div class="gv-card-header"><h2 class="gv-title">Detalle</h2></div>
                <dl class="space-y-4 p-5 text-sm">
                    <div>
                        <dt class="gv-muted-xs">Descripción</dt>
                        <dd class="mt-1 whitespace-pre-line text-slate-800">{{ $reporte->descripcion }}</dd>
                    </div>
                    <div>
                        <dt class="gv-muted-xs">Vía</dt>
                        <dd class="mt-1 text-slate-800">{{ $reporte->via?->nombre_via ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="gv-muted-xs">Tipo de daño</dt>
                        <dd class="mt-1 text-slate-800">{{ $reporte->tipoDano?->nombre ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="gv-muted-xs">Coordenadas</dt>
                        <dd class="mt-1 text-slate-800">{{ number_format((float) $reporte->latitud, 5) }}, {{ number_format((float) $reporte->longitud, 5) }}</dd>
                    </div>
                    @if ($esMunicipal)
                        <div>
                            <dt class="gv-muted-xs">Prioridad</dt>
                            <dd class="mt-1 text-slate-800">{{ $reporte->estado === 'PENDIENTE' ? 'Se calcula al validar' : $reporte->prioridad.' / 100' }}</dd>
                        </div>
                    @endif
                </dl>
            </section>

            <section class="gv-card lg:col-span-3">
                <div class="gv-card-header"><h2 class="gv-title">Ubicación</h2></div>
                <div class="p-5">
                    <div id="mapa" class="h-72 w-full rounded-lg border border-slate-200"></div>
                </div>
            </section>
        </div>

        <section class="gv-card">
            <div class="gv-card-header"><h2 class="gv-title">Fotografías</h2></div>
            <div class="p-5">
                @if ($reporte->fotografias->isEmpty())
                    <p class="text-sm text-slate-500">Este reporte no tiene fotografías.</p>
                @else
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                        @foreach ($reporte->fotografias as $foto)
                            <a href="{{ Storage::disk('public')->url($foto->ruta_foto) }}" target="_blank" rel="noopener">
                                <img src="{{ Storage::disk('public')->url($foto->ruta_foto) }}"
                                     alt="Fotografía {{ $loop->iteration }} del reporte"
                                     loading="lazy"
                                     class="h-32 w-full rounded-lg border border-slate-200 object-cover">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            const punto = [{{ (float) $reporte->latitud }}, {{ (float) $reporte->longitud }}];
            const mapa = L.map('mapa').setView(punto, 17);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(mapa);
            L.marker(punto).addTo(mapa);
        })();
    </script>
@endpush