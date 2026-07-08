<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Confiar en el proxy (ngrok en desarrollo) para que Laravel detecte correctamente el
        // esquema HTTPS público al generar URLs con route()/url(). Sin esto, route() genera
        // enlaces http:// que Mercado Pago rechaza en back_urls con auto_return.
        // NOTA: revisar antes de producción — si el hosting real no pasa por un proxy de
        // confianza, restringir "at" a las IPs del proxy en vez de '*'.
        // No se puede usar app()->environment() aquí: el binding 'env' del contenedor todavía
        // no existe en esta etapa del arranque y provoca un fatal error.
        $middleware->trustProxies(at: '*');

        $middleware->web(
            append: [
                \App\Http\Middleware\NoCacheHeaders::class,
            ],
            replace: [
                \Illuminate\Session\Middleware\StartSession::class => \App\Http\Middleware\StartSession::class,
            ],
        );
        $middleware->alias([
            'rol' => \App\Http\Middleware\CheckRol::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '/mp/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
