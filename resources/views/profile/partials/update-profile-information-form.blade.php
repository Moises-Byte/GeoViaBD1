<section>
    <header>
        <h2 class="gv-title text-lg">Información personal</h2>
    </header>

    <form id="send-verification" method="POST"
          action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="POST" action="{{ route('profile.update') }}"
          class="mt-6 space-y-5">
        @csrf
        @method('PATCH')

        <div>
            <x-input-label for="name" value="Nombre completo" />
            <input id="name" name="name" type="text"
                   class="gv-auth-input"
                   value="{{ old('name', $user->name) }}"
                   maxlength="100" required autocomplete="name">
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <input id="email" name="email" type="email"
                   class="gv-auth-input"
                   value="{{ old('email', $user->email) }}"
                   maxlength="100" required autocomplete="username">
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 space-y-2">
                    <p class="text-sm text-amber-800">
                        Tu correo electrónico todavía no está verificado.
                    </p>

                    <button type="submit" form="send-verification"
                            class="gv-auth-link text-sm">
                        Reenviar correo de verificación
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p role="status" class="text-sm text-emerald-700">
                            Se envió un nuevo enlace de verificación.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <button type="submit" class="gv-primary-button">
                Guardar cambios
            </button>

            @if (session('status') === 'profile-updated')
                <p role="status" class="text-sm font-medium text-emerald-700">
                    Cambios guardados.
                </p>
            @endif
        </div>
    </form>
</section>
