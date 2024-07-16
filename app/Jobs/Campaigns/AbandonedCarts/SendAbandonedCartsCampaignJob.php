<?php

namespace App\Jobs\Campaigns\AbandonedCarts;

use App\Enums\Jobs\BatchName;
use App\Enums\Whatsapp\MessageType;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAbandonedCartsCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Store $store,
        public MessageType $messageType,
        public ?string $message,
        public ?string $filePath,
        public ?string $imagePath,
        public ?string $videoPath,
        public ?string $audioPath,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->store->loadMissing(
            relations: ['whatsappAccount'],
        );

        $this->store
            ->abandonedCarts()
            ->with(
                relations: ['contact'],
            )
            ->get()
            ->chunk(
                size: 500,
            )
            ->each(
                callback: function (Collection $abandonedCartsChunk) {
                    $this->addOrCreateBatch(
                        jobs: new SendChunkedAbandonedCartsCampaignJob(
                            store: $this->store,
                            messageType: $this->messageType,
                            message: $this->message,
                            filePath: $this->filePath,
                            imagePath: $this->imagePath,
                            videoPath: $this->videoPath,
                            audioPath: $this->audioPath,
                            abandonedCarts: $abandonedCartsChunk,
                        ),
                        batchName: BatchName::CAMPAIGNS_ABANDONED_CARTS,
                        storeId: $this->store->id,
                    );
                },
            );
    }
}
