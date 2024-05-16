<?php

namespace App\Policies;

use App\Models\MessageTemplate;
use App\Models\User;

class MessageTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_merchant || $user->is_employee;
    }

    public function update(User $user, MessageTemplate $messageTemplate): bool
    {
        return ($user->is_merchant || $user->is_employee) && $messageTemplate->store_id === currentStore()->id;
    }
}
