@extends('layouts.app')

@php
    $title = 'Orden de trabajo #'.$orden->id_orden;
@endphp

@section('actions')
    <a href="{{ route('ordenes.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700">Volver al listado</a>
@endsection

@section('slot')
    <div class="max-w-3xl space-y-6">
        @if (session('success'))
            <div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="gv-card gv-card-body">
            <h2 class="gv-title">{{ $orden->reporte?->descripcion }}</h2>
            <dl class="mt-4 grid gap-4 sm:grid-cols-2 text-sm">
                <div><dt class="text-slate-500">Vía</dt><dd>{{ $orden->reporte?->via?->nombre_via }}</dd></div>
                <div><dt class="text-slate-500">Cuadrilla</dt><dd>{{ $orden->cuadrilla?->nombre }}</dd></div>
                <div><dt class="text-slate-500">Supervisor</dt><dd>{{ $orden->supervisor?->nombre }}</dd></div>
                <div><dt class="text-slate-500">Asignación</dt><dd>{{ $orden->fecha_asignacion?->format('d/m/Y') }}</dd></div>
                <div><dt class="text-slate-500">Estado de la orden</dt><dd class="font-semibold">{{ $orden->estado }}</dd></div>
                <div><dt class="text-slate-500">Estado del reporte</dt><dd>{{ $orden->reporte?->estado }}</dd></div>
            </dl>
            <p class="mt-5 font-semibold text-emerald-700">Avance: {{ $orden->avance }}%</p>
            <progress value="{{ $orden->avance }}" max="100" aria-label="Avance de la reparación" class="mt-2 h-3 w-full accent-emerald-600">{{ $orden->avance }}%</progress>
        </section>

        @if ($orden->estado === 'FINALIZADA')
            <section class="gv-card gv-card-body">
                <h2 class="gv-title">Trabajo finalizado</h2>
                <p class="mt-2 text-sm">Fecha de cierre: {{ $orden->fecha_finalizacion?->format('d/m/Y') }}</p>
                <p class="mt-2 whitespace-pre-wrap text-sm text-slate-600">{{ $orden->observaciones ?: 'Sin observaciones finales.' }}</p>
            </section>
        @else
            <section class="gv-card gv-card-body">
                <h2 class="gv-title">Actualizar avance</h2>
                <p class="gv-muted mt-2">Registra el porcentaje realizado. Al iniciar el trabajo, el reporte pasa a EN REPARACION.</p>
                <form method="POST" action="{{ route('ordenes.update', $orden) }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="avance" class="block text-sm font-medium text-slate-700">Avance (%)</label>
                        <input id="avance" name="avance" type="number" min="{{ $orden->avance }}" max="99" step="1" required value="{{ old('avance', $orden->avance) }}" class="gv-auth-input">
                        <p class="gv-muted-xs mt-1">De {{ $orden->avance }} a 99. Para completar el 100%, usa Finalizar orden.</p>
                    </div>
                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-slate-700">Observaciones del avance</label>
                        <textarea id="observaciones" name="observaciones" rows="4" maxlength="500" class="gv-auth-input">{{ old('observaciones', $orden->observaciones) }}</textarea>
                        <p class="gv-muted-xs mt-1">Máximo 500 caracteres. Se conserva la última actualización.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit" class="gv-primary-button">Guardar avance</button>
                        <a href="{{ route('ordenes.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Volver a órdenes
                        </a>
                    </div>
                </form>
            </section>

            <section class="gv-card gv-card-body">
                <h2 class="gv-title">Finalizar orden</h2>
                <p class="gv-muted mt-2">Usa esta acción cuando la reparación esté completa. La orden quedará al 100% y el reporte se marcará FINALIZADO.</p>
                <form method="POST" action="{{ route('ordenes.finalizar', $orden) }}" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="fecha_finalizacion" class="block text-sm font-medium text-slate-700">Fecha de finalización</label>
                        <input id="fecha_finalizacion" name="fecha_finalizacion" type="date" required min="{{ $orden->fecha_asignacion?->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}" value="{{ old('fecha_finalizacion', today()->format('Y-m-d')) }}" class="gv-auth-input">
                    </div>
                    <div>
                        <label for="observaciones_finales" class="block text-sm font-medium text-slate-700">Observaciones finales</label>
                        <textarea id="observaciones_finales" name="observaciones_finales" rows="4" maxlength="500" class="gv-auth-input">{{ old('observaciones_finales', $orden->observaciones) }}</textarea>
                    </div>
                    <button type="submit" class="gv-primary-button">Finalizar orden al 100%</button>
                </form>
            </section>
        @endif
    </div>
@endsection
