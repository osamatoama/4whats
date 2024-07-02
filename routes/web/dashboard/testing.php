<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

Route::middleware([
    Authenticate::using(
        guard: 'dashboard',
    ),
    RoleMiddleware::using(
        role: UserRole::ADMIN->value,
        guard: UserRole::ADMIN->guardName(),
    ),
])->group(function (): void {
    Route::get('/', function (): void {
        User::query()
            ->with(
                relations: [
                    'providerTokens',
                    'stores',
                    'subscriptions',
                ],
            )
            ->each(
                callback: function (User $user): void {
                    dump(
                        user: $user,
                        providerTokens: $user->providerTokens,
                        stores: $user->stores,
                        subscriptions: $user->subscriptions,
                    );
                }
            );
    });

    Route::get('delete-user/{user}', function (User $user): void {
        if ($user->is_admin) {
            abort(
                code: 404,
            );
        }

        $user->delete();
    });

    Route::get('queue/restart', function (): string {
        Artisan::call(
            command: 'queue:restart',
        );

        return Artisan::output();
    });
});
