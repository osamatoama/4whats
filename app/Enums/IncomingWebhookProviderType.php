<?php

namespace App\Enums;

enum IncomingWebhookProviderType: string
{
    case SALLA = 'salla';
    case ZID = 'zid';
    case FOUR_WHATS = 'four_whats';
}
