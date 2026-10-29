<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait AutorizaPorRol
{
    /** Roles que pueden consultar (solo lectura). */
    protected function rolesConsulta(): array
    {
        return ['AUTORIDAD', 'INSPECTOR', 'SUPERVISOR'];
    }

    /** Roles que pueden crear, editar y eliminar. */
    protected function rolesGestion(): array
    {
        return ['AUTORIDAD'];
    }

    protected function exigirConsulta(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->rol, $this->rolesConsulta(), true),
            403,
            'No tienes permiso para consultar esta sección.'
        );
    }

    protected function exigirGestion(Request $request): void
    {
        abort_unless(
            $this->puedeGestionar($request),
            403,
            'Solo la autoridad municipal puede realizar esta acción.'
        );
    }

    protected function puedeGestionar(Request $request): bool
    {
        return in_array($request->user()?->rol, $this->rolesGestion(), true);
    }
}