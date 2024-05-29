<?php

namespace App\Jobs\Whatsapp;

use App\Enums\Whatsapp\MessageStatus;
use App\Jobs\Concerns\InteractsWithException;
use App\Models\Message;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WhatsappSendTextMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $storeId,
        public int $instanceId,
        public string $instanceToken,
        public string $mobile,
        public string $message,
    ) {
        $this->maxAttempts = 5;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new FourWhatsService();

        try {
            $response = $service->sending(
                instanceId: $this->instanceId,
                instanceToken: $this->instanceToken,
            )->text(
                mobile: $this->mobile,
                message: $this->message,
            );
        } catch (FourWhatsException $e) {
            $this->handleException(
                e: new FourWhatsException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Error while sending whatsapp text message',
                            "Message {$e->getMessage()}",
                            "Instance ID: {$this->instanceId}",
                            "Instance Token: {$this->instanceToken}",
                        ],
                    ),
                    code: $e->getCode(),
                )
            );

            return;
        }

        Message::query()->create(attributes: [
            'store_id' => $this->storeId,
            'provider_id' => $response['id'],
            'mobile' => $this->mobile,
            'body' => $this->message,
            'status' => MessageStatus::PENDING,
        ]);
    }
}
