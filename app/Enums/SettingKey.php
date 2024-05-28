<?php

namespace App\Enums;

enum SettingKey: string
{
    case SYSTEM_FOUR_WHATS_VOUCHER = 'four_whats.voucher';
    case STORE_SALLA_CUSTOM_REVIEW_ORDER = MessageTemplate::SALLA_REVIEW_ORDER->value;
    case STORE_SALLA_CUSTOM_NEW_ORDER_FOR_EMPLOYEES = MessageTemplate::SALLA_NEW_ORDER_FOR_EMPLOYEES->value;

    public function label(): string
    {
        return __(key: 'enum.'.__CLASS__.'.'.$this->name);
    }
}
