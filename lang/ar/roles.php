<?php

use App\Enums\UserRole;

return [
    UserRole::ADMIN->value => 'مسؤول',
    UserRole::MERCHANT->value => 'تاجر',
    UserRole::EMPLOYEE->value => 'موظف',
];
