<?php

namespace Database\Seeders;

use App\Models\Cuadrilla;
use App\Models\Inspeccion;
use App\Models\OrdenTrabajo;
use App\Models\Reporte;
use App\Models\TipoDano;
use App\Models\User;
use App\Models\Via;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatosEntregaSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $cuentas = [
                ['Autoridad Municipal', 'autoridad.prueba@geovia.test', 'AUTORIDAD'],
                ['Inspector Vial', 'inspector.prueba@geovia.test', 'INSPECTOR'],
                ['Supervisor uno', 'supervisor1.prueba@geovia.test', 'SUPERVISOR'],
                ['Supervisor dos', 'supervisor2.prueba@geovia.test', 'SUPERVISOR'],
                ['Maria Lopez', 'vecino.prueba@geovia.test', 'VECINO'],
                ['Carlos Morales', 'vecino2.prueba@geovia.test', 'VECINO'],
                ['Ana Perez', 'vecino3.prueba@geovia.test', 'VECINO'],
            ];

            $usuarios = [];

            foreach ($cuentas as [$nombre, $correo, $rol]) {
                $usuario = User::firstOrNew(['correo' => $correo]);

                if (! $usuario->exists) {
                    $usuario->forceFill([
                        'nombre' => $nombre,
                        'rol' => $rol,
                        'contrasena' => 'GeoViaPrueba2026!',
                        'email_verified_at' => now(),
                    ])->save();
                }

                $usuarios[] = $usuario;
            }

            $vias = [];

            foreach ([
                         ['Calzada Justo Rufino Barrios', 'ALTO'],
                         ['5a Avenida', 'MEDIO'],
                         ['8a Avenida', 'ALTO'],
                         ['12a Avenida', 'MEDIO'],
                         ['20 Calle', 'BAJO'],
                     ] as $indice => [$nombre, $trafico]) {
                $vias[] = Via::firstOrCreate(
                    ['nombre_via' => $nombre],
                    [
                        'nivel_trafico' => $trafico,
                        'fecha_ultimo_mantenimiento' => today()
                            ->subDays(90 + $indice * 60),
                    ]
                );
            }

            $tipos = [];

            foreach ([
                         'Bache',
                         'Grieta',
                         'Hundimiento',
                         'Desprendimiento de pavimento',
                     ] as $nombre) {
                $tipos[] = TipoDano::firstOrCreate(
                    ['nombre' => $nombre],
                    [
                        'descripcion' => 'Tipo de dano del catalogo de demostracion.',
                        'activo' => 'S',
                    ]
                );
            }

            TipoDano::firstOrCreate(
                ['nombre' => 'Desgaste superficial'],
                [
                    'descripcion' => 'Ejemplo de tipo desactivado.',
                    'activo' => 'N',
                ]
            );

            $cuadrillas = [];

            foreach (['Norte', 'Centro', 'Sur'] as $zona) {
                $cuadrillas[] = Cuadrilla::firstOrCreate(
                    ['nombre' => 'Mantenimiento vial '.$zona],
                    [
                        'responsable' => 'Responsable de demostracion '.$zona,
                        'telefono' => null,
                    ]
                );
            }

            $estados = [
                'PENDIENTE',
                'VALIDADO',
                'RECHAZADO',
                'EN REPARACION',
                'FINALIZADO',
            ];

            for ($i = 0; $i < 10; $i++) {
                $estado = $estados[$i % 5];
                $via = $vias[$i % 5];
                $tipo = $tipos[$i % 4];
                $fecha = today()->subDays(20 - $i);

                $reporte = Reporte::firstOrCreate(
                    [
                        'descripcion' => 'DEMO '.($i + 1).': '
                            .$tipo->nombre.' en '.$via->nombre_via,
                        'usuario_id_usuario' => $usuarios[4 + $i % 3]->id_usuario,
                    ],
                    [
                        'via_id_via' => $via->id_via,
                        'tipo_dano_id_tipo_dano' => $tipo->id_tipo_dano,
                        'latitud' => 15.7277 + ($i % 5) * 0.001,
                        'longitud' => -88.5944 + intdiv($i, 5) * 0.001,
                        'fecha_reporte' => $fecha,
                        'estado' => $estado,
                        'prioridad' => 0,
                    ]
                );

                if (! $reporte->wasRecentlyCreated || $estado === 'PENDIENTE') {
                    continue;
                }

                Inspeccion::create([
                    'reporte_id_reporte' => $reporte->id_reporte,
                    'usuario_id_usuario' => $usuarios[1]->id_usuario,
                    'fecha_inspeccion' => $fecha->copy()->addDay(),
                    'resultado' => $estado === 'RECHAZADO'
                        ? 'RECHAZADO'
                        : 'APROBADO',
                    'observaciones' => 'Resultado simulado para la entrega: '.$estado,
                ]);

                $necesitaOrden = in_array(
                        $estado,
                        ['EN REPARACION', 'FINALIZADO'],
                        true
                    ) || ($estado === 'VALIDADO' && $i >= 5);

                if (! $necesitaOrden) {
                    continue;
                }

                $finalizada = $estado === 'FINALIZADO';

                OrdenTrabajo::create([
                    'reporte_id_reporte' => $reporte->id_reporte,
                    'cuadrilla_id_cuadrilla' => $cuadrillas[$i % 3]->id_cuadrilla,
                    'usuario_id_usuario' => $usuarios[2 + $i % 2]->id_usuario,
                    'fecha_asignacion' => $fecha->copy()->addDays(2),
                    'fecha_finalizacion' => $finalizada
                        ? $fecha->copy()->addDays(5)
                        : null,
                    'estado' => $finalizada
                        ? 'FINALIZADA'
                        : ($estado === 'VALIDADO' ? 'PENDIENTE' : 'EN PROCESO'),
                    'avance' => $finalizada
                        ? 100
                        : ($estado === 'VALIDADO' ? 0 : 45),
                    'observaciones' => 'Trabajo simulado para la demostracion.',
                ]);
            }
        });
    }
}
