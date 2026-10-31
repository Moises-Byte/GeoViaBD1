<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Database\QueryException;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $usuario = $request->user();

        $usuario->fill([
            'nombre' => $datos['name'],
            'correo' => $datos['email'],
        ]);

        if ($usuario->isDirty('correo')) {
            $usuario->email_verified_at = null;
        }

        $usuario->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $usuario = $request->user();

        $mensaje = 'No puedes eliminar tu cuenta porque tiene reportes, '
            .'inspecciones u órdenes de trabajo asociados.';

        if (
            $usuario->reportes()->exists()
            || $usuario->inspecciones()->exists()
            || $usuario->ordenesTrabajo()->exists()
        ) {
            return back()->withErrors([
                'cuenta' => $mensaje,
            ], 'userDeletion');
        }

        try {
            $usuario->delete();
        } catch (QueryException $e) {
            // Oracle también protege si se asocian registros durante el borrado.
            if (
                (int) ($e->errorInfo[1] ?? 0) !== 2292
                && ! str_contains($e->getMessage(), 'ORA-02292')
            ) {
                throw $e;
            }

            return back()->withErrors([
                'cuenta' => $mensaje,
            ], 'userDeletion');
        }

        Auth::guard('web')->logoutCurrentDevice();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('login')
            ->with('status', 'Tu cuenta fue eliminada correctamente.');
    }
}
