<?php

namespace App\Jobs\Zid\Pull\Customers;

use App\Dto\ContactDto;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Services\Contact\ContactService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PullCustomerJob implements ShouldQueue
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
        (new ContactService())->updateOrCreate(
            contactDto: ContactDto::fromZid(
                storeId: $this->storeId,
                data: $this->data,
            ),
        );
    }
}
