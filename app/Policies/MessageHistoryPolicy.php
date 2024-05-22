<?php

namespace App\Policies;

use App\Models\User;

class MessageHistoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_merchant || $user->is_employee;
    }
}
