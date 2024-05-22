<?php

namespace App\Policies;

use App\Models\User;

class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_merchant || $user->is_employee;
    }
}
