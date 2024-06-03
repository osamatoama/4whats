<?php

namespace App\Http\Controllers\Api\V1\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\Zid\Webhook\ZidWebhookHandler;
use Illuminate\Http\Request;

class ZidWebhookController extends Controller
{
    public function __invoke(Request $request, ZidWebhookHandler $zidWebhookHandler): void
    {
        if ($zidWebhookHandler->isNotVerified(
            token: $request->header(
                key: 'X-Zid-Webhook-Token',
            ),
            appId: $request->input(
                key: 'app_id',
            ),
        )) {
            return;
        }

        $zidWebhookHandler->handle(
            event: $request->input(
                key: 'event_name',
            ),
            providerId: $request->input(
                key: 'store_id',
            ),
            data: $request->except(
                keys: ['app_id', 'store_id', 'event_name'],
            ),
        );
    }
}
