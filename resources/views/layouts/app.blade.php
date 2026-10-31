@php
    $title = $title ?? 'Dashboard';

    $navItems = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['route' => 'mapa.publico', 'label' => 'Mapa público', 'icon' => 'map'],
        ['route' => 'prioridades.index', 'label' => 'Prioridades', 'icon' => 'priority', 'roles' => ['AUTORIDAD', 'SUPERVISOR']],
        ['route' => 'estadisticas.index', 'label' => 'Estadísticas', 'icon' => 'statistics', 'roles' => ['AUTORIDAD', 'SUPERVISOR']],
        ['route' => 'reportes.index', 'label' => 'Reportes', 'icon' => 'reports'],
        ['route' => 'vias.index', 'label' => 'Vías', 'icon' => 'roads', 'roles' => ['AUTORIDAD', 'INSPECTOR', 'SUPERVISOR']],
        ['route' => 'inspecciones.index', 'label' => 'Inspecciones', 'icon' => 'inspection', 'roles' => ['AUTORIDAD', 'INSPECTOR', 'SUPERVISOR']],
        ['route' => 'ordenes.index', 'label' => 'Órdenes', 'icon' => 'orders', 'roles' => ['AUTORIDAD', 'SUPERVISOR']],
        ['route' => 'cuadrillas.index', 'label' => 'Cuadrillas', 'icon' => 'crews', 'roles' => ['AUTORIDAD', 'INSPECTOR', 'SUPERVISOR']],
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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/geovia-panel.css') }}?v={{ filemtime(public_path('css/geovia-panel.css')) }}">

    @stack('head')
</head>

<body class="gv-panel {{ request()->routeIs('dashboard') ? 'gv-dashboard-page' : '' }} font-sans antialiased bg-slate-100 text-slate-900">
<div class="flex min-h-screen">
    <aside
        id="sidebar"
        class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full border-r border-slate-800 bg-slate-950 text-slate-300 transition-transform duration-200 md:static md:translate-x-0"
    >
        <div class="flex h-16 items-center gap-3 border-b border-slate-800 px-6">
            <div class="gv-sidebar-logo flex h-10 w-10 shrink-0 items-center justify-center">
                <svg viewBox="0 0 64 64" width="40" height="40" aria-hidden="true">
                    <defs>
                        <linearGradient id="sidebar-pin" x1="0" y1="0" x2="1" y2="1">
                            <stop stop-color="#18b787"/>
                            <stop offset="1" stop-color="#07865e"/>
                        </linearGradient>
                    </defs>
                    <path fill="url(#sidebar-pin)" d="M30 3C18 3 10 12 10 23c0 13 20 31 20 31s20-18 20-31C50 12 42 3 30 3Z"/>
                    <circle cx="30" cy="22" r="8" fill="#e0f8ed"/>
                    <path d="M17 61c13-12 28-20 24-33 15 12 14 23 11 33Z" fill="#102c4d" stroke="#d2f2e8" stroke-width="1.3"/>
                    <path d="M35 58c12-11 14-19 9-27" fill="none" stroke="white" stroke-width="2" stroke-dasharray="5 4"/>
                </svg>
            </div>

            <div>
                <p class="text-sm font-semibold text-white">GeoVía</p>
                <p class="text-xs text-slate-500">Gestión vial municipal</p>
            </div>
        </div>

        <nav class="space-y-1 px-3 py-5">
            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-widest text-slate-500">
                Menú principal
            </p>

            @foreach ($navItems as $item)
                @continue(isset($item['roles']) && ! in_array(Auth::user()?->rol, $item['roles'], true))

                @php
                    $exists = Route::has($item['route']);
                    $isActive = $exists
                        ? request()->routeIs(str_replace('.index', '.*', $item['route']))
                        : false;
                    $href = $exists ? route($item['route']) : '#';
                @endphp

                <a
                    href="{{ $href }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ $isActive ? 'bg-emerald-400/15 text-emerald-300' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}"
                >
                    @include('layouts.partials.sidebar-icon', ['icon' => $item['icon']])
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="absolute inset-x-0 bottom-0 border-t border-slate-800 p-4">
            <a
                href="{{ route('profile.edit') }}"
                class="block rounded-lg bg-slate-900 p-3 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
                <p class="break-words text-sm font-medium text-white">
                    {{ Auth::user()->name ?? 'Usuario municipal' }}
                </p>

                <p class="mt-1 truncate text-xs text-slate-400">
                    {{ Auth::user()->email ?? 'sesion@geovia.local' }}
                </p>

                <span class="mt-2 block text-xs font-semibold text-emerald-300">
                        Mi perfil
                    </span>
            </a>
        </div>
    </aside>

    <div
        id="sidebar-overlay"
        class="fixed inset-0 z-30 hidden bg-slate-950/60 md:hidden"
        onclick="toggleSidebar()"
    ></div>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="gv-panel-header sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 md:px-6">
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    class="rounded-lg border border-slate-200 p-2 text-slate-600 md:hidden"
                    onclick="toggleSidebar()"
                    aria-label="Abrir menú"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button>

                <div>
                    <h1 class="text-base font-semibold text-slate-900">
                        {{ $title }}
                    </h1>

                    <p class="hidden text-xs text-slate-500 sm:block">
                        {{ request()->routeIs('dashboard') ? 'Seguimiento de reportes y reparaciones' : 'Reportes, prioridades y órdenes de trabajo' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @yield('actions')

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    >
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

@stack('scripts')
</body>
</html>
