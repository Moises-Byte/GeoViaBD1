<section class="space-y-5">
    <header>
        <h2 class="text-lg font-semibold text-red-700">Eliminar cuenta</h2>

        <p class="mt-2 text-sm text-slate-600">
            La eliminación de tu cuenta es permanente.
        </p>
    </header>

    <x-danger-button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        Eliminar mi cuenta
    </x-danger-button>

    <x-modal name="confirm-user-deletion"
             :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('profile.destroy') }}"
              class="p-6">
            @csrf
            @method('DELETE')

            <h2 class="text-lg font-semibold text-slate-900">
                ¿Quieres eliminar tu cuenta?
            </h2>

            <p class="mt-2 text-sm text-slate-600">
                Esta acción no se puede deshacer. Introduce tu contraseña
                actual para confirmar.
            </p>

            <x-input-error
                :messages="$errors->userDeletion->get('cuenta')"
                class="mt-4" />

            <div class="mt-5">
                <x-input-label for="delete_account_password"
                               value="Contraseña actual" />

                <input id="delete_account_password"
                       name="password"
                       type="password"
                       class="gv-auth-input"
                       required
                       autocomplete="current-password">

                <x-input-error
                    :messages="$errors->userDeletion->get('password')"
                    class="mt-2" />
            </div>

            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <x-secondary-button type="button"
                                    x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>

                <x-danger-button type="submit">
                    Eliminar mi cuenta
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
