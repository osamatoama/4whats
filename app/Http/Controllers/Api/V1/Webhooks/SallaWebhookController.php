<?php

namespace App\Http\Controllers\Api\V1\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\IncomingWebhook\IncomingWebhookService;
use App\Services\Salla\Webhook\SallaWebhookHandler;
use Illuminate\Http\Request;

class SallaWebhookController extends Controller
{
    public function __invoke(Request $request, SallaWebhookHandler $sallaWebhooksHandler): void
    {
        IncomingWebhookService::saveSallaIncomingWebhook(
            payload: $request->all(),
        );

        if ($sallaWebhooksHandler->isNotVerified(token: $request->header(key: 'Authorization'))) {
            return;
        }

        $sallaWebhooksHandler->handle(
            event: $request->input(key: 'event'),
            merchantId: $request->input(key: 'merchant'),
            data: $request->input(key: 'data'),
        );
    }
}
