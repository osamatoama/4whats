<?php

use App\Http\Controllers\Dashboard\HomeController;
use Illuminate\Support\Facades\Route;

Route::group([], base_path(path: 'routes/web/dashboard/auth.php'));

Route::middleware('auth:dashboard')->group(function () {
    Route::get('/', HomeController::class)->name('home');
});
