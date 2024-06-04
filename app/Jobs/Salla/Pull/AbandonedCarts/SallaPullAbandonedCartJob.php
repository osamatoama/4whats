<?php

namespace App\Jobs\Salla\Pull\AbandonedCarts;

use App\Dto\AbandonedCartDto;
use App\Dto\ContactDto;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Services\AbandonedCart\AbandonedCartService;
use App\Services\Contact\ContactService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaPullAbandonedCartJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $storeId,
        public array $data,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $contact = (new ContactService())->firstOrCreate(
            contactDto: ContactDto::fromSallaAbandonedCart(
                storeId: $this->storeId,
                data: $this->data,
            ),
        );

        (new AbandonedCartService())->updateOrCreate(
            abandonedCartDto: AbandonedCartDto::fromSalla(
                storeId: $this->storeId,
                contactId: $contact->id,
                data: $this->data,
            ),
        );
    }
}
