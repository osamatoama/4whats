<?php

namespace App\Services\IncomingWebhook;

use App\Dto\IncomingWebhookDto;
use App\Jobs\IncomingWebhook\SaveIncomingWebhookJob;
use App\Models\IncomingWebhook;
use Throwable;

final readonly class IncomingWebhookService
{
    /**
     * @throws Throwable
     */
    public function create(IncomingWebhookDto $incomingWebhookDto): IncomingWebhook
    {
        try {
            return IncomingWebhook::query()
                ->create(
                    attributes: [
                        'provider_type' => $incomingWebhookDto->providerType,
                        'payload' => $incomingWebhookDto->payload,
                    ],
                );
        } catch (Throwable $e) {
            logger()->error('IncomingWebhookService::create failed', [
                'error'       => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Rethrow to let the job handle it
        }
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
