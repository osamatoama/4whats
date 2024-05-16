<?php

namespace App\Enums\Settings;

use App\Enums\StoreMessageTemplate;

enum StoreSettings: string implements SettingsEnum
{
    case SALLA_CUSTOM_REVIEW_ORDER = StoreMessageTemplate::SALLA_REVIEW_ORDER->value;
    case SALLA_CUSTOM_NEW_ORDER_FOR_EMPLOYEES = StoreMessageTemplate::SALLA_NEW_ORDER_FOR_EMPLOYEES->value;
}
