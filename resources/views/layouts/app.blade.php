@php
    $title = $title ?? 'Dashboard';

    $navItems = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['route' => 'prioridades.index', 'label' => 'Prioridades', 'icon' => 'reports', 'roles' => ['AUTORIDAD', 'SUPERVISOR']],
        ['route' => 'reportes.index', 'label' => 'Reportes', 'icon' => 'reports'],
        ['route' => 'vias.index', 'label' => 'Vias', 'icon' => 'roads'],
        ['route' => 'inspecciones.index', 'label' => 'Inspecciones', 'icon' => 'inspection'],
        ['route' => 'ordenes.index', 'label' => 'Órdenes', 'icon' => 'orders', 'roles' => ['AUTORIDAD', 'SUPERVISOR']],
        ['route' => 'cuadrillas.index', 'label' => 'Cuadrillas', 'icon' => 'crews'],
    ];
@endphp

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} | {{ config('app.name', 'GeoVia') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-slate-100 text-slate-900">
<div class="flex min-h-screen">
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full border-r border-slate-800 bg-slate-950 text-slate-300 transition-transform duration-200 md:static md:translate-x-0">
        <div class="flex h-16 items-center gap-3 border-b border-slate-800 px-6">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500 text-sm font-bold text-slate-950">
                GV
            </div>

            <div>
                <p class="text-sm font-semibold text-white">GeoVia</p>
                <p class="text-xs text-slate-500">Gestion vial municipal</p>
            </div>
        </div>

        <nav class="space-y-1 px-3 py-5">
            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">
                Operacion
            </p>

            @foreach ($navItems as $item)
                @continue(isset($item['roles']) && ! in_array(Auth::user()?->rol, $item['roles'], true))
                @php
                    $exists = Route::has($item['route']);
                    $isActive = $exists ? request()->routeIs(str_replace('.index', '.*', $item['route'])) : false;
                    $href = $exists ? route($item['route']) : '#';
                @endphp

                <a href="{{ $href }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ $isActive ? 'bg-emerald-400/15 text-emerald-300' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                    @include('layouts.partials.sidebar-icon', ['icon' => $item['icon']])
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="absolute inset-x-0 bottom-0 border-t border-slate-800 p-4">
            <div class="rounded-lg bg-slate-900 p-3">
                <p class="text-sm font-medium text-white">
                    {{ Auth::user()->name ?? 'Usuario municipal' }}
                </p>

                <p class="mt-1 truncate text-xs text-slate-500">
                    {{ Auth::user()->email ?? 'sesion@geovia.local' }}
                </p>
            </div>
        </div>
    </aside>

    <div id="sidebar-overlay"
         class="fixed inset-0 z-30 hidden bg-slate-950/60 md:hidden"
         onclick="toggleSidebar()">
    </div>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 md:px-6">
            <div class="flex items-center gap-3">
                <button type="button"
                        class="rounded-lg border border-slate-200 p-2 text-slate-600 md:hidden"
                        onclick="toggleSidebar()"
                        aria-label="Abrir menu">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div>
                    <h1 class="text-base font-semibold text-slate-900">
                        {{ $title }}
                    </h1>

                    <p class="hidden text-xs text-slate-500 sm:block">
                        Reportes, prioridades y ordenes de trabajo
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @yield('actions')

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                            class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Salir
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-4 md:p-6 lg:p-8">
            @yield('slot')
            {{ $slot ?? '' }}
        </main>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>
</body>
</html>
