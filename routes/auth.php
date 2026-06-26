<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.procesar');

    // Solicitud de contraseña nueva: la atiende Control Escolar/Admin, no un correo con token
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Evita el error 405 si alguien navega a /logout por GET
    Route::get('logout', fn() => redirect()->route('login'));
});
