<?php

namespace App\Jobs\Salla\Pull\OrderStatuses;

use App\Enums\MessageTemplates\SallaMessageTemplate;
use App\Enums\ProviderType;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Models\OrderStatus;
use App\Models\Store;
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
        $store = Store::query()->find(id: $this->storeId);

        $orderStatus = $store->orderStatuses()->updateOrCreate(attributes: [
            'store_id' => $this->storeId,
            'provider_type' => ProviderType::SALLA,
            'provider_id' => $this->data['id'],
        ], values: [
            'order_status_id' => $this->getOrderStatusId(),
            'name' => $this->data['name'],
        ]);

        $messageTemplateEnum = SallaMessageTemplate::ORDER_STATUSES;
        $store->messageTemplates()->firstOrCreate(attributes: [
            'key' => $messageTemplateEnum->value.'.'.$orderStatus->id,
        ], values: [
            'message' => 'من فضلك قم بتغيير نص الرسالة قبل التفعيل',
            'placeholders' => $messageTemplateEnum->placeholders(),
            'delay_in_seconds' => $messageTemplateEnum->delayInSeconds(),
        ]);
    }

    protected function getOrderStatusId(): ?int
    {
        if ($this->data['parent'] === null) {
            return null;
        }

        return OrderStatus::query()->firstOrCreate(attributes: [
            'store_id' => $this->storeId,
            'provider_type' => ProviderType::SALLA,
            'provider_id' => $this->data['parent']['id'],
        ], values: [
            'name' => $this->data['parent']['name'],
        ])->id;
    }
}
