
<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MapaPublicoController extends Controller
{
    /** Estados que se consideran alertas activas visibles para toda la comunidad. */
    private const ESTADOS_VISIBLES = [
        'PENDIENTE',
        'VALIDADO',
        'EN REPARACION',
    ];

    public function index(): View
    {
        return view('mapa.index', [
            'mapa' => config('geovia.mapa'),
            'urlReportes' => route('mapa.reportes'),
        ]);
    }

    /** Devuelve únicamente los campos necesarios para las alertas públicas. */
    public function reportes(): JsonResponse
    {
        $reportes = Reporte::query()
            ->with([
                'via:id_via,nombre_via',
                'tipoDano:id_tipo_dano,nombre',
                'fotografias:id_foto,reporte_id_reporte,ruta_foto',
            ])
            ->whereIn('estado', self::ESTADOS_VISIBLES)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->orderByDesc('fecha_reporte')
            ->orderByDesc('id_reporte')
            ->get()
            ->map(function (Reporte $reporte): array {
                $foto = $reporte->fotografias->first();

                return [
                    'id' => (int) $reporte->id_reporte,
                    'lat' => (float) $reporte->latitud,
                    'lng' => (float) $reporte->longitud,
                    'tipo' => $reporte->tipoDano?->nombre
                        ?? 'Daño vial',
                    'via' => $reporte->via?->nombre_via
                        ?? 'Vía sin nombre',
                    'estado' => $reporte->estado,
                    'descripcion' => $reporte->descripcion,
                    'fecha' => $reporte->fecha_reporte?->format('Y-m-d'),
                    'foto' => $foto
                        ? Storage::disk('public')->url($foto->ruta_foto)
                        : null,
                ];
            })
            ->values();

        return response()->json([
            'data' => $reportes,
            'meta' => [
                'total' => $reportes->count(),
                'actualizado_en' => now()->toIso8601String(),
            ],
        ])->header('Cache-Control', 'no-store, private');
    }
}
