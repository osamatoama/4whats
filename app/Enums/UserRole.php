<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MERCHANT = 'merchant';
    case EMPLOYEE = 'employee';
}
