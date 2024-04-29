<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Authenticate::redirectUsing(redirectToCallback: function (Request $request) {
            if ($request->routeIs(patterns: 'dashboard.*')) {
                return route(name: 'dashboard.login');
            }

            return url(path: '/');
        });

        RedirectIfAuthenticated::redirectUsing(redirectToCallback: function (Request $request) {
            if ($request->routeIs(patterns: 'dashboard.*')) {
                return route(name: 'dashboard.home');
            }

            return url(path: '/');
        });

        Paginator::defaultView(view: 'dashboard.partials.pagination.default');
    }
}
