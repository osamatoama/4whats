<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum MessageTemplate: string
{
    case ORDER_STATUSES = 'order_statuses';

    case SALLA_ABANDONED_CART = 'salla.event.abandoned.cart';
    case SALLA_OTP = 'salla.event.customer.otp.request';
    case SALLA_CUSTOMER_CREATED = 'salla.event.customer.created';
    case SALLA_REVIEW_ORDER = 'salla.custom.review_order';
    case SALLA_COD = 'salla.custom.cod';
    case SALLA_DIGITAL_PRODUCT = 'salla.custom.digital_product';
    case SALLA_NEW_ORDER_FOR_EMPLOYEES = 'salla.custom.new_order_for_employees';

    case ZID_ABANDONED_CART = 'zid.event.abandoned_cart.created';
    case ZID_CUSTOMER_CREATED = 'zid.event.customer.create';
    case ZID_COD = 'zid.custom.cod';
    case ZID_DIGITAL_PRODUCT = 'zid.custom.digital_product';
    case ZID_NEW_ORDER_FOR_EMPLOYEES = 'zid.custom.new_order_for_employees';

    public static function sallaCases(): Collection
    {
        return collect(value: self::cases())
            ->filter(callback: function (MessageTemplate $messageTemplate) {
                return $messageTemplate === self::ORDER_STATUSES || str(string: $messageTemplate->name)->startsWith(needles: 'SALLA_');
            });
    }

    public static function zidCases(): Collection
    {
        return collect(value: self::cases())
            ->filter(callback: function (MessageTemplate $messageTemplate) {
                return $messageTemplate === self::ORDER_STATUSES || str(string: $messageTemplate->name)->startsWith(needles: 'ZID_');
            });
    }

    public static function generateOrderStatusKey(int $orderStatusId): string
    {
        return self::ORDER_STATUSES->value.'.'.$orderStatusId;
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
            self::ZID_NEW_ORDER_FOR_EMPLOYEES->value,
        ];
    }

    public function placeholders(): array
    {
        return match ($this) {
            self::ORDER_STATUSES,
            self::SALLA_NEW_ORDER_FOR_EMPLOYEES,
            self::ZID_NEW_ORDER_FOR_EMPLOYEES => [
                '{CUSTOMER_NAME}',
                '{ORDER_ID}',
                '{ORDER_URL}',
                '{AMOUNT}',
                '{STATUS}',
                '{CURRENCY}',
                '{SHIPPING_COMPANY}',
                '{TRACKING_NUMBER}',
                '{TRACKING_URL}',
            ],
            self::SALLA_ABANDONED_CART,
            self::ZID_ABANDONED_CART => [
                '{CUSTOMER_NAME}',
                '{AMOUNT}',
                '{CURRENCY}',
                '{CHECKOUT_URL}',
            ],
            self::SALLA_OTP => [
                '{OTP}',
            ],
            self::SALLA_CUSTOMER_CREATED,
            self::ZID_CUSTOMER_CREATED => [
                '{CUSTOMER_NAME}',
            ],
            self::SALLA_REVIEW_ORDER => [
                '{REVIEW_URL}',
                '{CUSTOMER_NAME}',
                '{ORDER_ID}',
                '{AMOUNT}',
                '{STATUS}',
                '{CURRENCY}',
            ],
            self::SALLA_COD,
            self::ZID_COD => [
                '{CUSTOMER_NAME}',
                '{ORDER_ID}',
                '{AMOUNT}',
                '{STATUS}',
                '{CURRENCY}',
            ],
            self::SALLA_DIGITAL_PRODUCT,
            self::ZID_DIGITAL_PRODUCT => [
                '{CUSTOMER_NAME}',
                '{ORDER_ID}',
                '{PRODUCTS}',
            ],
        };
    }

    public function delayInSeconds(): int
    {
        return match ($this) {
            self::ORDER_STATUSES,
            self::SALLA_ABANDONED_CART,
            self::ZID_ABANDONED_CART,
            self::SALLA_REVIEW_ORDER => 60 * 60 * 2,
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
        return ! in_array(
            needle: $this,
            haystack: [
                self::SALLA_OTP,
                self::SALLA_CUSTOMER_CREATED,
                self::SALLA_COD,
                self::SALLA_DIGITAL_PRODUCT,
                self::SALLA_NEW_ORDER_FOR_EMPLOYEES,

                self::ZID_CUSTOMER_CREATED,
                self::ZID_COD,
                self::ZID_DIGITAL_PRODUCT,
                self::ZID_NEW_ORDER_FOR_EMPLOYEES,
            ],
        );
    }
}
