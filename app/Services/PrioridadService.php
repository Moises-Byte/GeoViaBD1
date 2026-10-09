<?php

namespace App\Services;

use App\Models\Reporte;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PrioridadService
{
    public function recalcular(): int
    {
        return DB::transaction(function () {
            $reportes = Reporte::with('via')
                ->where('estado', 'VALIDADO')
                ->get();

            $cantidades = Reporte::query()
                ->where('estado', '<>', 'RECHAZADO')
                ->select('via_id_via')
                ->selectRaw('COUNT(*) AS cantidad')
                ->groupBy('via_id_via')
                ->pluck('cantidad', 'via_id_via');

            foreach ($reportes as $reporte) {
                $via = $reporte->via;

                if (! $via) {
                    throw new RuntimeException(
                        "El reporte {$reporte->id_reporte} no tiene vía."
                    );
                }

                $cantidad = (int) ($cantidades[$reporte->via_id_via] ?? 0);

                // Cantidad de reportes: máximo 40 puntos, al alcanzar 10.
                $puntosReportes = min($cantidad, 10) * 4;

                // Sin fecha conocida, no se suma antigüedad.
                $diasSinMantenimiento = 0;

                if ($via->fecha_ultimo_mantenimiento !== null) {
                    $diasSinMantenimiento = max(
                        0,
                        (int) $via->fecha_ultimo_mantenimiento
                            ->copy()
                            ->startOfDay()
                            ->diffInDays(today(config('app.timezone')), false)
                    );
                }

                // Antigüedad: máximo 30 puntos, al alcanzar 365 días.
                $puntosTiempo = (min($diasSinMantenimiento, 365) / 365) * 30;

                $puntosTrafico = match ($via->nivel_trafico) {
                    'ALTO' => 30,
                    'MEDIO' => 20,
                    'BAJO' => 10,
                    default => throw new RuntimeException(
                        "Tráfico inválido en la vía {$via->id_via}."
                    ),
                };

                $reporte->prioridad = (int) round(
                    $puntosReportes + $puntosTiempo + $puntosTrafico
                );

                $reporte->save();
            }

            return $reportes->count();
        });
    }
}
