<?php

use App\Http\Controllers\DashboardController;
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
    Route::resource('ordenes', OrdenTrabajoController::class)
        ->only(['index', 'create', 'store']);

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
