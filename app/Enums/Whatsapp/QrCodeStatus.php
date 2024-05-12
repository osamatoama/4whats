<?php

namespace App\Enums\Whatsapp;

enum QrCodeStatus: string
{
    case GOT_QR_CODE = 'got_qr_code';
    case AUTHENTICATED = 'authenticated';
}
