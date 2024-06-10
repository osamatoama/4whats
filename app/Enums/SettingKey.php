<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum SettingKey: string
{
    use HasLabel;

    case SYSTEM_FOUR_WHATS_VOUCHER = 'four_whats.voucher';
    case STORE_ORDER_STATUS_ID_FOR_REVIEW_ORDER_MESSAGE = 'order_status_id_for_review_order_message';
    case STORE_EMPLOYEES_MOBILES_FOR_NEW_ORDER_MESSAGE = 'employees_mobiles_for_new_order_message';
}
