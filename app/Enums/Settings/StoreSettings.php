<?php

namespace App\Enums\Settings;

enum StoreSettings: string implements SettingsEnum
{
    case SALLA_CUSTOM_REVIEW_ORDER = 'salla.custom.review_order.status_id';
    case SALLA_CUSTOM_NEW_ORDER_FOR_EMPLOYEES = 'salla.custom.new_order_for_employees.mobiles';
}
