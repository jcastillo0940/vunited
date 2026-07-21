<?php

use Illuminate\Foundation\Application;
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
        $middleware->alias([
            'admin.permission' => \App\Http\Middleware\EnsureAdminHasPermission::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\AttachCorrelationId::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                if (\Illuminate\Support\Facades\Auth::guard('web')->check()) {
                    abort(403);
                }

                return route('admin.login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(fn ($request) => $request->is('admin') || $request->is('admin/*')
            ? route('admin.dashboard')
            : route('dashboard'));

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
