<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function (): void {
    dump(
        users: \App\Models\User::all(),
        stores: \App\Models\Store::all(),
    );
});
