<?php

use App\Http\Controllers\Dashboard\EmployeeController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\TemplateController;
use Illuminate\Support\Facades\Route;

Route::group([], base_path(path: 'routes/web/dashboard/auth.php'));

Route::middleware(['auth:dashboard'])->group(function () {
    Route::get('/', HomeController::class)->name('home');

    Route::resource('employees', EmployeeController::class)->except(['show', 'edit', 'update']);

    Route::resource('templates', TemplateController::class)->only(['index']);
});
