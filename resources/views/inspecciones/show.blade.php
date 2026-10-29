@extends('layouts.app')

@php
    $title = 'Inspección #'.$inspeccion->id_inspeccion;
    $reporte = $inspeccion->reporte;
    $aprobada = $inspeccion->resultado === 'APROBADO';
@endphp

@section('actions')
    <a href="{{ route('inspecciones.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
        Volver
    </a>
@endsection

@section('slot')
    <div class="mx-auto max-w-3xl space-y-6">
        <x-alerta-exito />

        @if ($errors->has('inspeccion'))
            <div role="alert" class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                {{ $errors->first('inspeccion') }}
            </div>
        @endif

        <section class="gv-card">
            <div class="gv-card-header flex flex-wrap items-center justify-between gap-3">
                <h2 class="gv-title">Resultado de la inspección</h2>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $aprobada ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-rose-50 text-rose-700 ring-rose-600/20' }}">
                    {{ $aprobada ? 'Aprobado' : 'Rechazado' }}
                </span>
            </div>

            <dl class="space-y-4 p-5 text-sm">
                <div>
                    <dt class="gv-muted-xs">Inspector</dt>
                    <dd class="mt-1 text-slate-800">{{ $inspeccion->inspector?->nombre ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="gv-muted-xs">Fecha</dt>
                    <dd class="mt-1 text-slate-800">{{ $inspeccion->fecha_inspeccion?->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="gv-muted-xs">Observaciones</dt>
                    <dd class="mt-1 whitespace-pre-line text-slate-800">{{ $inspeccion->observaciones ?: 'Sin observaciones.' }}</dd>
                </div>
            </dl>
        </section>

        <section class="gv-card">
            <div class="gv-card-header flex flex-wrap items-center justify-between gap-3">
                <h2 class="gv-title">Reporte inspeccionado</h2>
                @if ($reporte)
                    <x-estado-badge :estado="$reporte->estado" />
                @endif
            </div>

            @if ($reporte)
                <div class="space-y-3 p-5 text-sm">
                    <p class="font-semibold text-slate-800">
                        #{{ $reporte->id_reporte }} · {{ $reporte->tipoDano?->nombre ?? 'Daño vial' }} en {{ $reporte->via?->nombre_via ?? 'vía sin nombre' }}
                    </p>
                    <p class="whitespace-pre-line text-slate-600">{{ $reporte->descripcion }}</p>
                    <p class="text-xs text-slate-500">
                        Reportado el {{ $reporte->fecha_reporte?->format('d/m/Y') }}
                        @if ($reporte->usuario) por {{ $reporte->usuario->nombre }} @endif
                    </p>
                    <a href="{{ route('reportes.show', $reporte) }}" class="inline-block font-semibold text-emerald-700 underline">Ver reporte completo</a>
                </div>
            @else
                <p class="p-5 text-sm text-slate-500">El reporte asociado ya no está disponible.</p>
            @endif
        </section>

        @if ($puedeGestionar)
            <section class="gv-card gv-card-body">
                @if ($motivoBloqueo)
                    <p class="text-sm text-slate-500">{{ $motivoBloqueo }}</p>
                @else
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm text-slate-500">Si cambias o eliminas la inspección, el estado del reporte se ajusta automáticamente.</p>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('inspecciones.edit', $inspeccion) }}" class="font-semibold text-emerald-700 underline">Editar</a>
                            <form method="POST" action="{{ route('inspecciones.destroy', $inspeccion) }}"
                                  data-confirm="¿Eliminar la inspección #{{ $inspeccion->id_inspeccion }}? El reporte volverá a su estado anterior."
                                  onsubmit="return confirm(this.dataset.confirm)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-semibold text-rose-700 underline">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @endif
            </section>
        @endif
    </div>
@endsection