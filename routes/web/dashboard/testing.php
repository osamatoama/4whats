<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
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
            ->find(
                id: 4,
            )
            ->delete();

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
});
