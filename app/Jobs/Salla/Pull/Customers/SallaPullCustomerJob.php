<?php

namespace App\Jobs\Salla\Pull\Customers;

use App\Enums\ContactSource;
use App\Enums\ProviderType;
use App\Models\Contact;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaPullCustomerJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        Contact::query()->updateOrCreate(attributes: [
            'store_id' => $this->storeId,
            'provider_type' => ProviderType::SALLA,
            'provider_id' => $this->data['id'],
            'source' => ContactSource::SALLA,
        ], values: [
            'first_name' => $this->data['first_name'],
            'last_name' => $this->data['last_name'],
            'email' => $this->data['email'],
            'phone' => $this->data['mobile_code'].$this->data['mobile'],
            'gender' => $this->data['gender'] ?: null,
        ]);
    }
}
