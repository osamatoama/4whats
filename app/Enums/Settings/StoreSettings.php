<?php

namespace App\Enums\Settings;

use App\Enums\MessageTemplates\SallaMessageTemplate;

enum StoreSettings: string implements SettingsEnum
{
    case SALLA_CUSTOM_REVIEW_ORDER = SallaMessageTemplate::REVIEW_ORDER->value;
    case SALLA_CUSTOM_NEW_ORDER_FOR_EMPLOYEES = SallaMessageTemplate::NEW_ORDER_FOR_EMPLOYEES->value;
}
