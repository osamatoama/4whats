<?php

namespace App\Jobs\Salla\Pull\AbandonedCarts;

use App\Enums\ContactSource;
use App\Enums\ProviderType;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Models\AbandonedCart;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

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
        AbandonedCart::query()->updateOrCreate(attributes: [
            'store_id' => $this->storeId,
            'provider_type' => ProviderType::SALLA,
            'provider_id' => $this->data['id'],
        ], values: [
            'contact_id' => $this->getContactId(),
            'total_amount' => $this->data['total']['amount'] * 100,
            'total_currency' => $this->data['total']['currency'],
            'checkout_url' => $this->data['checkout_url'],
            'created_at' => $this->parseDate(time: $this->data['created_at']['date'], timezone: $this->data['created_at']['timezone']),
            'updated_at' => $this->parseDate(time: $this->data['updated_at']['date'], timezone: $this->data['updated_at']['timezone']),
        ]);
    }

    protected function getContactId(): int
    {
        $name = str(string: $this->data['customer']['name']);

        return Contact::query()
            ->firstOrCreate(attributes: [
                'store_id' => $this->storeId,
                'provider_type' => ProviderType::SALLA,
                'provider_id' => $this->data['customer']['id'],
                'source' => ContactSource::SALLA,
            ], values: [
                'first_name' => $name->before(search: ' ')->toString(),
                'last_name' => $name->after(search: ' ')->toString(),
                'email' => $this->data['customer']['email'],
                'phone' => $this->data['customer']['mobile'],
                'gender' => null,
            ])->id;
    }

    protected function parseDate(string $time, string $timezone): Carbon
    {
        return Carbon::parse(time: $time, timezone: $timezone)->timezone(value: config(key: 'app.timezone'));
    }
}
