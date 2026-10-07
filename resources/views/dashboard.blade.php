@extends('layouts.app')

@php
    $title = 'Dashboard municipal';
@endphp

@section('actions')
    <a href="#"
       class="hidden sm:inline-flex gv-primary-button">
        Nuevo reporte
    </a>
@endsection

@section('slot')
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="gv-card gv-card-body">
                <p class="text-sm font-medium text-slate-500">Reportes pendientes</p>
                <p class="gv-stat-number">--</p>
                <p class="mt-3 text-xs text-slate-400">Esperando validacion municipal</p>
            </article>

            <article class="gv-card gv-card-body">
                <p class="text-sm font-medium text-slate-500">Reportes validados</p>
                <p class="gv-stat-number">--</p>
                <p class="mt-3 text-xs text-slate-400">Listos para generar orden</p>
            </article>

            <article class="gv-card gv-card-body">
                <p class="text-sm font-medium text-slate-500">Ordenes activas</p>
                <p class="gv-stat-number">--</p>
                <p class="mt-3 text-xs text-slate-400">Cuadrillas trabajando en campo</p>
            </article>

            <article class="gv-card gv-card-body">
                <p class="text-sm font-medium text-slate-500">Prioridad promedio</p>
                <p class="gv-stat-number">--</p>
                <p class="mt-3 text-xs text-slate-400">Pendiente de calculo</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-3">
            <div class="gv-card xl:col-span-2">
                <div class="gv-card-header">
                    <h2 class="gv-title">Reportes con mayor prioridad</h2>
                    <p class="gv-muted-xs mt-0.5">Vista preparada para mostrar los reportes mas urgentes.</p>
                </div>

                <div class="divide-y divide-slate-100">
                    <div class="px-5 py-4">
                        <p class="font-semibold text-slate-800">Reporte vial pendiente</p>
                        <p class="mt-1 gv-muted">Via afectada · Tipo de dano · Nivel de trafico</p>
                    </div>

                    <div class="px-5 py-4">
                        <p class="font-semibold text-slate-800">Reporte vial validado</p>
                        <p class="mt-1 gv-muted">Pendiente de asignar a orden de trabajo</p>
                    </div>

                    <div class="px-5 py-4">
                        <p class="font-semibold text-slate-800">Reporte en reparacion</p>
                        <p class="mt-1 gv-muted">Seguimiento de avance municipal</p>
                    </div>
                </div>
            </div>

            <div class="gv-card gv-card-body">
                <h2 class="gv-title">Estado de reportes</h2>
                <p class="gv-muted-xs mt-0.5">Resumen por flujo de atencion.</p>

                <div class="mt-5 space-y-4">
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Pendiente</span>
                            <span class="font-semibold text-slate-900">--</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-100"></div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Validado</span>
                            <span class="font-semibold text-slate-900">--</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-100"></div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">En reparacion</span>
                            <span class="font-semibold text-slate-900">--</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-100"></div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Finalizado</span>
                            <span class="font-semibold text-slate-900">--</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-100"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="gv-card">
            <div class="gv-card-header">
                <h2 class="gv-title">Ordenes de trabajo recientes</h2>
                <p class="gv-muted-xs mt-0.5">Tabla preparada para cuadrillas, supervisores y avance.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="gv-table">
                    <thead class="gv-table-head">
                    <tr>
                        <th class="gv-table-cell">Orden</th>
                        <th class="gv-table-cell">Reporte</th>
                        <th class="gv-table-cell">Cuadrilla</th>
                        <th class="gv-table-cell">Supervisor</th>
                        <th class="gv-table-cell">Estado</th>
                        <th class="gv-table-cell">Avance</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm text-slate-500">
                            Aun no hay ordenes registradas.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
