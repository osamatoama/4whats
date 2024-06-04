<?php

namespace App\Jobs\Zid\Pull\OrderStatuses;

use App\Dto\OrderStatusDto;
use App\Dto\TemplateDto;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Services\OrderStatus\OrderStatusService;
use App\Services\Template\TemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PullOrderStatusJob implements ShouldQueue
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
        $orderStatus = (new OrderStatusService())->updateOrCreate(
            orderStatusDto: OrderStatusDto::fromZid(
                storeId: $this->storeId,
                data: $this->data,
            ),
        );

        (new TemplateService())->firstOrCreate(
            templateDto: TemplateDto::fromOrderStatusMessageTemplate(
                storeId: $this->storeId,
                orderStatusId: $orderStatus->id,
            ),
        );
    }
}
