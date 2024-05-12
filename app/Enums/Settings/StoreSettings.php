<?php

namespace App\Enums\Settings;

enum StoreSettings: string implements SettingsEnum
{
    case MESSAGES_ABANDONED_CARTS = 'messages.abandoned_carts';
    case MESSAGES_OTP = 'messages.otp';
}
