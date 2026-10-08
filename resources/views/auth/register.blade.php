<x-guest-layout>
    <h1 class="gv-auth-heading">Crear cuenta</h1>

    <form method="POST" action="{{ route('register') }}" class="gv-auth-form">
        @csrf

        <div>
            <x-input-label for="name" value="Nombre completo" />
            <input
                id="name"
                name="name"
                type="text"
                class="gv-auth-input"
                value="{{ old('name') }}"
                maxlength="100"
                required
                autofocus
                autocomplete="name"
            >
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

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
                autocomplete="username"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Contraseña" />
            <input id="password" name="password" type="password"
                   class="gv-auth-input" minlength="8"
                   required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />
            <input id="password_confirmation" name="password_confirmation"
                   type="password" class="gv-auth-input" minlength="8"
                   required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="gv-auth-submit">Crear cuenta</button>
    </form>

    <p class="gv-auth-footer">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="gv-auth-link">Inicia sesión</a>
    </p>
</x-guest-layout>
