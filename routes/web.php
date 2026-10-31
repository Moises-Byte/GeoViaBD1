<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\ExportacionController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\PrioridadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CuadrillaController;
use App\Http\Controllers\InspeccionController;
use App\Http\Controllers\ViaController;
use App\Http\Controllers\MapaPublicoController;
use App\Http\Controllers\TipoDanoController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Mapa público de alertas: no requiere iniciar sesión.
Route::get('/mapa', [MapaPublicoController::class, 'index'])
    ->name('mapa.publico');

Route::get('/api/mapa/reportes', [MapaPublicoController::class, 'reportes'])
    ->name('mapa.reportes');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/estadisticas/excel', [ExportacionController::class, 'excel'])
        ->name('estadisticas.excel');

    Route::get('/estadisticas', [EstadisticaController::class, 'index'])
        ->name('estadisticas.index');

    Route::resource('reportes', ReporteController::class)
        ->only(['index', 'create', 'store', 'show']);

        Route::resource('vias', ViaController::class)
        ->parameters(['vias' => 'via'])
        ->except(['show']);

    Route::resource('cuadrillas', CuadrillaController::class)
        ->parameters(['cuadrillas' => 'cuadrilla'])
        ->except(['show']);

    Route::resource('inspecciones', InspeccionController::class)
        ->parameters(['inspecciones' => 'inspeccion']);

    Route::resource('ordenes', OrdenTrabajoController::class)
        ->parameters(['ordenes' => 'orden'])
        ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::post('/ordenes/{orden}/finalizar', [OrdenTrabajoController::class, 'finalizar'])
        ->name('ordenes.finalizar');

    Route::get('/prioridades', [PrioridadController::class, 'index'])
        ->name('prioridades.index');

    Route::post('/prioridades/recalcular', [PrioridadController::class, 'recalcular'])
        ->name('prioridades.recalcular');

    Route::resource('tipos-dano', TipoDanoController::class)
        ->parameters(['tipos-dano' => 'tipo'])
        ->only(['index', 'create', 'store', 'edit', 'update']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
