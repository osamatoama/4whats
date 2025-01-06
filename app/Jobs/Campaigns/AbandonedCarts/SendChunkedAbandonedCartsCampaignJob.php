<?php

namespace App\Jobs\Campaigns\AbandonedCarts;

use App\Enums\Jobs\BatchName;
use App\Enums\Jobs\QueueName;
use App\Enums\Whatsapp\MessageType;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Jobs\Whatsapp\WhatsappSendAudioMessageJob;
use App\Jobs\Whatsapp\WhatsappSendFileMessageJob;
use App\Jobs\Whatsapp\WhatsappSendImageMessageJob;
use App\Jobs\Whatsapp\WhatsappSendTextMessageJob;
use App\Jobs\Whatsapp\WhatsappSendVideoMessageJob;
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
        public MessageType $messageType,
        public ?string $message,
        public ?string $filePath,
        public ?string $imagePath,
        public ?string $videoPath,
        public ?string $audioPath,
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

        foreach ($this->abandonedCarts as $index => $abandonedCart) {
            if (isInBlacklistedMobiles(mobile: $abandonedCart->contact->mobile, store: $this->store)) {
                return;
            }

            $message = $this->message === null ? null : str(
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

            $job = match ($this->messageType) {
                MessageType::TEXT => new WhatsappSendTextMessageJob(
                    storeId: $this->store->id,
                    instanceId: $this->store->whatsappAccount->instance_id,
                    instanceToken: $this->store->whatsappAccount->instance_token,
                    mobile: $abandonedCart->contact->mobile,
                    message: $message,
                ),
                MessageType::FILE => new WhatsappSendFileMessageJob(
                    storeId: $this->store->id,
                    instanceId: $this->store->whatsappAccount->instance_id,
                    instanceToken: $this->store->whatsappAccount->instance_token,
                    mobile: $abandonedCart->contact->mobile,
                    filePath: $this->filePath,
                    caption: $this->message,
                ),
                MessageType::IMAGE => new WhatsappSendImageMessageJob(
                    storeId: $this->store->id,
                    instanceId: $this->store->whatsappAccount->instance_id,
                    instanceToken: $this->store->whatsappAccount->instance_token,
                    mobile: $abandonedCart->contact->mobile,
                    imagePath: $this->imagePath,
                    caption: $message,
                ),
                MessageType::VIDEO => new WhatsappSendVideoMessageJob(
                    storeId: $this->store->id,
                    instanceId: $this->store->whatsappAccount->instance_id,
                    instanceToken: $this->store->whatsappAccount->instance_token,
                    mobile: $abandonedCart->contact->mobile,
                    videoPath: $this->videoPath,
                    caption: $message,
                ),
                MessageType::AUDIO => new WhatsappSendAudioMessageJob(
                    storeId: $this->store->id,
                    instanceId: $this->store->whatsappAccount->instance_id,
                    instanceToken: $this->store->whatsappAccount->instance_token,
                    mobile: $abandonedCart->contact->mobile,
                    audioPath: $this->audioPath,
                ),
            };

            // Add a delay to spread out messages
            $delay = now()->addSeconds($index * config('queue.job-delay.whatsapp-message-delay')); // Gap between messages in seconds
            $job->delay($delay);

            $this->addOrCreateBatch(
                jobs: $job,
                batchName: BatchName::CAMPAIGNS_ABANDONED_CARTS,
                storeId: $this->store->id,
                queueName: QueueName::OTHERS->value,
            );
        }

    }
}
