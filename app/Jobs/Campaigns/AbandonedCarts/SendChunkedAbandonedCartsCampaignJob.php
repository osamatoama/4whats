<?php

namespace App\Jobs\Campaigns\AbandonedCarts;

use App\Enums\Jobs\BatchName;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Jobs\Whatsapp\WhatsappSendTextMessageJob;
use App\Models\AbandonedCart;
use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendChunkedAbandonedCartsCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Store $store,
        public string $message,
        public Collection $abandonedCarts,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $this->abandonedCarts->each(
            callback: function (AbandonedCart $abandonedCart) {
                if (isInBlacklistedMobiles(mobile: $abandonedCart->contact->mobile, store: $this->store)) {
                    return;
                }

                $message = str(
                    string: $this->message,
                )->replace(
                    search: '{CUSTOMER_NAME}',
                    replace: $abandonedCart->contact->name,
                )->replace(
                    search: '{AMOUNT}',
                    replace: $abandonedCart->total_amount,
                )->replace(
                    search: '{CURRENCY}',
                    replace: $abandonedCart->total_currency,
                )->replace(
                    search: '{CHECKOUT_URL}',
                    replace: $abandonedCart->checkout_url,
                )->toString();

                $this->addOrCreateBatch(
                    jobs: new WhatsappSendTextMessageJob(
                        storeId: $this->store->id,
                        instanceId: $this->store->whatsappAccount->instance_id,
                        instanceToken: $this->store->whatsappAccount->instance_token,
                        mobile: $abandonedCart->contact->mobile,
                        message: $message,
                    ),
                    batchName: BatchName::CAMPAIGNS_ABANDONED_CARTS,
                    storeId: $this->store->id,
                );
            },
        );
    }
}
