<?php

namespace App\Jobs\Concerns;

use App\Enums\Jobs\BatchName;
use App\Services\Queue\BatchService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;

trait InteractsWithBatches
{
    use Batchable;

    /**
     * @param  ShouldQueue|ShouldQueue[]  $jobs
     */
    protected function addOrCreateBatch(ShouldQueue|array $jobs, BatchName $batchName, int $storeId): void
    {
        if ($this->batchId !== null) {
            $this->batch()->add(jobs: $jobs);
        } else {
            BatchService::createPendingBatch(
                jobs: $jobs,
                batchName: $batchName,
                storeId: $storeId,
            )->dispatch();
        }
    }
}
