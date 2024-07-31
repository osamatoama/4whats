<?php

namespace App\Jobs\Salla\Webhook\App\Trial;

use App\Enums\SubscriptionType;
use App\Jobs\Concerns\InteractsWithException;
use App\Jobs\FourWhats\FourWhatsSetInstanceWebhookJob;
use App\Models\FourWhatsCredential;
use App\Models\Store;
use App\Models\WhatsappAccount;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaAppTrialStartedJob implements ShouldQueue
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
                            'Exception while starting salla trial',
                            "Merchant: {$this->merchantId}",
                            'Reason: Store not found',
                        ],
                    ),
                    code: 404,
                ),
                delay: 10,
            );

            return;
        }

        $user = $store->user;
        if ($user->is_not_integrated_with_four_whats) {
            $this->handleException(
                e: new Exception(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Exception while starting salla trial',
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

        $fourWhatsService = new FourWhatsService();
        if ($whatsappAccount->instance_id === 0 && $whatsappAccount->instance_token === '') {
            try {
                $this->create(
                    fourWhatsService: $fourWhatsService,
                    fourWhatsCredentials: $fourWhatsCredentials,
                    whatsappAccount: $whatsappAccount,
                    expiredAt: $this->data['end_date'],
                );
            } catch (FourWhatsException) {
                return;
            }
        } else {
            try {
                $this->renew(
                    fourWhatsService: $fourWhatsService,
                    fourWhatsCredentials: $fourWhatsCredentials,
                    whatsappAccount: $whatsappAccount,
                    expiredAt: $this->data['end_date'],
                );
            } catch (FourWhatsException) {
                return;
            }
        }

        $store->update(
            attributes: [
                'subscription_type' => SubscriptionType::TRIAL,
            ],
        );
    }

    /**
     * @throws FourWhatsException
     */
    protected function create(
        FourWhatsService $fourWhatsService,
        FourWhatsCredential $fourWhatsCredentials,
        WhatsappAccount $whatsappAccount,
        string $expiredAt,
    ): array {
        try {
            $response = $fourWhatsService->instances(apiKey: $fourWhatsCredentials->api_key)->create(
                email: $fourWhatsCredentials->email,
                packageId: 1,
            );
        } catch (FourWhatsException $e) {
            $exception = new FourWhatsException(
                message: generateMessageUsingSeparatedLines(
                    lines: [
                        'Exception while creating four whats instance',
                        "Merchant: {$this->merchantId}",
                        "Whatsapp Account: {$whatsappAccount->id}",
                        "Reason: {$e->getMessage()}",
                    ],
                ),
                code: $e->getCode(),
            );

            $this->handleException(
                e: $exception,
            );

            throw $exception;
        }

        FourWhatsSetInstanceWebhookJob::dispatch(
            instanceId: $response['instance_id'],
            instanceToken: $response['instance_token'],
        );

        $whatsappAccount->update(
            attributes: [
                'instance_id' => $response['instance_id'],
                'instance_token' => $response['instance_token'],
                'expired_at' => $expiredAt,
            ],
        );

        return $response;
    }

    /**
     * @throws FourWhatsException
     */
    protected function renew(
        FourWhatsService $fourWhatsService,
        FourWhatsCredential $fourWhatsCredentials,
        WhatsappAccount $whatsappAccount,
        string $expiredAt,
    ): array {
        try {
            $response = $fourWhatsService->instances(apiKey: $fourWhatsCredentials->api_key)->renew(
                email: $fourWhatsCredentials->email,
                instanceId: $whatsappAccount->instance_id,
                packageId: 1,
            );
        } catch (FourWhatsException $e) {
            $exception = new FourWhatsException(
                message: generateMessageUsingSeparatedLines(
                    lines: [
                        'Exception while renewing four whats instance',
                        "Merchant: {$this->merchantId}",
                        "Whatsapp Account: {$whatsappAccount->id}",
                        "Reason: {$e->getMessage()}",
                    ],
                ),
                code: $e->getCode(),
            );

            $this->handleException(
                e: $exception,
            );

            throw $exception;
        }

        $whatsappAccount->update(
            attributes: [
                'expired_at' => $expiredAt,
            ],
        );

        return $response;
    }
}
