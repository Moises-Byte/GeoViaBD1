@extends('layouts.app')

@php
    $editando = $inspeccion !== null;
    $title = $editando ? 'Editar inspección' : 'Registrar inspección';
    $clase = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600';
    $resultadoActual = old('resultado', $inspeccion?->resultado);
    $seleccion = old('reporte_id_reporte', $reporteSeleccionado);
    $reporte = $inspeccion?->reporte;
@endphp

@section('actions')
    <a href="{{ $editando ? route('inspecciones.show', $inspeccion) : route('inspecciones.index') }}"
       class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
        Volver
    </a>
@endsection

@section('slot')
    <form method="POST"
          action="{{ $editando ? route('inspecciones.update', $inspeccion) : route('inspecciones.store') }}"
          class="mx-auto max-w-3xl space-y-6"
          novalidate>
        @csrf
        @if ($editando)
            @method('PUT')
        @endif

        @if ($errors->has('inspeccion'))
            <div role="alert" class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                {{ $errors->first('inspeccion') }}
            </div>
        @endif

        <section class="gv-card">
            <div class="gv-card-header">
                <h2 class="gv-title">1. Reporte a inspeccionar</h2>
                <p class="gv-muted-xs mt-0.5">
                    {{ $editando ? 'El reporte no se puede cambiar; si te equivocaste de reporte, elimina esta inspección y registra otra.' : 'Solo aparecen los reportes pendientes de inspección.' }}
                </p>
            </div>

            <div class="p-5">
                @if ($editando)
                    <div class="rounded-lg bg-slate-50 p-4 text-sm">
                        <p class="font-semibold text-slate-800">
                            Reporte #{{ $reporte->id_reporte }} · {{ $reporte->tipoDano?->nombre ?? 'Daño vial' }} en {{ $reporte->via?->nombre_via ?? 'vía sin nombre' }}
                        </p>
                        <p class="mt-1 whitespace-pre-line text-slate-600">{{ $reporte->descripcion }}</p>
                        <a href="{{ route('reportes.show', $reporte) }}" target="_blank" rel="noopener"
                           class="mt-2 inline-block font-semibold text-emerald-700 underline">Ver reporte completo (fotos y mapa)</a>
                    </div>
                @elseif ($pendientes->isEmpty())
                    <p class="text-sm text-slate-500">No hay reportes pendientes de inspección en este momento.</p>
                @else
                    <label for="reporte_id_reporte" class="block text-sm font-medium text-slate-700">Reporte</label>
                    <select id="reporte_id_reporte" name="reporte_id_reporte" required class="{{ $clase }}">
                        <option value="">Selecciona un reporte…</option>
                        @foreach ($pendientes as $pendiente)
                            <option value="{{ $pendiente->id_reporte }}"
                                    data-descripcion="{{ $pendiente->descripcion }}"
                                    @selected((string) $seleccion === (string) $pendiente->id_reporte)>
                                #{{ $pendiente->id_reporte }} · {{ $pendiente->via?->nombre_via ?? 'Sin vía' }} · {{ $pendiente->tipoDano?->nombre ?? 'Sin tipo' }} · {{ $pendiente->fecha_reporte?->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('reporte_id_reporte')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror

                    <div id="detalle-reporte" class="mt-3 hidden rounded-lg bg-slate-50 p-4 text-sm">
                        <p id="descripcion-reporte" class="whitespace-pre-line text-slate-600"></p>
                        <a id="enlace-reporte" href="#" target="_blank" rel="noopener"
                           class="mt-2 inline-block font-semibold text-emerald-700 underline">Ver reporte completo (fotos y mapa)</a>
                    </div>
                @endif
            </div>
        </section>

        <section class="gv-card">
            <div class="gv-card-header">
                <h2 class="gv-title">2. Resultado de la inspección</h2>
                <p class="gv-muted-xs mt-0.5">Al aprobar, el reporte queda validado y listo para una orden de trabajo.</p>
            </div>

            <div class="space-y-5 p-5">
                <fieldset>
                    <legend class="sr-only">Resultado</legend>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-4 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                            <input type="radio" name="resultado" value="APROBADO" class="mt-1 text-emerald-600 focus:ring-emerald-600" @checked($resultadoActual === 'APROBADO')>
                            <span>
                                <span class="block text-sm font-semibold text-slate-800">Aprobar</span>
                                <span class="block text-xs text-slate-500">El daño existe y debe atenderse.</span>
                            </span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-4 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50">
                            <input type="radio" name="resultado" value="RECHAZADO" class="mt-1 text-rose-600 focus:ring-rose-600" @checked($resultadoActual === 'RECHAZADO')>
                            <span>
                                <span class="block text-sm font-semibold text-slate-800">Rechazar</span>
                                <span class="block text-xs text-slate-500">No procede (duplicado, sin daño, fuera de jurisdicción…).</span>
                            </span>
                        </label>
                    </div>
                    @error('resultado')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div>
                    <label for="observaciones" class="block text-sm font-medium text-slate-700">
                        Observaciones <span class="font-normal text-slate-400">(obligatorias si rechazas; el vecino las verá)</span>
                    </label>
                    <textarea id="observaciones" name="observaciones" rows="4" maxlength="500"
                              class="{{ $clase }}">{{ old('observaciones', $inspeccion?->observaciones) }}</textarea>
                    @error('observaciones')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-5 py-4">
                <a href="{{ $editando ? route('inspecciones.show', $inspeccion) : route('inspecciones.index') }}"
                   class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Cancelar</a>
                <button type="submit" class="gv-primary-button">{{ $editando ? 'Guardar cambios' : 'Registrar inspección' }}</button>
            </div>
        </section>
    </form>
@endsection

@if (! $editando && $pendientes->isNotEmpty())
    @push('scripts')
        <script>
            (function () {
                const select = document.getElementById('reporte_id_reporte');
                const detalle = document.getElementById('detalle-reporte');
                const descripcion = document.getElementById('descripcion-reporte');
                const enlace = document.getElementById('enlace-reporte');
                const base = @json(url('/reportes')) + '/';

                function actualizar() {
                    const opcion = select.options[select.selectedIndex];
                    if (!select.value) {
                        detalle.classList.add('hidden');
                        return;
                    }
                    descripcion.textContent = opcion.dataset.descripcion || '';
                    enlace.href = base + select.value;
                    detalle.classList.remove('hidden');
                }

                select.addEventListener('change', actualizar);
                actualizar();
            })();
        </script>
    @endpush
@endif