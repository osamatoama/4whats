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
        dump(
            users: \App\Models\User::all(),
            stores: \App\Models\Store::all(),
        );
    });
});
