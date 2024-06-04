<?php

namespace App\Jobs\Zid\Pull\OrderStatuses;

use App\Enums\Jobs\BatchName;
use App\Jobs\Concerns\InteractsWithBatches;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PullOrderStatusesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $storeId,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $orderStatuses = [
            [
                'id' => 'new',
                'name' => 'جديد',
            ],
            [
                'id' => 'preparing',
                'name' => 'جاري التجهيز',
            ],
            [
                'id' => 'ready',
                'name' => 'جاهز',
            ],
            [
                'id' => 'indelivery',
                'name' => 'جاري التوصيل',
            ],
            [
                'id' => 'delivered',
                'name' => 'مكتمل',
            ],
            [
                'id' => 'cancelled',
                'name' => 'ملغي',
            ],
        ];

        $jobs = [];
        foreach ($orderStatuses as $orderStatus) {
            $jobs[] = new PullOrderStatusJob(
                storeId: $this->storeId,
                data: $orderStatus,
            );
        }

        $this->addOrCreateBatch(
            jobs: $jobs,
            batchName: BatchName::ZID_PULL_ORDER_STATUSES,
            storeId: $this->storeId,
        );
    }
}
