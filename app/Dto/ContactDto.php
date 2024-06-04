<?php

namespace App\Dto;

use App\Enums\ContactSource;
use App\Enums\ProviderType;
use App\Services\Salla\SallaService;
use Illuminate\Support\Carbon;

final readonly class ContactDto
{
    public function __construct(
        public int $storeId,
        public ProviderType $providerType,
        public int $providerId,
        public ContactSource $source,
        public string $firstName,
        public ?string $lastName,
        public ?string $email,
        public string $mobile,
        public ?string $gender,
        public Carbon $createdAt,
        public Carbon $updatedAt,
    ) {
    }

    public static function fromSalla(int $storeId, array $data): self
    {
        return new self(
            storeId: $storeId,
            providerType: ProviderType::SALLA,
            providerId: $data['id'],
            source: ContactSource::SALLA,
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            mobile: '+'.$data['mobile_code'].$data['mobile'],
            gender: $data['gender'],
            createdAt: now(),
            updatedAt: SallaService::parseDate(
                data: $data['updated_at'],
            ),
        );
    }

    public static function fromSallaAbandonedCart(int $storeId, array $data): self
    {
        $name = str(string: $data['customer']['name']);

        return new self(
            storeId: $storeId,
            providerType: ProviderType::SALLA,
            providerId: $data['customer']['id'],
            source: ContactSource::SALLA,
            firstName: $name->before(search: ' ')->toString(),
            lastName: $name->after(search: ' ')->toString(),
            email: $data['customer']['email'],
            mobile: $data['customer']['mobile'],
            gender: null,
            createdAt: now(),
            updatedAt: now(),
        );
    }

    public static function fromZid(int $storeId, array $data): self
    {
        $name = str(string: $data['name']);

        return new self(
            storeId: $storeId,
            providerType: ProviderType::ZID,
            providerId: $data['id'],
            source: ContactSource::ZID,
            firstName: $name->before(search: ' ')->toString(),
            lastName: $name->after(search: ' ')->toString(),
            email: $data['email'],
            mobile: $data['mobile'],
            gender: $data['gender'],
            createdAt: now(),
            updatedAt: now(),
        );
    }

    public static function fromZidAbandonedCart(int $storeId, array $data): self
    {
        $name = str(string: $data['customer_name']);

        return new self(
            storeId: $storeId,
            providerType: ProviderType::ZID,
            providerId: $data['customer_id'],
            source: ContactSource::ZID,
            firstName: $name->before(search: ' ')->toString(),
            lastName: $name->after(search: ' ')->toString(),
            email: $data['customer_email'],
            mobile: $data['customer_mobile'],
            gender: null,
            createdAt: now(),
            updatedAt: now(),
        );
    }
}
