<?php

namespace App\Jobs\Campaigns\Contacts;

use App\Enums\Jobs\BatchName;
use App\Enums\Jobs\QueueName;
use App\Enums\Whatsapp\MessageType;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Jobs\Whatsapp\WhatsappSendAudioMessageJob;
use App\Jobs\Whatsapp\WhatsappSendFileMessageJob;
use App\Jobs\Whatsapp\WhatsappSendImageMessageJob;
use App\Jobs\Whatsapp\WhatsappSendTextMessageJob;
use App\Jobs\Whatsapp\WhatsappSendVideoMessageJob;
use App\Models\Contact;
use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendChunkedContactsCampaignJob implements ShouldQueue
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
        public Collection $contacts,
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

        foreach ($this->contacts as $index =>$contact) {
            if (isInBlacklistedMobiles(mobile: $contact->mobile, store: $this->store)) {
                return;
            }

            $message = $this->message === null ? null : str(
                string: $this->message,
            )->replace(
                search: '{CUSTOMER_NAME}',
                replace: $contact->name,
            )->toString();

            $job = match ($this->messageType) {
                MessageType::TEXT => new WhatsappSendTextMessageJob(
                    storeId: $this->store->id,
                    instanceId: $this->store->whatsappAccount->instance_id,
                    instanceToken: $this->store->whatsappAccount->instance_token,
                    mobile: $contact->mobile,
                    message: $message,
                ),
                MessageType::FILE => new WhatsappSendFileMessageJob(
                    storeId: $this->store->id,
                    instanceId: $this->store->whatsappAccount->instance_id,
                    instanceToken: $this->store->whatsappAccount->instance_token,
                    mobile: $contact->mobile,
                    filePath: $this->filePath,
                    caption: $this->message,
                ),
                MessageType::IMAGE => new WhatsappSendImageMessageJob(
                    storeId: $this->store->id,
                    instanceId: $this->store->whatsappAccount->instance_id,
                    instanceToken: $this->store->whatsappAccount->instance_token,
                    mobile: $contact->mobile,
                    imagePath: $this->imagePath,
                    caption: $message,
                ),
                MessageType::VIDEO => new WhatsappSendVideoMessageJob(
                    storeId: $this->store->id,
                    instanceId: $this->store->whatsappAccount->instance_id,
                    instanceToken: $this->store->whatsappAccount->instance_token,
                    mobile: $contact->mobile,
                    videoPath: $this->videoPath,
                    caption: $message,
                ),
                MessageType::AUDIO => new WhatsappSendAudioMessageJob(
                    storeId: $this->store->id,
                    instanceId: $this->store->whatsappAccount->instance_id,
                    instanceToken: $this->store->whatsappAccount->instance_token,
                    mobile: $contact->mobile,
                    audioPath: $this->audioPath,
                ),
            };

            // Add a delay to spread out messages
            $delay = now()->addSeconds($index * config('queue.job-delay.whatsapp-message-delay')); // Gap between messages in seconds
            $job->delay($delay);

            $this->addOrCreateBatch(
                jobs: $job,
                batchName: BatchName::CAMPAIGNS_CONTACTS,
                storeId: $this->store->id,
                queueName: QueueName::OTHERS->value
            );
        }

    }
}
