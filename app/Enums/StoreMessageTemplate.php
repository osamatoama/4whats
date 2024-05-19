<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum StoreMessageTemplate: string
{
    case ORDER_STATUSES = 'order_statuses';
    case SALLA_ABANDONED_CART = 'salla.event.abandoned.cart';
    case SALLA_OTP = 'salla.event.customer.otp.request';
    case SALLA_CUSTOMER_CREATED = 'salla.event.customer.created';
    case SALLA_REVIEW_ORDER = 'salla.custom.review_order';
    case SALLA_COD = 'salla.custom.cod';
    case SALLA_NEW_ORDER_FOR_EMPLOYEES = 'salla.custom.new_order_for_employees';

    public static function sallaCases(): Collection
    {
        return collect(value: self::cases())
            ->filter(callback: function (StoreMessageTemplate $storeMessageTemplate) {
                return $storeMessageTemplate === self::ORDER_STATUSES || str(string: $storeMessageTemplate->name)->startsWith(needles: 'SALLA_');
            });

    }

    public static function reviewOrderValues(): array
    {
        return [
            self::SALLA_REVIEW_ORDER->value,
        ];
    }

    public static function newOrderForEmployeesValues(): array
    {
        return [
            self::SALLA_NEW_ORDER_FOR_EMPLOYEES->value,
        ];
    }

    public function placeholders(): array
    {
        return match ($this) {
            self::ORDER_STATUSES => ['{CUSTOMER_NAME}', '{ORDER_ID}', '{STATUS}'],
            self::SALLA_ABANDONED_CART => ['{CUSTOMER_NAME}', '{AMOUNT}', '{CURRENCY}', '{CHECKOUT_URL}'],
            self::SALLA_OTP => ['{OTP}'],
            self::SALLA_CUSTOMER_CREATED => ['{CUSTOMER_NAME}'],
            self::SALLA_REVIEW_ORDER => ['{REVIEW_URL}', '{CUSTOMER_NAME}', '{ORDER_ID}', '{AMOUNT}', '{STATUS}', '{CURRENCY}'],
            self::SALLA_COD, self::SALLA_NEW_ORDER_FOR_EMPLOYEES => ['{CUSTOMER_NAME}', '{ORDER_ID}', '{AMOUNT}', '{STATUS}', '{CURRENCY}'],
        };
    }

    public function delayInSeconds(): int
    {
        return match ($this) {
            self::SALLA_ABANDONED_CART, self::SALLA_REVIEW_ORDER, self::ORDER_STATUSES => 60 * 60 * 2,
            default => 0,
        };
    }

    public function defaultMessage(): string
    {
        return __(key: 'enum.'.__CLASS__.'.'.$this->name.'.default');
    }

    public function label(): string
    {
        return __(key: 'enum.'.__CLASS__.'.'.$this->name.'.label');
    }

    public function description(): string
    {
        $placeholders = implode(' ', $this->placeholders());

        return __(key: 'enum.'.__CLASS__.'.'.$this->name.'.description', replace: ['placeholders' => $placeholders]);
    }

    public function hint(): ?string
    {
        $key = 'enum.'.__CLASS__.'.'.$this->name.'.label';
        $value = __(key: $key);

        if ($value === $key) {
            return null;
        }

        return $value;
    }

    public function shouldShowDelay(): bool
    {
        return ! in_array(needle: $this, haystack: [
            self::SALLA_OTP,
            self::SALLA_CUSTOMER_CREATED,
            self::SALLA_COD,
            self::SALLA_NEW_ORDER_FOR_EMPLOYEES,
        ]);
    }
}
