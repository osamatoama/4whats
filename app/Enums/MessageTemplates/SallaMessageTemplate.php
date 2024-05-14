<?php

namespace App\Enums\MessageTemplates;

enum SallaMessageTemplate: string
{
    case ABANDONED_CART = 'salla.event.abandoned.cart';
    case OTP = 'salla.event.customer.otp.request';
    case CUSTOMER_CREATED = 'salla.event.customer.created';
    case REVIEW_ORDER = 'salla.custom.review_order';
    case COD = 'salla.custom.cod';
    case NEW_ORDER_FOR_EMPLOYEES = 'salla.custom.new_order_for_employees';
    case ORDER_STATUSES = 'salla.order_statuses';

    public function placeholders(): array
    {
        return match ($this) {
            self::ABANDONED_CART => ['CUSTOMER_NAME', 'AMOUNT', 'CURRENCY', 'CHECKOUT_URL'],
            self::OTP => ['OTP'],
            self::CUSTOMER_CREATED => ['CUSTOMER_NAME'],
            self::REVIEW_ORDER => ['REVIEW_URL', 'CUSTOMER_NAME', 'ORDER_ID', 'AMOUNT', 'STATUS', 'CURRENCY'],
            self::COD, self::NEW_ORDER_FOR_EMPLOYEES => ['CUSTOMER_NAME', 'ORDER_ID', 'AMOUNT', 'STATUS', 'CURRENCY'],
            self::ORDER_STATUSES => ['CUSTOMER_NAME', 'ORDER_ID', 'STATUS'],
        };
    }

    public function delayInSeconds(): int
    {
        return match ($this) {
            self::ABANDONED_CART, self::REVIEW_ORDER, self::ORDER_STATUSES => 60 * 60 * 2,
            default => 0,
        };
    }
}
