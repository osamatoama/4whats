<?php

namespace App\Dto;

use App\Enums\ProviderType;

final readonly class OrderStatusDto
{
    public function __construct(
        public ?int $orderStatusId,
        public int $storeId,
        public ProviderType $providerType,
        public int|string $providerId,
        public string $name,
    ) {
    }

    public static function fromSalla(?int $orderStatusId, int $storeId, array $data): self
    {
        return new self(
            orderStatusId: $orderStatusId,
            storeId: $storeId,
            providerType: ProviderType::SALLA,
            providerId: $data['id'],
            name: $data['name'],
        );
    }

    public static function fromSallaParent(int $storeId, array $data): self
    {
        return new self(
            orderStatusId: null,
            storeId: $storeId,
            providerType: ProviderType::SALLA,
            providerId: $data['parent']['id'],
            name: $data['parent']['name'],
        );
    }

    public static function fromZid(int $storeId, array $data): self
    {
        return new self(
            orderStatusId: null,
            storeId: $storeId,
            providerType: ProviderType::ZID,
            providerId: $data['id'],
            name: $data['name'],
        );
    }
}
