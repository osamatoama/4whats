<?php

use App\Enums\UserRole;
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
        \App\Models\User::query()
            ->find(
                id: 3,
            )
            ->delete();

        dump(
            users: \App\Models\User::all(),
            stores: \App\Models\Store::all(),
        );
    });
});
