<?php

namespace App\Policies;

use App\Models\Template;
use App\Models\User;

class TemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_merchant || $user->is_employee;
    }

    public function update(User $user, Template $template): bool
    {
        return ($user->is_merchant || $user->is_employee) && $template->store_id === currentStore()->id;
    }
}
