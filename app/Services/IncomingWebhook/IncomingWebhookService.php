<?php

namespace App\Services\IncomingWebhook;

use App\Dto\IncomingWebhookDto;
use App\Jobs\IncomingWebhook\SaveIncomingWebhookJob;
use App\Models\IncomingWebhook;

final readonly class IncomingWebhookService
{
    public function create(IncomingWebhookDto $incomingWebhookDto): IncomingWebhook
    {
        return IncomingWebhook::query()
            ->create(
                attributes: [
                    'provider_type' => $incomingWebhookDto->providerType,
                    'payload' => $incomingWebhookDto->payload,
                ],
            );
    }

    public static function saveSallaIncomingWebhook(array $payload): void
    {
        SaveIncomingWebhookJob::dispatch(
            incomingWebhookDto: IncomingWebhookDto::fromSalla(
                payload: $payload,
            ),
        );
    }

    public static function saveZidIncomingWebhook(array $payload): void
    {
        SaveIncomingWebhookJob::dispatch(
            incomingWebhookDto: IncomingWebhookDto::fromZid(
                payload: $payload,
            ),
        );
    }

    public static function saveFourWhatsIncomingWebhook(array $payload): void
    {
        SaveIncomingWebhookJob::dispatch(
            incomingWebhookDto: IncomingWebhookDto::fromFourWhats(
                payload: $payload,
            ),
        );
    }
}
