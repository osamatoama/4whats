<?php

namespace App\Enums;

enum CampaignType: string
{
    case CONTACTS = 'contacts';
    case ABANDONED_CARTS = 'abandoned_carts';

    public function label(): string
    {
        return __(
            key: 'enum.'.__CLASS__.'.'.$this->name,
        );
    }

    public function placeholders(): array
    {
        return match ($this) {
            self::CONTACTS => ['{CUSTOMER_NAME}'],
            self::ABANDONED_CARTS => ['{CUSTOMER_NAME}', '{AMOUNT}', '{CURRENCY}', '{CHECKOUT_URL}'],
        };
    }

    public function placeholdersAsString(): string
    {
        return implode(
            separator: ' ',
            array: $this->placeholders(),
        );
    }
}
