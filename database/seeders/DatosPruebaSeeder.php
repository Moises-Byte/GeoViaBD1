<?php

namespace Database\Seeders;

use App\Models\Reporte;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Vías con diferentes niveles de tráfico.
            DB::table('via')->updateOrInsert(
                ['nombre_via' => 'Avenida de prueba'],
                [
                    'nivel_trafico' => 'ALTO',
                    'fecha_ultimo_mantenimiento' => '2025-01-01',
                ]
            );

            DB::table('via')->updateOrInsert(
                ['nombre_via' => 'Calle de prueba'],
                [
                    'nivel_trafico' => 'BAJO',
                    'fecha_ultimo_mantenimiento' => '2026-09-01',
                ]
            );

            // Tipos de daño.
            DB::table('tipo_dano')->updateOrInsert(
                ['nombre' => 'Bache'],
                [
                    'descripcion' => 'Hueco en la superficie de la vía',
                    'activo' => 'S',
                ]
            );

            DB::table('tipo_dano')->updateOrInsert(
                ['nombre' => 'Grieta'],
                [
                    'descripcion' => 'Fisura en la superficie de la vía',
                    'activo' => 'S',
                ]
            );

            // Cuadrillas.
            DB::table('cuadrilla')->updateOrInsert(
                ['nombre' => 'Cuadrilla de prueba 1'],
                [
                    'responsable' => 'Responsable de prueba 1',
                    'telefono' => null,
                ]
            );

            DB::table('cuadrilla')->updateOrInsert(
                ['nombre' => 'Cuadrilla de prueba 2'],
                [
                    'responsable' => 'Responsable de prueba 2',
                    'telefono' => null,
                ]
            );

            // Usuarios: conservar las cuentas si ya existen.
            $usuarios = [
                [
                    'nombre' => 'Vecino de prueba',
                    'correo' => 'vecino.local@example.com',
                    'rol' => 'VECINO',
                ],
                [
                    'nombre' => 'Supervisor de prueba',
                    'correo' => 'supervisor.local@example.com',
                    'rol' => 'SUPERVISOR',
                ],
            ];

            foreach ($usuarios as $usuario) {
                $existe = DB::table('usuario')
                    ->where('correo', $usuario['correo'])
                    ->exists();

                if (!$existe) {
                    DB::table('usuario')->insert([
                        ...$usuario,
                        'contrasena' => Hash::make('GeoViaPrueba2026!'),
                        'email_verified_at' => now(),
                    ]);
                }
            }

            // Obtener los IDs generados por Oracle.
            $vecinoId = DB::table('usuario')
                ->where('correo', 'vecino.local@example.com')
                ->value('id_usuario');

            $bacheId = DB::table('tipo_dano')
                ->where('nombre', 'Bache')
                ->value('id_tipo_dano');

            // Reportes validados para probar prioridades y órdenes.
            $reportes = [
                'Avenida de prueba' => 'Bache de prueba en avenida',
                'Calle de prueba' => 'Bache de prueba en calle',
            ];

            foreach ($reportes as $nombreVia => $descripcion) {
                $viaId = DB::table('via')
                    ->where('nombre_via', $nombreVia)
                    ->value('id_via');

                Reporte::firstOrCreate(
                    [
                        'usuario_id_usuario' => $vecinoId,
                        'via_id_via' => $viaId,
                        'descripcion' => $descripcion,
                    ],
                    [
                        'tipo_dano_id_tipo_dano' => $bacheId,
                        'latitud' => 14.63,
                        'longitud' => -90.51,
                        'fecha_reporte' => now(),
                        'estado' => 'VALIDADO',
                        'prioridad' => 0,
                    ]
                );
            }
        });
    }
}