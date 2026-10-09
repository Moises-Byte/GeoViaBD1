@extends('layouts.app')

@php
    $title = 'Nueva orden de trabajo';
    $puedeCrear = $reportes->isNotEmpty() && $cuadrillas->isNotEmpty() && $supervisores->isNotEmpty();
@endphp

@section('actions')
    <a href="{{ route('ordenes.index') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700">
        Volver al listado
    </a>
@endsection

@section('slot')
    <section class="gv-card gv-card-body max-w-3xl">
        <h2 class="gv-title">Asignar una reparación</h2>
        <p class="gv-muted mt-2">Selecciona un reporte validado que todavía no tenga una orden. La nueva orden empieza pendiente y con avance de 0%.</p>

        @if ($errors->any())
            <div role="alert" class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                <p class="font-semibold">Revisa los datos del formulario.</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (! $puedeCrear)
            <div role="status" class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-900">
                @if ($reportes->isEmpty())
                    <p>No hay reportes validados sin una orden asignada.</p>
                @endif
                @if ($cuadrillas->isEmpty())
                    <p>Primero debe registrarse una cuadrilla.</p>
                @endif
                @if ($supervisores->isEmpty())
                    <p>Primero debe registrarse un usuario con rol SUPERVISOR.</p>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('ordenes.store') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="reporte_id_reporte" class="block text-sm font-medium text-slate-700">Reporte validado</label>
                <select id="reporte_id_reporte" name="reporte_id_reporte" required class="gv-auth-input">
                    <option value="">Selecciona un reporte</option>
                    @foreach ($reportes as $reporte)
                        <option value="{{ $reporte->id_reporte }}" @selected((string) old('reporte_id_reporte') === (string) $reporte->id_reporte)>
                            #{{ $reporte->id_reporte }} · {{ $reporte->via?->nombre_via }} · {{ $reporte->descripcion }} · {{ $reporte->prioridad }}/100
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('reporte_id_reporte')" class="mt-2" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="cuadrilla_id_cuadrilla" class="block text-sm font-medium text-slate-700">Cuadrilla</label>
                    <select id="cuadrilla_id_cuadrilla" name="cuadrilla_id_cuadrilla" required class="gv-auth-input">
                        <option value="">Selecciona una cuadrilla</option>
                        @foreach ($cuadrillas as $cuadrilla)
                            <option value="{{ $cuadrilla->id_cuadrilla }}" @selected((string) old('cuadrilla_id_cuadrilla') === (string) $cuadrilla->id_cuadrilla)>
                                {{ $cuadrilla->nombre }} · {{ $cuadrilla->responsable }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('cuadrilla_id_cuadrilla')" class="mt-2" />
                </div>

                <div>
                    <label for="usuario_id_usuario" class="block text-sm font-medium text-slate-700">Supervisor</label>
                    <select id="usuario_id_usuario" name="usuario_id_usuario" required class="gv-auth-input">
                        <option value="">Selecciona un supervisor</option>
                        @foreach ($supervisores as $supervisor)
                            <option value="{{ $supervisor->id_usuario }}" @selected((string) old('usuario_id_usuario') === (string) $supervisor->id_usuario)>
                                {{ $supervisor->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('usuario_id_usuario')" class="mt-2" />
                </div>
            </div>

            <div>
                <label for="fecha_asignacion" class="block text-sm font-medium text-slate-700">Fecha de asignación</label>
                <input type="date" id="fecha_asignacion" name="fecha_asignacion" required
                       value="{{ old('fecha_asignacion', today()->format('Y-m-d')) }}"
                       max="{{ today()->format('Y-m-d') }}" class="gv-auth-input">
                <x-input-error :messages="$errors->get('fecha_asignacion')" class="mt-2" />
            </div>

            <div>
                <label for="observaciones" class="block text-sm font-medium text-slate-700">Observaciones (opcional)</label>
                <textarea id="observaciones" name="observaciones" rows="4" maxlength="500" class="gv-auth-input">{{ old('observaciones') }}</textarea>
                <p class="gv-muted-xs mt-1">Máximo 500 caracteres.</p>
                <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
            </div>

            <button type="submit" @disabled(! $puedeCrear) class="gv-primary-button disabled:cursor-not-allowed disabled:opacity-50">
                Crear orden
            </button>
        </form>
    </section>
@endsection
