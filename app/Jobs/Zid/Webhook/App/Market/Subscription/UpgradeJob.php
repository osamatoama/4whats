<?php

namespace App\Jobs\Zid\Webhook\App\Market\Subscription;

use App\Jobs\Concerns\InteractsWithException;
use App\Jobs\Zid\Contracts\WebhookJob;
use App\Models\Store;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class UpgradeJob implements ShouldQueue, WebhookJob
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly string $event,
        public readonly int $providerId,
        public readonly array $data,
    ) {
        $this->maxAttempts = 5;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $store = Store::query()->zid(providerId: $this->providerId)->first();
        if ($store === null) {
            $this->handleException(
                e: new Exception(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            "Error while handling zid {$this->event} webhook",
                            "ProviderID: {$this->providerId}",
                            'Reason: Store not found',
                        ],
                    ),
                    code: 404,
                ),
                fail: true,
            );

            return;
        }

        $user = $store->user;
        if ($user->is_not_integrated_with_four_whats) {
            $this->handleException(
                e: new Exception(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            "Error while handling zid {$this->event} webhook",
                            "ProviderID: {$this->providerId}",
                            'Reason: User is not integrated with four whats',
                        ],
                    ),
                    code: 401,
                ),
                delay: 60,
            );

            return;
        }

        $startedAt = Carbon::parse(
            time: $this->data['start_date'],
        );
        $endedAt = Carbon::parse(
            time: $this->data['end_date'],
        );

        $fourWhatsCredentials = $user->fourWhatsCredential;
        $whatsappAccount = $store->whatsappAccount;

        $fourWhatsService = new FourWhatsService();
        try {
            $fourWhatsService->instances(apiKey: $fourWhatsCredentials->api_key)->renew(
                email: $fourWhatsCredentials->email,
                instanceId: $whatsappAccount->instance_id,
                packageId: $this->getPackageId(
                    startedAt: $startedAt,
                    endedAt: $endedAt,
                ),
            );
        } catch (FourWhatsException $e) {
            $this->handleException(
                e: new FourWhatsException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Exception while renewing four whats instance',
                            "ProviderID: {$this->providerId}",
                            "WhatsappAccount: {$whatsappAccount->id}",
                            "Reason: {$e->getMessage()}",
                        ],
                    ),
                    code: $e->getCode(),
                ),
            );

            return;
        }

        $whatsappAccount->update(
            attributes: [
                'expired_at' => $endedAt,
            ],
        );

        $user->subscriptions()->create(
            attributes: [
                'provider_type' => $store->provider_type,
                'provider_id' => $store->provider_id,
                'total_amount' => $this->data['amount_paid'] * 100,
                'total_currency' => 'SAR',
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
            ],
        );
    }

    protected function getPackageId(Carbon $startedAt, Carbon $endedAt): int
    {
        $diff = (int) ceil(
            num: $startedAt->diffInMonths(
                date: $endedAt,
            ),
        );

        return match ($diff) {
            1 => 2,
            3 => 3,
            6 => 4,
            12 => 5,
        };
    }
}
