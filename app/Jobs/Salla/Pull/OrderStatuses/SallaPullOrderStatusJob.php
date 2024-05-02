<?php

namespace App\Jobs\Salla\Pull\OrderStatuses;

use App\Enums\ProviderType;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Models\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaPullOrderStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        public array $data,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        OrderStatus::query()->updateOrCreate(attributes: [
            'user_id' => $this->userId,
            'provider_type' => ProviderType::SALLA,
            'provider_id' => $this->data['id'],
        ], values: [
            'order_status_id' => $this->data['parent'] === null ? null : $this->getOrderStatusId(),
            'name' => $this->data['name'],
        ]);
    }

    protected function getOrderStatusId(): int
    {
        return OrderStatus::query()->firstOrCreate(attributes: [
            'user_id' => $this->userId,
            'provider_type' => ProviderType::SALLA,
            'provider_id' => $this->data['parent']['id'],
        ], values: [
            'name' => $this->data['parent']['name'],
        ])->id;
    }
}
