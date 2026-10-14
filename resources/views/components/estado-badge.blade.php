@props(['estado'])

@php
    $estilos = [
        'PENDIENTE' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'VALIDADO' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'EN REPARACION' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
        'FINALIZADO' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'RECHAZADO' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
    ];

    $etiquetas = [
        'PENDIENTE' => 'Pendiente de revisión',
        'VALIDADO' => 'Validado',
        'EN REPARACION' => 'En reparación',
        'FINALIZADO' => 'Finalizado',
        'RECHAZADO' => 'Rechazado',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset '.($estilos[$estado] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20')]) }}>
    {{ $etiquetas[$estado] ?? $estado }}
</span>