<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GeoVia') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @if (request()->routeIs('login'))
            <style>
                .gv-login-map {
                    min-height: 100vh;
                    min-height: 100svh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 16px;
                    background: linear-gradient(rgba(255,255,255,.22), rgba(255,255,255,.22)),
                        url('{{ asset('images/puerto-barrios-auth.png') }}') center / cover no-repeat;
                }
                .gv-login-card {
                    width: 100%; max-width: 460px; padding: 24px 32px;
                    background: rgba(255,255,255,.98); border: 1px solid #e2e8f0;
                    border-radius: 24px; box-shadow: 0 18px 60px rgba(15,23,42,.15);
                }
                .gv-login-brand { display: block; text-align: center; color: #0f2749; font-size: 32px; font-weight: 700; margin-bottom: 12px; }
                .gv-login-brand svg { width: 48px; height: 48px; }
                .gv-login-card .gv-auth-heading { text-align: center; color: #0f2749; }
                .gv-login-subtitle { text-align: center; color: #64748b; font-size: 14px; margin: -8px 0 16px; }
                .gv-login-card .gv-auth-form > :not([hidden]) ~ :not([hidden]) { margin-top: 14px; }
                .gv-login-card .gv-auth-footer { margin-top: 16px; padding-top: 14px; }
                @media (max-width: 480px) {
                    .gv-login-map { padding: 12px; }
                    .gv-login-card { padding: 20px 22px; border-radius: 20px; }
                }
                @media (max-height: 650px) {
                    .gv-login-map { padding: 8px 12px; }
                    .gv-login-card { padding: 16px 24px; }
                    .gv-login-brand { font-size: 28px; margin-bottom: 6px; }
                    .gv-login-brand svg { width: 36px; height: 36px; }
                    .gv-login-card .gv-auth-heading { margin-bottom: 12px; }
                    .gv-login-subtitle { margin-bottom: 12px; }
                    .gv-login-card .gv-auth-form > :not([hidden]) ~ :not([hidden]) { margin-top: 10px; }
                    .gv-login-card .gv-auth-footer { margin-top: 12px; padding-top: 10px; }
                }
            </style>
        @endif
    </head>
    <body class="font-sans text-gray-900 antialiased">
        @if (request()->routeIs('login'))
            <main class="gv-login-map">
                <div class="gv-login-card">
                    <a href="/" class="gv-login-brand" aria-label="GeoVía, inicio">
                        <svg viewBox="0 0 64 64" width="64" height="64" aria-hidden="true" style="margin: 0 auto 8px">
                            <path fill="#059669" d="M32 3C18 3 9 13 9 26c0 17 23 35 23 35s23-18 23-35C55 13 46 3 32 3Z"/>
                            <circle cx="32" cy="25" r="13" fill="white"/>
                            <path d="M21 58 30 20h5l10 38Z" fill="#0f2749"/>
                            <path d="M32 26v4m0 5v5m0 5v6" stroke="white" stroke-width="2"/>
                        </svg>
                        GeoVía
                    </a>
                    {{ $slot }}
                </div>
            </main>
        @else
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <span class="gv-auth-brand">GeoVia</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
        @endif
    </body>
</html>
