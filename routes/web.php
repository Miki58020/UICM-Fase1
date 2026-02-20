<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home.index')->name('home');

// Módulo de aspirantes
Route::view('/registro', 'aspirantes.registro')->name('aspirantes.registro');
Route::view('/confirmacion', 'aspirantes.confirmacion')->name('aspirantes.confirmacion');
Route::view('/seguimiento', 'aspirantes.seguimiento')->name('aspirantes.seguimiento');
Route::view('/resultado', 'aspirantes.resultado')->name('aspirantes.resultado');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
