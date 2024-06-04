<?php

namespace App\Enums\Jobs;

enum BatchName: string
{
    case CAMPAIGNS_CONTACTS = 'campaigns.contacts';
    case CAMPAIGNS_ABANDONED_CARTS = 'campaigns.abandoned-carts';
    case SALLA_INSTALLATION = 'salla.installation';
    case SALLA_PULL_CUSTOMERS = 'salla.pull.customers';
    case SALLA_PULL_ABANDONED_CARTS = 'salla.pull.abandoned-carts';
    case SALLA_PULL_ORDER_STATUSES = 'salla.pull.order-statuses';
    case ZID_INSTALLATION = 'zid.installation';
    case ZID_PULL_ABANDONED_CARTS = 'zid.pull.abandoned-carts';
    case ZID_PULL_CUSTOMERS = 'zid.pull.customers';
    case ZID_PULL_ORDER_STATUSES = 'zid.pull.order-statuses';

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
