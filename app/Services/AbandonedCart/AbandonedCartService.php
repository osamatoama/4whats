<?php

namespace App\Services\AbandonedCart;

use App\Dto\AbandonedCartDto;
use App\Models\AbandonedCart;

class AbandonedCartService
{
    public function updateOrCreate(AbandonedCartDto $abandonedCartDto): AbandonedCart
    {
        return AbandonedCart::query()
            ->updateOrCreate(
                attributes: [
                    'store_id' => $abandonedCartDto->storeId,
                    'provider_type' => $abandonedCartDto->providerType,
                    'provider_id' => $abandonedCartDto->providerId,
                ],
                values: [
                    'contact_id' => $abandonedCartDto->contactId,
                    'total_amount' => $abandonedCartDto->totalAmount,
                    'total_currency' => $abandonedCartDto->totalCurrency,
                    'checkout_url' => $abandonedCartDto->checkoutUrl,
                    'created_at' => $abandonedCartDto->createdAt,
                    'updated_at' => $abandonedCartDto->updatedAt,
                ],
            );
    }
}
