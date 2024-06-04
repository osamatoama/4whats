<?php

namespace App\Enums;

enum SettingKey: string
{
    case SYSTEM_FOUR_WHATS_VOUCHER = 'four_whats.voucher';
    case STORE_ORDER_STATUS_ID_FOR_REVIEW_ORDER_EVENT = 'order_status_id_for_review_order_event';
    case STORE_EMPLOYEES_MOBILES_FOR_NEW_ORDER_EVENT = 'employees_mobiles_for_new_order_event';

    public function label(): string
    {
        return __(key: 'enum.'.__CLASS__.'.'.$this->name);
    }
}
