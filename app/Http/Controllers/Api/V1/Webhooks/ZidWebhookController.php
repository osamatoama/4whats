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
            token: $request->query(
                key: 'token',
            ),
        )) {
            return;
        }

        $zidWebhookHandler->handle(
            event: $request->query(
                key: 'event',
            ),
            providerId: $request->query(
                key: 'store',
                default: $request->input(
                    key: 'store_id',
                ),
            ),
            data: $request->except(
                keys: [
                    'token',
                    'event',
                    'store',
                ],
            ),
        );
    }
}
