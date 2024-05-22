<?php

namespace App\Enums\Settings;

use App\Enums\MessageTemplate;

enum StoreSettings: string implements SettingsEnum
{
    case SALLA_CUSTOM_REVIEW_ORDER = MessageTemplate::SALLA_REVIEW_ORDER->value;
    case SALLA_CUSTOM_NEW_ORDER_FOR_EMPLOYEES = MessageTemplate::SALLA_NEW_ORDER_FOR_EMPLOYEES->value;
}
