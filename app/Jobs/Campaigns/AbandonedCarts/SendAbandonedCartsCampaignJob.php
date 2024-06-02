<?php

namespace App\Jobs\Campaigns\AbandonedCarts;

use App\Enums\Jobs\BatchName;
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
        public string $message,
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
                            message: $this->message,
                            abandonedCarts: $abandonedCartsChunk,
                        ),
                        name: BatchName::CAMPAIGNS_ABANDONED_CARTS->generate(
                            storeId: $this->store->id,
                        ),
                    );
                },
            );
    }
}
