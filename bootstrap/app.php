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
        // Aquí defines alias de middleware personalizados
        $middleware->alias([
            'rol' => \App\Http\Middleware\VerificarRol::class, // 👈 tu middleware de roles
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Aquí puedes manejar errores globales si lo deseas
    })
    ->create(); // 👈 El create SIEMPRE va al final
