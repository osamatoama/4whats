<?php

namespace App\Dto;

use App\Enums\ProviderType;
use App\Services\Zid\OAuth\Support\Store as ZidStore;

final readonly class StoreDto
{
    public function __construct(
        public int $userId,
        public ProviderType $providerType,
        public int $providerId,
        public ?string $providerUUID,
        public string $name,
        public string $mobile,
        public string $email,
        public string $url,
    ) {
    }

    public static function fromSalla(int $userId, array $sallaStore): self
    {
        return new self(
            userId: $userId,
            providerType: ProviderType::SALLA,
            providerId: $sallaStore['merchant']['id'],
            providerUUID: null,
            name: $sallaStore['merchant']['name'],
            mobile: $sallaStore['mobile'],
            email: $sallaStore['email'],
            url: $sallaStore['merchant']['domain'],
        );
    }

    public static function fromZid(int $userId, ZidStore $zidStore): self
    {
        return new self(
            userId: $userId,
            providerType: ProviderType::ZID,
            providerId: $zidStore->id,
            providerUUID: $zidStore->uuid,
            name: $zidStore->name,
            mobile: $zidStore->mobile,
            email: $zidStore->email,
            url: $zidStore->url,
        );
    }
}
