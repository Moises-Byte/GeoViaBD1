<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LimpiarEntregaSeeder extends Seeder
{
    public function run(): void
    {
        if (! $this->command || ! $this->command->confirm(
                'Se borraran TODOS los usuarios y registros actuales de GeoVia y se cargaran datos de entrega. Continuar?',
                false
            )) {
            return;
        }

        DB::transaction(function () {
            foreach ([
                         'fotografia',
                         'inspeccion',
                         'orden_trabajo',
                         'reporte',
                         'cuadrilla',
                         'via',
                         'tipo_dano',
                         'password_reset_tokens',
                         'usuario',
                     ] as $tabla) {
                DB::table($tabla)->delete();
            }

            $this->call(DatosEntregaSeeder::class);
        });
    }
}
