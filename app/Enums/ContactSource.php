<?php

namespace App\Enums;

enum ContactSource: string
{
    case MANUAL = 'manual';
    case SALLA = 'salla';
    case ZID = 'zid';
}
