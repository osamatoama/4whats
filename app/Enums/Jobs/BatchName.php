<?php

namespace App\Enums\Jobs;

enum BatchName: string
{
    case CAMPAIGNS_CONTACTS = 'campaigns.contacts';
    case CAMPAIGNS_ABANDONED_CARTS = 'campaigns.abandoned_carts';
    case SALLA_PULL_CUSTOMERS = 'salla.pull.customers';
    case SALLA_PULL_ABANDONED_CARTS = 'salla.pull.abandoned-carts';
    case SALLA_PULL_ORDER_STATUSES = 'salla.pull.order-statuses';

    public function generate(int $storeId): string
    {
        return $this->value.':'.$storeId;
    }

    public static function fromBatchName(string $batchName): self
    {
        return self::from(
            value: str(
                string: $batchName,
            )->before(
                search: ':',
            )->toString(),
        );
    }
}
