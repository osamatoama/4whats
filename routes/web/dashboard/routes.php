<?php

use App\Http\Controllers\Dashboard\Campaign\CampaignCurrentController;
use App\Http\Controllers\Dashboard\Campaign\CampaignSendController;
use App\Http\Controllers\Dashboard\ContactController;
use App\Http\Controllers\Dashboard\EmployeeController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\MessageController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\StoreController;
use App\Http\Controllers\Dashboard\TemplateController;
use Illuminate\Support\Facades\Route;

Route::group([], base_path(
    path: 'routes/web/dashboard/auth.php',
));

Route::prefix('oauth')->name('oauth.')->middleware(['guest:dashboard'])->group(base_path(
    path: 'routes/web/dashboard/oauth.php',
));

Route::middleware(['auth:dashboard'])->group(function () {
    Route::get('/', HomeController::class)->name('home');

    Route::resource('stores', StoreController::class)->only(['index']);

    Route::resource('employees', EmployeeController::class)->only(['index', 'create', 'store']);

    Route::resource('templates', TemplateController::class)->only(['index']);

    Route::resource('contacts', ContactController::class)->only(['index']);

    Route::prefix('campaigns')->name('campaigns.')->group(function () {
        Route::get('send', CampaignSendController::class)->name('send');
        Route::get('current', CampaignCurrentController::class)->name('current');
    });

    Route::resource('messages', MessageController::class)->only(['index']);

    Route::resource('settings', SettingController::class)->only(['index']);
});

Route::prefix('testing')->name('testing.')->group(base_path(
    path: 'routes/web/dashboard/testing.php',
));
