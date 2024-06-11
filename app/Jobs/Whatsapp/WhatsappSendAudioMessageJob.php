<?php

namespace App\Jobs\Whatsapp;

use App\Enums\Whatsapp\MessageStatus;
use App\Enums\Whatsapp\MessageType;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Jobs\Concerns\InteractsWithException;
use App\Models\Message;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class WhatsappSendAudioMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $storeId,
        public int $instanceId,
        public string $instanceToken,
        public string $mobile,
        public string $audioPath,
    ) {
        $this->maxAttempts = 5;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $name = str(string: $this->audioPath)->afterLast(search: '/')->toString();
        $url = Storage::url(
            path: $this->audioPath,
        );

        $service = new FourWhatsService();

        try {
            $response = $service->sending(
                instanceId: $this->instanceId,
                instanceToken: $this->instanceToken,
            )->ppt(
                mobile: $this->mobile,
                fileUrl: $url,
            );
        } catch (FourWhatsException $e) {
            $this->handleException(
                e: new FourWhatsException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Error while sending whatsapp audio message',
                            "Message {$e->getMessage()}",
                            "AudioPath: {$this->audioPath}",
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
                'type' => MessageType::AUDIO,
                'mobile' => $this->mobile,
                'body' => null,
                'status' => MessageStatus::PENDING,
                'attachments' => [
                    [
                        'path' => $this->audioPath,
                        'name' => $name,
                        'url' => $url,
                    ],
                ],
            ],
        );
    }
}
