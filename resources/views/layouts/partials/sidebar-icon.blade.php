@php
    $paths = [
        'dashboard' => 'M3 10 12 3l9 7M5 9v12h5v-7h4v7h5V9',
        'priority' => 'M9 5H6a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3M9 3h6v4H9zM8 12h1m3 0h4M8 17h1m3 0h4',
        'statistics' => 'M3 14h4v7H3zM10 9h4v12h-4zM17 3h4v18h-4z',
        'reports' => 'M5 3h9l5 5v13H5zM14 3v6h5M9 13h6M9 17h6',
        'roads' => 'M4 21 8 3M20 21 16 3M12 3v3m0 4v4m0 4v3',
        'inspection' => 'M9 11l2 2 4-5M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z',
        'orders' => 'M7 7h10M7 12h10M7 17h6M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z',
        'crews' => 'M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6 21v-3a6 6 0 0 1 12 0v3ZM3 9a2 2 0 1 0 0 4m18-4a2 2 0 1 1 0 4M2 21v-3a4 4 0 0 1 2-3m18 6v-3a4 4 0 0 0-2-3',
    ];
@endphp

<svg class="h-5 w-5 flex-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
    <path stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.7"
          d="{{ $paths[$icon] ?? $paths['dashboard'] }}"/>
</svg>
