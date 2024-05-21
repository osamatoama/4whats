<?php

use App\Http\Controllers\Api\V1\Salla\WidgetController;
use App\Http\Controllers\Api\V1\Webhooks\FourWhatsWebhookController;
use App\Http\Controllers\Api\V1\Webhooks\SallaWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('salla', SallaWebhookController::class)->name('salla');
    Route::post('four-whats', FourWhatsWebhookController::class)->name('four-whats');
});

Route::prefix('salla')->name('salla.')->group(function () {
    Route::get('widget', WidgetController::class)->name('widget');
});
