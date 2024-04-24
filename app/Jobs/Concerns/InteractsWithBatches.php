<?php

namespace App\Jobs\Concerns;

use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Bus;

trait InteractsWithBatches
{
    use Batchable;

    protected function addOrCreateBatch(array $jobs, string $name): void
    {
        if ($this->batchId !== null) {
            $this->batch()->add(jobs: $jobs);
        } else {
            Bus::batch(jobs: [$jobs])->name(name: $name)->dispatch();
        }
    }
}
