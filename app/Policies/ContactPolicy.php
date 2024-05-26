<?php

namespace App\Policies;

use App\Models\User;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_merchant || $user->is_employee;
    }

    public function export(User $user): bool
    {
        return $user->is_merchant || $user->is_employee;
    }
}
