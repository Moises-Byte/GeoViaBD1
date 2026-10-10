<?php

namespace App\Http\Controllers;

use App\Models\Fotografia;
use App\Models\Reporte;
use App\Models\TipoDano;
use App\Models\Via;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ReporteController extends Controller
{
    private const ESTADOS = ['PENDIENTE', 'VALIDADO', 'RECHAZADO', 'EN REPARACION', 'FINALIZADO'];

    private const ROLES_MUNICIPALES = ['AUTORIDAD', 'INSPECTOR', 'SUPERVISOR'];

    /**
     * El vecino ve solo sus reportes; el personal municipal ve todos.
     */
    public function index(Request $request): View
    {
        $esMunicipal = $this->esMunicipal($request);
        $estado = $request->query('estado');
        $estado = in_array($estado, self::ESTADOS, true) ? $estado : null;

        $reportes = Reporte::with(['via', 'tipoDano', 'usuario', 'ordenesTrabajo'])
            ->when(! $esMunicipal, fn ($q) => $q->where('usuario_id_usuario', $request->user()->id_usuario))
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->orderByDesc('fecha_reporte')
            ->orderByDesc('id_reporte')
            ->paginate(10)
            ->withQueryString();

        return view('reportes.index', [
            'reportes' => $reportes,
            'estados' => self::ESTADOS,
            'estadoActual' => $estado,
            'esMunicipal' => $esMunicipal,
        ]);
    }

    public function create(): View
    {
        return view('reportes.create', [
            'vias' => Via::orderBy('nombre_via')->get(),
            'tipos' => TipoDano::where('activo', 'S')->orderBy('nombre')->get(),
            'mapa' => config('geovia.mapa'),
            'maxFotos' => config('geovia.fotos.maximo'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $maxFotos = (int) config('geovia.fotos.maximo');
        $maxKb = (int) config('geovia.fotos.max_kb');

        $datos = $request->validate([
            'via_id_via' => ['required', 'integer', Rule::exists('via', 'id_via')],
            'tipo_dano_id_tipo_dano' => [
                'required',
                'integer',
                Rule::exists('tipo_dano', 'id_tipo_dano')->where('activo', 'S'),
            ],
            'descripcion' => ['required', 'string', 'min:10', 'max:500'],
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
            'fotos' => ['nullable', 'array', "max:{$maxFotos}"],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', "max:{$maxKb}"],
        ], [
            'via_id_via.required' => 'Selecciona la vía afectada.',
            'via_id_via.exists' => 'La vía seleccionada no existe.',
            'tipo_dano_id_tipo_dano.required' => 'Selecciona el tipo de daño.',
            'tipo_dano_id_tipo_dano.exists' => 'El tipo de daño seleccionado no está disponible.',
            'descripcion.required' => 'Describe el daño que encontraste.',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',
            'descripcion.max' => 'La descripción no puede superar 500 caracteres.',
            'latitud.required' => 'Marca la ubicación del daño en el mapa.',
            'latitud.numeric' => 'La ubicación marcada no es válida.',
            'latitud.between' => 'La latitud debe estar entre -90 y 90.',
            'longitud.required' => 'Marca la ubicación del daño en el mapa.',
            'longitud.numeric' => 'La ubicación marcada no es válida.',
            'longitud.between' => 'La longitud debe estar entre -180 y 180.',
            'fotos.array' => 'Las fotografías no son válidas.',
            'fotos.max' => "Puedes adjuntar máximo {$maxFotos} fotografías.",
            'fotos.*.image' => 'Cada archivo debe ser una imagen.',
            'fotos.*.mimes' => 'Las fotografías deben ser JPG, PNG o WEBP.',
            'fotos.*.max' => 'Cada fotografía puede pesar máximo '.($maxKb / 1024).' MB.',
            'fotos.*.uploaded' => 'No se pudo subir una fotografía. Revisa que no pese más de '.($maxKb / 1024).' MB.',
        ]);

        $rutasGuardadas = [];

        try {
            $reporte = DB::transaction(function () use ($request, $datos, &$rutasGuardadas) {
                $reporte = Reporte::create([
                    'usuario_id_usuario' => $request->user()->id_usuario,
                    'via_id_via' => $datos['via_id_via'],
                    'tipo_dano_id_tipo_dano' => $datos['tipo_dano_id_tipo_dano'],
                    'descripcion' => trim($datos['descripcion']),
                    'latitud' => round((float) $datos['latitud'], 7),
                    'longitud' => round((float) $datos['longitud'], 7),
                    'fecha_reporte' => now(),
                    'estado' => 'PENDIENTE',
                    'prioridad' => 0,
                ]);

                foreach ($request->file('fotos', []) as $foto) {
                    $ruta = $foto->store("reportes/{$reporte->id_reporte}", 'public');
                    $rutasGuardadas[] = $ruta;

                    Fotografia::create([
                        'reporte_id_reporte' => $reporte->id_reporte,
                        'ruta_foto' => $ruta,
                    ]);
                }

                return $reporte;
            });
        } catch (Throwable $e) {
            // Si algo falla, no dejar archivos huérfanos en disco.
            Storage::disk('public')->delete($rutasGuardadas);
            report($e);

            return back()
                ->withInput($request->except('fotos'))
                ->withErrors(['general' => 'No se pudo guardar el reporte. Inténtalo de nuevo en unos minutos.']);
        }

        return redirect()->route('reportes.show', $reporte)->with(
            'success',
            "Tu reporte #{$reporte->id_reporte} fue enviado. La autoridad municipal lo revisará pronto."
        );
    }

    public function show(Request $request, Reporte $reporte): View
    {
        abort_unless(
            $this->esMunicipal($request)
                || (int) $reporte->usuario_id_usuario === (int) $request->user()->id_usuario,
            403,
            'Solo puedes consultar tus propios reportes.'
        );

        $reporte->load([
            'via',
            'tipoDano',
            'usuario',
            'fotografias',
            'inspecciones',
            'ordenesTrabajo.cuadrilla',
        ]);

        return view('reportes.show', [
            'reporte' => $reporte,
            'orden' => $reporte->ordenesTrabajo->sortByDesc('id_orden')->first(),
            'inspeccion' => $reporte->inspecciones->sortByDesc('id_inspeccion')->first(),
            'esMunicipal' => $this->esMunicipal($request),
        ]);
    }

    private function esMunicipal(Request $request): bool
    {
        return in_array($request->user()?->rol, self::ROLES_MUNICIPALES, true);
    }
}