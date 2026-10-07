@php
    $paths = [
        'dashboard' => 'M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z',
        'reports' => 'M7 3h10a2 2 0 0 1 2 2v16l-7-3-7 3V5a2 2 0 0 1 2-2Zm2 5h6M9 12h6',
        'roads' => 'M8 21 11 3h2l3 18M6 21h12M12 7v3M12 14v3',
        'inspection' => 'M9 11l2 2 4-5M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z',
        'orders' => 'M7 7h10M7 12h10M7 17h6M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z',
        'crews' => 'M16 11a4 4 0 1 0-8 0M5 21a7 7 0 0 1 14 0M19 8v4M21 10h-4',
    ];
@endphp

<svg class="h-5 w-5 flex-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
    <path stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="{{ $paths[$icon] ?? $paths['dashboard'] }}"/>
</svg>
