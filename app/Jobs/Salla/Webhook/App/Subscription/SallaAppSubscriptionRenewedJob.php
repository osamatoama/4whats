<?php

namespace App\Jobs\Salla\Webhook\App\Subscription;

use App\Jobs\Concerns\InteractsWithException;
use App\Jobs\FourWhats\FourWhatsSetInstanceWebhookJob;
use App\Models\Store;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaAppSubscriptionRenewedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $event,
        public int $merchantId,
        public array $data,
    ) {
        $this->maxAttempts = 5;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $store = Store::query()->salla(providerId: $this->merchantId)->first();
        if ($store === null) {
            $this->handleException(
                e: new Exception(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Exception while renewing salla subscription',
                            "Merchant: {$this->merchantId}",
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
                            'Exception while renewing salla subscription',
                            "Merchant: {$this->merchantId}",
                            'Reason: User is not integrated with four whats',
                        ],
                    ),
                    code: 401,
                ),
                delay: 60,
            );

            return;
        }

        $fourWhatsCredentials = $user->fourWhatsCredential;
        $whatsappAccount = $store->whatsappAccount;

        $packageId = $this->data['plan_period'] == 1 ? 2 : 5;
        $expiredAt = $this->data['end_date'];

        $fourWhatsService = new FourWhatsService();
        try {
            $response = $fourWhatsService->instances(apiKey: $fourWhatsCredentials->api_key)->renew(
                email: $fourWhatsCredentials->email,
                instanceId: $whatsappAccount->instance_id,
                packageId: $packageId,
            );
        } catch (FourWhatsException $e) {
            $this->handleException(
                e: new FourWhatsException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Exception while renewing four whats instance',
                            "Merchant: {$this->merchantId}",
                            "Whatsapp Account: {$whatsappAccount->id}",
                            "Reason: {$e->getMessage()}",
                        ],
                    ),
                    code: $e->getCode(),
                ),
            );

            return;
        }

        FourWhatsSetInstanceWebhookJob::dispatch(
            instanceId: $response['instance_id'],
            instanceToken: $response['instance_token'],
        );

        $whatsappAccount->update(attributes: [
            'expired_at' => $expiredAt,
        ]);

        $user->subscriptions()->create(attributes: [
            'provider_type' => $store->provider_type,
            'provider_id' => $store->provider_id,
            'total_amount' => $this->data['total'] * 100,
            'total_currency' => 'SAR',
            'started_at' => $this->data['start_date'],
            'ended_at' => $this->data['end_date'],
        ]);
    }
}
