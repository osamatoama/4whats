<?php

namespace App\Services\Store;

use App\Dto\StoreDto;
use App\Models\Store;

class StoreService
{
    public function create(StoreDto $storeDto): Store
    {
        return Store::query()->create(
            attributes: [
                'user_id' => $storeDto->userId,
                'provider_type' => $storeDto->providerType,
                'provider_id' => $storeDto->providerId,
                'provider_uuid' => $storeDto->providerUUID,
                'name' => $storeDto->name,
                'mobile' => $storeDto->mobile,
                'email' => $storeDto->email,
                'domain' => $storeDto->url,
            ],
        );
    }
}
