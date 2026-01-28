<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
        'permission' => \App\Http\Middleware\CheckPermission::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
    $middleware->api(append: [
            LocaleMiddleware::class,
            ]);
            $middleware->web(append: [
        \App\Http\Middleware\SetLocale::class,

    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
