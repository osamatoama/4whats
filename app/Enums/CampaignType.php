<?php

namespace App\Enums;

use App\Models\QueuedJobBatch;

enum CampaignType: string
{
    case CONTACTS = 'contacts';
    case ABANDONED_CARTS = 'abandoned_carts';

    public static function fromQueuedJobBatch(QueuedJobBatch $queuedJobBatch): self
    {
        $name = str(
            string: $queuedJobBatch->name,
        )->after(
            search: '.',
        )->before(
            search: ':',
        )->toString();

        return self::from(
            value: $name,
        );
    }

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
