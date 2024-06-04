<?php

namespace App\Services\OrderStatus;

use App\Dto\OrderStatusDto;
use App\Models\OrderStatus;

class OrderStatusService
{
    public function firstOrCreate(OrderStatusDto $orderStatusDto): OrderStatus
    {
        return OrderStatus::query()
            ->firstOrCreate(
                attributes: [
                    'store_id' => $orderStatusDto->storeId,
                    'provider_type' => $orderStatusDto->providerType,
                    'provider_id' => $orderStatusDto->providerId,
                ],
                values: [
                    'order_status_id' => $orderStatusDto->orderStatusId,
                    'name' => $orderStatusDto->name,
                ],
            );
    }

    public function updateOrCreate(OrderStatusDto $orderStatusDto): OrderStatus
    {
        return OrderStatus::query()
            ->updateOrCreate(
                attributes: [
                    'store_id' => $orderStatusDto->storeId,
                    'provider_type' => $orderStatusDto->providerType,
                    'provider_id' => $orderStatusDto->providerId,
                ],
                values: [
                    'order_status_id' => $orderStatusDto->orderStatusId,
                    'name' => $orderStatusDto->name,
                ],
            );
    }
}
