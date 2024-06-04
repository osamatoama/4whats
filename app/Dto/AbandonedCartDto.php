<?php

namespace App\Dto;

use App\Enums\ProviderType;
use App\Services\Salla\SallaService;
use Illuminate\Support\Carbon;

final readonly class AbandonedCartDto
{
    public function __construct(
        public int $storeId,
        public int $contactId,
        public ProviderType $providerType,
        public int|string $providerId,
        public int $totalAmount,
        public string $totalCurrency,
        public string $checkoutUrl,
        public Carbon $createdAt,
        public Carbon $updatedAt,
    ) {
    }

    public static function fromSalla(int $storeId, int $contactId, array $data): self
    {
        return new self(
            storeId: $storeId,
            contactId: $contactId,
            providerType: ProviderType::SALLA,
            providerId: $data['id'],
            totalAmount: $data['total']['amount'] * 100,
            totalCurrency: $data['total']['currency'],
            checkoutUrl: $data['checkout_url'],
            createdAt: SallaService::parseDate(
                data: $data['created_at'],
            ),
            updatedAt: SallaService::parseDate(
                data: $data['updated_at'],
            ),
        );
    }

    public static function fromZid(int $storeId, int $contactId, array $data): self
    {
        return new self(
            storeId: $storeId,
            contactId: $contactId,
            providerType: ProviderType::ZID,
            providerId: $data['id'],
            totalAmount: $data['cart_total'] * 100,
            totalCurrency: str(string: $data['cart_total_string'])->after(search: ' ')->toString(),
            checkoutUrl: $data['url'],
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at'],
        );
    }
}
