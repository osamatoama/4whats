<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');

        Horizon::auth(function ($request) {
//            if ($request->ajax()){
//                return true;
//            }
//            else if (isset($request->checkstatus) && $request->checkstatus == 'ok'){
//                return true;
//            }else{
//                throw new UnauthorizedHttpException('Unauthorized');
//            }

            return true;
        });
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user) {
            return auth()->user()->email == 'admin@example.com';
        });
    }
}
