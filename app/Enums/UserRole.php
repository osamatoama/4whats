<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;
use Spatie\Permission\Models\Role;

enum UserRole: string
{
    use HasLabel;

    case ADMIN = 'admin';
    case MERCHANT = 'merchant';
    case EMPLOYEE = 'employee';

    public function guardName(): string
    {
        return 'dashboard';
    }

    public function asModel()
    {
        return once(
            callback: fn (): Role => Role::query()
                ->where(
                    column: 'name',
                    operator: '=',
                    value: $this->value,
                )->where(
                    column: 'guard_name',
                    operator: '=',
                    value: $this->guardName(),
                )
                ->first(),
        );
    }
}
