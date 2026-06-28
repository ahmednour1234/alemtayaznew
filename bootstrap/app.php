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
        // Run the Laravel scheduler on every web request (at most once per minute)
        $middleware->web(\App\Http\Middleware\RunScheduler::class);

        $middleware->alias([
            'auth.admin'      => \App\Http\Middleware\AdminAuthenticate::class,
            'permission'      => \App\Http\Middleware\CheckPermission::class,
            'auto.permission' => \App\Http\Middleware\AutoPermission::class,
            'log.access'      => \App\Http\Middleware\LogAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
