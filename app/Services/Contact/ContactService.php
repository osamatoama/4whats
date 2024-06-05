<?php

namespace App\Services\Contact;

use App\Dto\ContactDto;
use App\Models\Contact;

class ContactService
{
    public function firstOrCreate(ContactDto $contactDto): Contact
    {
        return Contact::query()
            ->firstOrCreate(
                attributes: [
                    'store_id' => $contactDto->storeId,
                    'provider_type' => $contactDto->providerType,
                    'provider_id' => $contactDto->providerId,
                    'source' => $contactDto->source,
                ],
                values: [
                    'provider_created_at' => $contactDto->providerCreatedAt,
                    'provider_updated_at' => $contactDto->providerUpdatedAt,
                    'first_name' => $contactDto->firstName,
                    'last_name' => $contactDto->lastName,
                    'email' => $contactDto->email,
                    'mobile' => $contactDto->mobile,
                    'gender' => $contactDto->gender,
                ],
            );
    }

    public function updateOrCreate(ContactDto $contactDto): Contact
    {
        return Contact::query()->updateOrCreate(
            attributes: [
                'store_id' => $contactDto->storeId,
                'provider_type' => $contactDto->providerType,
                'provider_id' => $contactDto->providerId,
                'source' => $contactDto->source,
            ],
            values: [
                'provider_created_at' => $contactDto->providerCreatedAt,
                'provider_updated_at' => $contactDto->providerUpdatedAt,
                'first_name' => $contactDto->firstName,
                'last_name' => $contactDto->lastName,
                'email' => $contactDto->email,
                'mobile' => $contactDto->mobile,
                'gender' => $contactDto->gender,
            ],
        );
    }
}
