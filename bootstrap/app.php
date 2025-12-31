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
        // Registrar alias de middleware
        $middleware->alias([
            'prevent.back' => \App\Http\Middleware\PreventBackHistory::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'check.status' => \App\Http\Middleware\CheckUserStatus::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        
        // Configurar redirección para invitados
        $middleware->redirectGuestsTo('/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
