<?php

namespace App\Jobs\Campaigns\Contacts;

use App\Enums\Jobs\BatchName;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Jobs\Whatsapp\WhatsappSendTextMessageJob;
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
        public string $message,
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

        $this->contacts->each(
            callback: function (Contact $contact) {
                if (isInBlacklistedMobiles(mobile: $contact->mobile, store: $this->store)) {
                    return;
                }

                $message = str(
                    string: $this->message,
                )->replace(
                    search: '{CUSTOMER_NAME}',
                    replace: $contact->name,
                )->toString();

                $this->addOrCreateBatch(
                    jobs: new WhatsappSendTextMessageJob(
                        storeId: $this->store->id,
                        instanceId: $this->store->whatsappAccount->instance_id,
                        instanceToken: $this->store->whatsappAccount->instance_token,
                        mobile: $contact->mobile,
                        message: $message,
                    ),
                    name: BatchName::CAMPAIGNS_CONTACTS->generate(
                        storeId: $this->store->id,
                    ),
                );
            },
        );
    }
}
