<?php

namespace App\Jobs\Whatsapp;

use App\Enums\Whatsapp\MessageStatus;
use App\Enums\Whatsapp\MessageType;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Jobs\Concerns\InteractsWithException;
use App\Models\Message;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use App\Traits\HasMessageRateLimit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class WhatsappSendImageMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels, HasMessageRateLimit;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $storeId,
        public int $instanceId,
        public string $instanceToken,
        public string $mobile,
        public string $imagePath,
        public ?string $caption = null,
    ) {
        $this->maxAttempts = 5;
    }

    public function middleware(): array
    {
        return [new RateLimited('campaign-whatsapp-messages')];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $name = str(string: $this->imagePath)->afterLast(search: '/')->toString();
        $url = Storage::url(
            path: $this->imagePath,
        );

        $service = new FourWhatsService();

        try {
            $response = $service->sending(
                instanceId: $this->instanceId,
                instanceToken: $this->instanceToken,
            )->file(
                mobile: $this->mobile,
                fileName: $name,
                fileUrl: $url,
                caption: $this->caption,
            );
        } catch (FourWhatsException $e) {
            $this->handleException(
                e: new FourWhatsException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Error while sending whatsapp image message',
                            "Message {$e->getMessage()}",
                            "ImagePath: {$this->imagePath}",
                            "Instance ID: {$this->instanceId}",
                            "Instance Token: {$this->instanceToken}",
                        ],
                    ),
                    code: $e->getCode(),
                )
            );

            return;
        }

        Message::query()->create(
            attributes: [
                'store_id' => $this->storeId,
                'provider_id' => $response['id'],
                'type' => MessageType::IMAGE,
                'mobile' => $this->mobile,
                'body' => $this->caption,
                'status' => MessageStatus::PENDING,
                'attachments' => [
                    [
                        'path' => $this->imagePath,
                        'name' => $name,
                        'url' => $url,
                    ],
                ],
            ],
        );
    }
}
