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
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'active' => \App\Http\Middleware\CheckActiveUser::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\CheckActiveUser::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'payos/webhook',
            'api/payos/webhook',
            'api/search-history',
            'api/search-history/*',
            'api/chat-ai/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
