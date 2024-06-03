<?php

use App\Http\Controllers\Api\V1\Salla\SettingsValidatorController;
use App\Http\Controllers\Api\V1\Salla\WidgetController;
use App\Http\Controllers\Api\V1\Webhooks\FourWhatsWebhookController;
use App\Http\Controllers\Api\V1\Webhooks\SallaWebhookController;
use App\Http\Controllers\Api\V1\Webhooks\ZidWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('salla', SallaWebhookController::class)->name('salla');
    Route::post('zid', ZidWebhookController::class)->name('zid');
    Route::post('four-whats', FourWhatsWebhookController::class)->name('four-whats');
});

Route::prefix('salla')->name('salla.')->group(function () {
    Route::post('settings-validator', SettingsValidatorController::class)->name('settings-validator');
    Route::get('widget', WidgetController::class)->name('widget');
});
