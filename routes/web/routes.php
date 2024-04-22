<?php

use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->name('dashboard.')->group(base_path(path: 'routes/web/dashboard/routes.php'));

Route::redirect('/', 'dashboard');
