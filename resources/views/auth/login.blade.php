<x-guest-layout>
    <h1 class="gv-auth-heading">Iniciar sesión</h1>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="gv-auth-form">
        @csrf

        <div>
            <x-input-label for="email" value="Correo electrónico" />

            <input
                id="email"
                name="email"
                type="email"
                class="gv-auth-input"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
            >

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Contraseña" />

            <input
                id="password"
                name="password"
                type="password"
                class="gv-auth-input"
                required
                autocomplete="current-password"
            >

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input
                name="remember"
                type="checkbox"
                class="rounded border-gray-300 text-emerald-700 focus:ring-emerald-600"
                @checked(old('remember'))
            >
            Recordarme
        </label>

        <button type="submit" class="gv-auth-submit">
            Iniciar sesión
        </button>
    </form>

    <p class="gv-auth-footer">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}" class="gv-auth-link">Regístrate</a>
    </p>
</x-guest-layout>
