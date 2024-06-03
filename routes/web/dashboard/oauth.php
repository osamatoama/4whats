<?php

use App\Http\Controllers\Dashboard\OAuth\Zid\ZidCallbackController;
use App\Http\Controllers\Dashboard\OAuth\Zid\ZidRedirectController;
use Illuminate\Support\Facades\Route;

Route::prefix('zid')->name('zid.')->group(function () {
    Route::get('redirect', ZidRedirectController::class)->name('redirect');
    Route::get('callback', ZidCallbackController::class)->name('callback');
});
