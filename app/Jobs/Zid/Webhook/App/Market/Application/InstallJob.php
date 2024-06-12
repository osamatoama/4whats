<?php

namespace App\Jobs\Zid\Webhook\App\Market\Application;

use App\Jobs\Concerns\InteractsWithException;
use App\Jobs\FourWhats\FourWhatsSetInstanceWebhookJob;
use App\Jobs\Zid\Contracts\WebhookJob;
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
use Illuminate\Support\Carbon;

class InstallJob implements ShouldQueue, WebhookJob
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
        if ($user->is_uninstalled) {
            $store->update(
                attributes: [
                    'is_uninstalled' => false,
                ],
            );

            $user->update(
                attributes: [
                    'is_uninstalled' => false,
                ],
            );
        }

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

        if ($user->subscriptions()->where(
            column: 'started_at',
            operator: '=',
            value: $startedAt,
        )->where(
            column: 'ended_at',
            operator: '=',
            value: $endedAt,
        )->exists()) {
            return;
        }

        $fourWhatsCredentials = $user->fourWhatsCredential;
        $whatsappAccount = $store->whatsappAccount;

        $fourWhatsService = new FourWhatsService();
        if ($whatsappAccount->instance_id === 0 && $whatsappAccount->instance_token === '') {
            try {
                $response = $this->create(
                    fourWhatsService: $fourWhatsService,
                    fourWhatsCredentials: $fourWhatsCredentials,
                    whatsappAccount: $whatsappAccount,
                    startedAt: $startedAt,
                    endedAt: $endedAt,
                );
            } catch (FourWhatsException) {
                return;
            }
        } else {
            try {
                $response = $this->renew(
                    fourWhatsService: $fourWhatsService,
                    fourWhatsCredentials: $fourWhatsCredentials,
                    whatsappAccount: $whatsappAccount,
                    startedAt: $startedAt,
                    endedAt: $endedAt,
                );
            } catch (FourWhatsException) {
                return;
            }
        }

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

    /**
     * @throws FourWhatsException
     */
    protected function create(
        FourWhatsService $fourWhatsService,
        FourWhatsCredential $fourWhatsCredentials,
        WhatsappAccount $whatsappAccount,
        Carbon $startedAt,
        Carbon $endedAt,
    ): array {
        try {
            $response = $fourWhatsService->instances(apiKey: $fourWhatsCredentials->api_key)->create(
                email: $fourWhatsCredentials->email,
                packageId: $this->getPackageId(
                    startedAt: $startedAt,
                    endedAt: $endedAt,
                ),
            );
        } catch (FourWhatsException $e) {
            $exception = new FourWhatsException(
                message: generateMessageUsingSeparatedLines(
                    lines: [
                        'Exception while creating four whats instance',
                        "ProviderID: {$this->providerId}",
                        "WhatsappAccount: {$whatsappAccount->id}",
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
                'expired_at' => $endedAt,
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
        Carbon $startedAt,
        Carbon $endedAt,
    ): array {
        try {
            $response = $fourWhatsService->instances(apiKey: $fourWhatsCredentials->api_key)->renew(
                email: $fourWhatsCredentials->email,
                instanceId: $whatsappAccount->instance_id,
                packageId: $this->getPackageId(
                    startedAt: $startedAt,
                    endedAt: $endedAt,
                ),
            );
        } catch (FourWhatsException $e) {
            $exception = new FourWhatsException(
                message: generateMessageUsingSeparatedLines(
                    lines: [
                        'Exception while renewing four whats instance',
                        "ProviderID: {$this->providerId}",
                        "WhatsappAccount: {$whatsappAccount->id}",
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
                'expired_at' => $endedAt,
            ],
        );

        return $response;
    }
}
