<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\ExportacionController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\PrioridadController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/estadisticas/excel', [ExportacionController::class, 'excel'])
        ->name('estadisticas.excel');

    Route::get('/estadisticas', [EstadisticaController::class, 'index'])
        ->name('estadisticas.index');

    Route::resource('ordenes', OrdenTrabajoController::class)
        ->parameters(['ordenes' => 'orden'])
        ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::post('/ordenes/{orden}/finalizar', [OrdenTrabajoController::class, 'finalizar'])
        ->name('ordenes.finalizar');

    Route::get('/prioridades', [PrioridadController::class, 'index'])
        ->name('prioridades.index');

    Route::post('/prioridades/recalcular', [PrioridadController::class, 'recalcular'])
        ->name('prioridades.recalcular');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
