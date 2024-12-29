<?php

namespace App\Enums\Jobs;

enum QueueName: string
{
    case SUBSCRIPTIONS = 'subscriptions';

    case ORDERS = 'orders';

    case CUSTOMERS = 'customers';

    case ABANDONED_CARTS = 'abandoned-carts';
}
