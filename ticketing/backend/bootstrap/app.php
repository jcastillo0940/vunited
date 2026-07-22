<?php

use App\Http\Middleware\AttachCorrelationId;
use App\Http\Middleware\RequireAdmin;
use App\Http\Middleware\RequireCustomer;
use App\Http\Middleware\VerifyInternalSecret;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            AttachCorrelationId::class,
        ]);
        $middleware->alias([
            'internal.secret' => VerifyInternalSecret::class,
            'admin' => RequireAdmin::class,
            'customer' => RequireCustomer::class,
        ]);
        $middleware->trustProxies(at: '*');
        // API-only app: no 'login' route exists. Sin esto, un guest en una ruta
        // protegida sin header Accept: application/json revienta con
        // "Route [login] not defined" en vez de un 401 limpio.
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
