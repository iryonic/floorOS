<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// Auto-create .env from .env.example if missing on fresh deployments (prevents key:generate crash)
$rootPath = dirname(__DIR__);
if (!file_exists($rootPath . '/.env') && file_exists($rootPath . '/.env.example')) {
    @copy($rootPath . '/.env.example', $rootPath . '/.env');
}

return Application::configure(basePath: $rootPath)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/admin/login');
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
