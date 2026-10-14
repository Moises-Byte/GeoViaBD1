@extends('layouts.app')

@php
    $title = 'Nuevo reporte';
@endphp

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('actions')
    <a href="{{ route('reportes.index') }}" class="hidden rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 sm:inline-flex">
        Mis reportes
    </a>
@endsection

@section('slot')
    <form method="POST"
          action="{{ route('reportes.store') }}"
          enctype="multipart/form-data"
          class="mx-auto max-w-5xl space-y-6"
          novalidate>
        @csrf

        @if ($errors->has('general'))
            <div role="alert" class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                {{ $errors->first('general') }}
            </div>
        @endif

        @if ($errors->any() && ! $errors->has('general'))
            <div role="alert" class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                Revisa los campos marcados en rojo antes de enviar el reporte.
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-5">
            {{-- Datos del daño --}}
            <section class="gv-card lg:col-span-2">
                <div class="gv-card-header">
                    <h2 class="gv-title">1. ¿Qué encontraste?</h2>
                    <p class="gv-muted-xs mt-0.5">Cuéntanos dónde está y qué tipo de daño es.</p>
                </div>

                <div class="space-y-5 p-5">
                    <div>
                        <label for="via_id_via" class="block text-sm font-medium text-slate-700">Vía afectada</label>
                        <select id="via_id_via" name="via_id_via" required
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
                            <option value="">Selecciona una vía…</option>
                            @foreach ($vias as $via)
                                <option value="{{ $via->id_via }}" @selected((string) old('via_id_via') === (string) $via->id_via)>
                                    {{ $via->nombre_via }}
                                </option>
                            @endforeach
                        </select>
                        @error('via_id_via')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                        @if ($vias->isEmpty())
                            <p class="mt-1 text-xs text-amber-600">Aún no hay vías registradas. Pide a la municipalidad que las agregue.</p>
                        @endif
                    </div>

                    <div>
                        <label for="tipo_dano_id_tipo_dano" class="block text-sm font-medium text-slate-700">Tipo de daño</label>
                        <select id="tipo_dano_id_tipo_dano" name="tipo_dano_id_tipo_dano" required
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
                            <option value="">Selecciona el tipo de daño…</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id_tipo_dano }}" @selected((string) old('tipo_dano_id_tipo_dano') === (string) $tipo->id_tipo_dano)>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_dano_id_tipo_dano')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-slate-700">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="5" maxlength="500" required
                                  placeholder="Ej.: Bache grande frente a la tienda, ya dañó llantas de varios carros."
                                  class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600">{{ old('descripcion') }}</textarea>
                        <div class="mt-1 flex justify-between">
                            <div>
                                @error('descripcion')
                                    <p class="text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <p class="text-xs text-slate-400"><span id="contador">0</span>/500</p>
                        </div>
                    </div>

                    <div>
                        <label for="fotos" class="block text-sm font-medium text-slate-700">
                            Fotografías <span class="font-normal text-slate-400">(opcional, hasta {{ $maxFotos }})</span>
                        </label>
                        <input id="fotos" name="fotos[]" type="file" multiple accept="image/jpeg,image/png,image/webp"
                               class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                        <p class="mt-1 text-xs text-slate-400">JPG, PNG o WEBP. Máximo {{ config('geovia.fotos.max_kb') / 1024 }} MB cada una.</p>
                        @error('fotos')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                        @foreach ($errors->get('fotos.*') as $mensajes)
                            @foreach ($mensajes as $mensaje)
                                <p class="mt-1 text-xs text-rose-600">{{ $mensaje }}</p>
                            @endforeach
                        @endforeach
                        <div id="vista-previa" class="mt-3 grid grid-cols-3 gap-2"></div>
                    </div>
                </div>
            </section>

            {{-- Mapa --}}
            <section class="gv-card lg:col-span-3">
                <div class="gv-card-header flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="gv-title">2. ¿Dónde está?</h2>
                        <p class="gv-muted-xs mt-0.5">Toca el mapa para colocar el marcador; puedes arrastrarlo para ajustarlo.</p>
                    </div>
                    <button type="button" id="usar-ubicacion"
                            class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Usar mi ubicación
                    </button>
                </div>

                <div class="p-5">
                    <div id="mapa" class="h-80 w-full rounded-lg border border-slate-200 sm:h-96" role="application" aria-label="Mapa para marcar la ubicación del daño"></div>

                    <input type="hidden" id="latitud" name="latitud" value="{{ old('latitud') }}">
                    <input type="hidden" id="longitud" name="longitud" value="{{ old('longitud') }}">

                    <p id="estado-ubicacion" class="mt-3 text-sm text-slate-500">Aún no has marcado la ubicación.</p>
                    <p id="aviso-ubicacion" class="mt-1 hidden text-xs text-amber-600"></p>
                    @error('latitud')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    @if (! $errors->has('latitud'))
                        @error('longitud')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
            </section>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                Cancelar
            </a>
            <button type="submit" id="enviar" class="gv-primary-button">Enviar reporte</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            const centro = [{{ $mapa['latitud'] }}, {{ $mapa['longitud'] }}];
            const latInput = document.getElementById('latitud');
            const lngInput = document.getElementById('longitud');
            const estado = document.getElementById('estado-ubicacion');
            const aviso = document.getElementById('aviso-ubicacion');
            const boton = document.getElementById('usar-ubicacion');

            const mapa = L.map('mapa').setView(centro, {{ $mapa['zoom'] }});
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(mapa);

            let marcador = null;

            function colocar(lat, lng, centrar) {
                lat = Number(lat.toFixed(7));
                lng = Number(lng.toFixed(7));
                latInput.value = lat;
                lngInput.value = lng;
                estado.textContent = 'Ubicación marcada: ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
                estado.classList.remove('text-slate-500');
                estado.classList.add('font-medium', 'text-emerald-700');

                if (marcador) {
                    marcador.setLatLng([lat, lng]);
                } else {
                    marcador = L.marker([lat, lng], { draggable: true }).addTo(mapa);
                    marcador.on('dragend', function () {
                        const p = marcador.getLatLng();
                        colocar(p.lat, p.lng, false);
                    });
                }
                if (centrar) mapa.setView([lat, lng], Math.max(mapa.getZoom(), 17));
            }

            mapa.on('click', function (e) { colocar(e.latlng.lat, e.latlng.lng, false); });

            // Conservar el marcador si el formulario volvió con errores.
            const latPrevia = parseFloat(latInput.value);
            const lngPrevia = parseFloat(lngInput.value);
            if (!isNaN(latPrevia) && !isNaN(lngPrevia)) colocar(latPrevia, lngPrevia, true);

            boton.addEventListener('click', function () {
                aviso.classList.add('hidden');
                if (!navigator.geolocation) {
                    aviso.textContent = 'Tu navegador no permite obtener la ubicación. Marca el punto en el mapa.';
                    aviso.classList.remove('hidden');
                    return;
                }
                boton.disabled = true;
                boton.textContent = 'Buscando…';
                navigator.geolocation.getCurrentPosition(function (pos) {
                    colocar(pos.coords.latitude, pos.coords.longitude, true);
                    boton.disabled = false;
                    boton.textContent = 'Usar mi ubicación';
                }, function () {
                    aviso.textContent = 'No pudimos obtener tu ubicación. Revisa el permiso del navegador o marca el punto en el mapa.';
                    aviso.classList.remove('hidden');
                    boton.disabled = false;
                    boton.textContent = 'Usar mi ubicación';
                }, { enableHighAccuracy: true, timeout: 10000 });
            });

            // Contador de caracteres.
            const desc = document.getElementById('descripcion');
            const contador = document.getElementById('contador');
            const actualizar = function () { contador.textContent = desc.value.length; };
            desc.addEventListener('input', actualizar);
            actualizar();

            // Vista previa de fotografías y límite de cantidad.
            const fotos = document.getElementById('fotos');
            const previa = document.getElementById('vista-previa');
            const maximo = {{ (int) $maxFotos }};
            fotos.addEventListener('change', function () {
                previa.innerHTML = '';
                if (fotos.files.length > maximo) {
                    aviso.textContent = 'Seleccionaste ' + fotos.files.length + ' fotografías; el máximo es ' + maximo + '.';
                    aviso.classList.remove('hidden');
                    fotos.value = '';
                    return;
                }
                Array.from(fotos.files).forEach(function (archivo) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(archivo);
                    img.alt = archivo.name;
                    img.className = 'h-20 w-full rounded-lg border border-slate-200 object-cover';
                    img.onload = function () { URL.revokeObjectURL(img.src); };
                    previa.appendChild(img);
                });
            });

            // Evitar envío sin ubicación y doble clic.
            document.querySelector('form[action="{{ route('reportes.store') }}"]').addEventListener('submit', function (e) {
                if (!latInput.value || !lngInput.value) {
                    e.preventDefault();
                    aviso.textContent = 'Marca la ubicación del daño en el mapa antes de enviar.';
                    aviso.classList.remove('hidden');
                    document.getElementById('mapa').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
                const enviar = document.getElementById('enviar');
                enviar.disabled = true;
                enviar.textContent = 'Enviando…';
            });
        })();
    </script>
@endpush