<x-guest-layout>
    <h1 class="gv-auth-heading">Recuperar contraseña</h1>

    @if (session('status'))
        <p role="status" class="mb-4 text-sm font-medium text-emerald-700">
            Solicitud de recuperación procesada.
        </p>
    @endif

    <form method="POST" action="{{ route('password.email') }}"
          class="gv-auth-form">
        @csrf

        <div>
            <x-input-label for="email" value="Correo electrónico" />

            <input
                id="email"
                name="email"
                type="email"
                class="gv-auth-input"
                value="{{ old('email') }}"
                maxlength="100"
                required
                autofocus
                autocomplete="email"
            >

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="gv-auth-submit">
            Solicitar enlace
        </button>
    </form>

    <p class="gv-auth-footer">
        <a href="{{ route('login') }}" class="gv-auth-link">
            Volver a iniciar sesión
        </a>
    </p>
</x-guest-layout>
