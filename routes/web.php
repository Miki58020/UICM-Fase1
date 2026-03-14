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
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Terminar sesión vía AJAX (cuando se detecta restauración de pestaña cerrada)
Route::post('/session/terminate', function (Illuminate\Http\Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->json(['status' => 'ok']);
})->name('session.terminate');

Route::view('/', 'home.index')->name('home');
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');

// Módulo de aspirantes (público)
Route::get('/registro', [AspiranteController::class, 'create'])->name('aspirantes.registro');
Route::post('/registro', [AspiranteController::class, 'store'])->name('aspirantes.store');

Route::view('/confirmacion', 'aspirantes.confirmacion')->name('aspirantes.confirmacion');
Route::view('/seguimiento', 'aspirantes.seguimiento')->name('aspirantes.seguimiento');
Route::get('/resultado', [AspiranteController::class, 'resultado'])->name('aspirantes.resultado');

// Módulo pago de inscripción (público)
Route::get('/aspirante/pago', [PagoController::class, 'create'])->name('aspirantes.pago');
Route::post('/aspirante/pago', [PagoController::class, 'store'])->name('aspirantes.pago.enviar');
Route::view('/aspirante/pago-confirmacion', 'aspirantes.pago_confirmacion')->name('aspirantes.pago.confirmacion');

// Portal del alumno
Route::middleware(['auth', 'rol:alumno'])->group(function () {
    Route::get('/alumno', [AlumnoController::class, 'dashboard'])->name('alumno.dashboard');
});

// Módulo finanzas — Validación de pagos
Route::middleware(['auth', 'rol:finanzas'])->group(function () {
    Route::get('/finanzas/pagos', [PagoController::class, 'index'])->name('finanzas.pagos.index');
    Route::get('/finanzas/pagos/{pago}', [PagoController::class, 'show'])->name('finanzas.pagos.show');
    Route::patch('/finanzas/pagos/{pago}/aprobar', [PagoController::class, 'aprobar'])->name('finanzas.pagos.aprobar');
    Route::patch('/finanzas/pagos/{pago}/rechazar', [PagoController::class, 'rechazar'])->name('finanzas.pagos.rechazar');
});

// Módulo Control Escolar — Aspirantes e inscripciones
Route::middleware(['auth', 'rol:control_escolar'])->group(function () {
    Route::get('/admin/aspirantes', [AspiranteController::class, 'index'])->name('admin.aspirantes.index');
    Route::get('/admin/aspirantes/{aspirante}', [AspiranteController::class, 'show'])->name('admin.aspirantes.show');
    Route::patch('/admin/aspirantes/{aspirante}/aprobar', [AspiranteController::class, 'aprobar'])->name('admin.aspirantes.aprobar');
    Route::patch('/admin/aspirantes/{aspirante}/rechazar', [AspiranteController::class, 'rechazar'])->name('admin.aspirantes.rechazar');

    Route::get('/admin/inscripciones', [InscripcionController::class, 'index'])->name('admin.inscripciones.index');
    Route::post('/admin/inscripciones/{alumno}/inscribir', [InscripcionController::class, 'inscribir'])->name('admin.inscripciones.inscribir');
    Route::get('/admin/inscripciones/{alumno}/resultado', [InscripcionController::class, 'resultado'])->name('admin.inscripciones.generar');
});

// Módulo admin — Gestión de usuarios del sistema
Route::middleware(['auth', 'rol:admin'])->group(function () {
    Route::get('/admin/usuarios', [UsuarioController::class, 'index'])->name('admin.usuarios.index');
    Route::post('/admin/usuarios', [UsuarioController::class, 'store'])->name('admin.usuarios.store');
    Route::patch('/admin/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('/admin/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('admin.usuarios.destroy');
});

// Módulo coordinación — Materias, profesores y carga académica
Route::middleware(['auth', 'rol:coordinacion'])->group(function () {
    Route::get('/admin/materias', [MateriaController::class, 'index'])->name('admin.materias.index');
    Route::post('/admin/materias', [MateriaController::class, 'store'])->name('admin.materias.store');
    Route::patch('/admin/materias/{materia}', [MateriaController::class, 'update'])->name('admin.materias.update');
    Route::patch('/admin/materias/{materia}/toggle', [MateriaController::class, 'toggle'])->name('admin.materias.toggle');

    Route::get('/admin/profesores', [ProfesorController::class, 'index'])->name('admin.profesores.index');
    Route::post('/admin/profesores', [ProfesorController::class, 'store'])->name('admin.profesores.store');
    Route::patch('/admin/profesores/{profesor}', [ProfesorController::class, 'update'])->name('admin.profesores.update');
    Route::patch('/admin/profesores/{profesor}/toggle', [ProfesorController::class, 'toggle'])->name('admin.profesores.toggle');

    Route::get('/admin/carga-academica', [CargaAcademicaController::class, 'index'])->name('admin.carga-academica.index');
    Route::post('/admin/carga-academica/{grupo}/generar', [CargaAcademicaController::class, 'generar'])->name('admin.carga-academica.generar');
});

// Servir archivos privados — solo usuarios autenticados del sistema
Route::get('/admin/archivo/{path}', function (string $path) {
    if (!Storage::disk('local')->exists($path)) {
        abort(404);
    }
    return Storage::disk('local')->response($path);
})->middleware('auth')->where('path', '.*')->name('admin.archivo');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
