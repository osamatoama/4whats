<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web/routes.php',
        api: __DIR__.'/../routes/api/routes.php',
        commands: __DIR__.'/../routes/console/routes.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'log.zid.callback' => \App\Http\Middleware\LogZidCallback::class,
        ]);

        $middleware->redirectTo(
            guests: function (Request $request): string {
                if ($request->routeIs(patterns: 'dashboard.*')) {
                    return route(name: 'dashboard.login');
                }

                return url(path: '/');
            },
            users: function (Request $request): string {
                if ($request->routeIs(patterns: 'dashboard.*')) {
                    return route(name: 'dashboard.home');
                }

                return url(path: '/');
            },
        );

        $middleware->append(\App\Http\Middleware\Log404Middleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
