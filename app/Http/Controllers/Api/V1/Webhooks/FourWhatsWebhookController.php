<?php

namespace App\Http\Controllers\Api\V1\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\WhatsappAccount;
use Illuminate\Http\Request;

class FourWhatsWebhookController extends Controller
{
    public function __invoke(Request $request): void
    {
        if (! WhatsappAccount::query()->where(
            column: 'instance_id',
            operator: '=',
            value: $request->input(
                key: 'instanceId',
            ),
        )->exists()) {
            return;
        }

        Message::query()
            ->where(
                column: 'provider_id',
                operator: '=',
                value: $request->input(
                    key: 'ack.0.id',
                ),
            )
            ->update(
                values: [
                    'status' => $request->input(
                        key: 'ack.0.status',
                    ),
                ],
            );
    }
}
