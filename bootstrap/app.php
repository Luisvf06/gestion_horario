<?php
    #Este archivo es el antiguo kernel.php, los middlewares se definen en withmiddleware, dentro del array   
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\LoginMiddleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi([
            EnsureFrontendRequestsAreStateful::class,

        ]);
        $middleware->web([
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            LoginMiddleware::class,  #Creado por mí, supuestamente para controlar el acceso a usuarios no autenticados
        ]);

        $middleware->trustHosts(at: ['laravel.test']); //lo que va después de at es el dominio al que se responde
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Configuración de excepciones
    })->create();
