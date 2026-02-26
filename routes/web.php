<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AspiranteController;
use App\Http\Controllers\CargaAcademicaController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfesorController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home.index')->name('home');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');

// Módulo de aspirantes
Route::get('/registro', [AspiranteController::class, 'create'])->name('aspirantes.registro');
Route::post('/registro', [AspiranteController::class, 'store'])->name('aspirantes.store');

Route::view('/confirmacion', 'aspirantes.confirmacion')->name('aspirantes.confirmacion');
Route::view('/seguimiento', 'aspirantes.seguimiento')->name('aspirantes.seguimiento');
Route::get('/resultado', [AspiranteController::class, 'resultado'])->name('aspirantes.resultado');

// Portal del alumno
Route::get('/alumno', [AlumnoController::class, 'dashboard'])->name('alumno.dashboard')->middleware('auth');

// Módulo pago de inscripción
Route::get('/aspirante/pago', [PagoController::class, 'create'])->name('aspirantes.pago');
Route::post('/aspirante/pago', [PagoController::class, 'store'])->name('aspirantes.pago.enviar');
Route::view('/aspirante/pago-confirmacion', 'aspirantes.pago_confirmacion')->name('aspirantes.pago.confirmacion');

// Módulo finanzas — Validación de pagos
Route::get('/finanzas/pagos', [PagoController::class, 'index'])->name('finanzas.pagos.index');
Route::get('/finanzas/pagos/{pago}', [PagoController::class, 'show'])->name('finanzas.pagos.show');
Route::patch('/finanzas/pagos/{pago}/aprobar', [PagoController::class, 'aprobar'])->name('finanzas.pagos.aprobar');
Route::patch('/finanzas/pagos/{pago}/rechazar', [PagoController::class, 'rechazar'])->name('finanzas.pagos.rechazar');

// Módulo admin — Generación de matrículas e inscripción
Route::get('/admin/inscripciones', [InscripcionController::class, 'index'])->name('admin.inscripciones.index');
Route::post('/admin/inscripciones/{alumno}/inscribir', [InscripcionController::class, 'inscribir'])->name('admin.inscripciones.inscribir');
Route::get('/admin/inscripciones/{alumno}/resultado', [InscripcionController::class, 'resultado'])->name('admin.inscripciones.generar');

// Módulo admin — Gestión de materias (Coordinación Académica)
Route::get('/admin/materias', [MateriaController::class, 'index'])->name('admin.materias.index');
Route::post('/admin/materias', [MateriaController::class, 'store'])->name('admin.materias.store');
Route::patch('/admin/materias/{materia}', [MateriaController::class, 'update'])->name('admin.materias.update');
Route::patch('/admin/materias/{materia}/toggle', [MateriaController::class, 'toggle'])->name('admin.materias.toggle');

// Módulo admin — Gestión de profesores (Coordinación Académica)
Route::get('/admin/profesores', [ProfesorController::class, 'index'])->name('admin.profesores.index');
Route::post('/admin/profesores', [ProfesorController::class, 'store'])->name('admin.profesores.store');
Route::patch('/admin/profesores/{profesor}', [ProfesorController::class, 'update'])->name('admin.profesores.update');
Route::patch('/admin/profesores/{profesor}/toggle', [ProfesorController::class, 'toggle'])->name('admin.profesores.toggle');

// Módulo admin — Carga académica por grupo (Coordinación Académica)
Route::get('/admin/carga-academica', [CargaAcademicaController::class, 'index'])->name('admin.carga-academica.index');
Route::post('/admin/carga-academica/{grupo}/generar', [CargaAcademicaController::class, 'generar'])->name('admin.carga-academica.generar');

// Módulo admin — Validación de aspirantes (Control Escolar)
Route::get('/admin/aspirantes', [AspiranteController::class, 'index'])->name('admin.aspirantes.index');
Route::get('/admin/aspirantes/{aspirante}', [AspiranteController::class, 'show'])->name('admin.aspirantes.show');
Route::patch('/admin/aspirantes/{aspirante}/aprobar', [AspiranteController::class, 'aprobar'])->name('admin.aspirantes.aprobar');
Route::patch('/admin/aspirantes/{aspirante}/rechazar', [AspiranteController::class, 'rechazar'])->name('admin.aspirantes.rechazar');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
