<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;
use Illuminate\Bus\Batch;

enum CampaignType: string
{
    use HasLabel;

    case CONTACTS = 'contacts';
    case ABANDONED_CARTS = 'abandoned_carts';

    public static function fromBatch(Batch $batch): self
    {
        $name = str(
            string: $batch->name,
        )->after(
            search: '.',
        )->before(
            search: ':',
        )->toString();

        return self::from(
            value: $name,
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
