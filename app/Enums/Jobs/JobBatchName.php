<?php

namespace App\Enums\Jobs;

enum JobBatchName: string
{
    case SALLA_PULL_CUSTOMERS = 'salla.pull.customers';
    case SALLA_PULL_ABANDONED_CARTS = 'salla.pull.abandoned-carts';
    case SALLA_PULL_ORDER_STATUSES = 'salla.pull.order-statuses';

    public function generate(int $storeId): string
    {
        return $this->value.':'.$storeId;
    }
}
