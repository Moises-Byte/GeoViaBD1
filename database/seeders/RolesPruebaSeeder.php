<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RolesPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $cuentas = [
            ['Autoridad prueba', 'autoridad.prueba@geovia.test', 'AUTORIDAD'],
            ['Inspector prueba', 'inspector.prueba@geovia.test', 'INSPECTOR'],
            ['Supervisor uno', 'supervisor1.prueba@geovia.test', 'SUPERVISOR'],
            ['Supervisor dos', 'supervisor2.prueba@geovia.test', 'SUPERVISOR'],
            ['Vecino prueba', 'vecino.prueba@geovia.test', 'VECINO'],
        ];

        foreach ($cuentas as [$nombre, $correo, $rol]) {
            $usuario = User::firstOrNew([
                'correo' => $correo,
            ]);

            $usuario->forceFill([
                'nombre' => $nombre,
                'rol' => $rol,
                'contrasena' => 'GeoViaPrueba2026!',
                'email_verified_at' => now(),
                'remember_token' => null,
            ])->save();
        }
    }
}
