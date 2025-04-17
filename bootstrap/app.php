<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CorsMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web([
            // Add your web middleware here
        ]);

        $middleware->api([
            CorsMiddleware::class, // Apply CORS middleware to the API group
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);
    })
    // ->withMiddleware(function (Middleware $middleware) {
    //     // Register middleware for specific groups if needed
    //     // $middleware->register(CorsMiddleware::class);
    // })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
