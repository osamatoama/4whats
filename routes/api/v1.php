<?php

use App\Http\Controllers\Api\V1\Webhooks\SallaWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('salla', SallaWebhookController::class)->name('salla');
});
