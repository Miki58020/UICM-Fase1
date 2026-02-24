<?php

use App\Http\Controllers\AspiranteController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home.index')->name('home');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');

// Módulo de aspirantes
Route::get('/registro', [AspiranteController::class, 'create'])->name('aspirantes.registro');
Route::post('/registro', [AspiranteController::class, 'store'])->name('aspirantes.store');

Route::view('/confirmacion', 'aspirantes.confirmacion')->name('aspirantes.confirmacion');
Route::view('/seguimiento', 'aspirantes.seguimiento')->name('aspirantes.seguimiento');
Route::view('/resultado', 'aspirantes.resultado')->name('aspirantes.resultado');

// Portal del alumno
Route::view('/alumno', 'alumno.dashboard')->name('alumno.dashboard');

// Módulo pago de inscripción
Route::view('/aspirante/pago', 'aspirantes.pago')->name('aspirantes.pago');
Route::post('/aspirante/pago', [PagoController::class, 'store'])->name('aspirantes.pago.enviar');
Route::view('/aspirante/pago-confirmacion', 'aspirantes.pago_confirmacion')->name('aspirantes.pago.confirmacion');

// Módulo finanzas — Validación de pagos
Route::view('/finanzas/pagos', 'finanzas.pagos.index')->name('finanzas.pagos.index');
Route::view('/finanzas/pagos/show', 'finanzas.pagos.show')->name('finanzas.pagos.show');

// Módulo admin — Generación de matrículas e inscripción
Route::view('/admin/inscripciones', 'admin.inscripciones.index')->name('admin.inscripciones.index');
Route::view('/admin/inscripciones/generar', 'admin.inscripciones.generar')->name('admin.inscripciones.generar');

// Módulo admin — Gestión de materias (Coordinación Académica)
Route::view('/admin/materias', 'admin.materias.index')->name('admin.materias.index');

// Módulo admin — Gestión de profesores (Coordinación Académica)
Route::view('/admin/profesores', 'admin.profesores.index')->name('admin.profesores.index');

// Módulo admin — Carga académica por grupo (Coordinación Académica)
Route::view('/admin/carga-academica', 'admin.carga-academica.index')->name('admin.carga-academica.index');

// Módulo admin — Validación de aspirantes (Control Escolar)
Route::view('/admin/aspirantes', 'admin.aspirantes.index')->name('admin.aspirantes.index');
Route::view('/admin/aspirantes/show', 'admin.aspirantes.show')->name('admin.aspirantes.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
