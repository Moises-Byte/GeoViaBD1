<section>
    <header>
        <h2 class="gv-title text-lg">Cambiar contraseña</h2>
    </header>

    <form method="POST" action="{{ route('password.update') }}"
          class="mt-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="update_password_current_password"
                           value="Contraseña actual" />

            <input id="update_password_current_password"
                   name="current_password"
                   type="password"
                   class="gv-auth-input"
                   required
                   autocomplete="current-password">

            <x-input-error
                :messages="$errors->updatePassword->get('current_password')"
                class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password"
                           value="Nueva contraseña" />

            <input id="update_password_password"
                   name="password"
                   type="password"
                   class="gv-auth-input"
                   minlength="8"
                   required
                   autocomplete="new-password">

            <x-input-error
                :messages="$errors->updatePassword->get('password')"
                class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation"
                           value="Confirmar nueva contraseña" />

            <input id="update_password_password_confirmation"
                   name="password_confirmation"
                   type="password"
                   class="gv-auth-input"
                   minlength="8"
                   required
                   autocomplete="new-password">

            <x-input-error
                :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <button type="submit" class="gv-primary-button">
                Actualizar contraseña
            </button>

            @if (session('status') === 'password-updated')
                <p role="status" class="text-sm font-medium text-emerald-700">
                    Contraseña actualizada.
                </p>
            @endif
        </div>
    </form>
</section>
