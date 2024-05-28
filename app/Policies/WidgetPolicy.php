<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Widget;

class WidgetPolicy
{
    public function view(User $user, Widget $widget): bool
    {
        return ($user->is_merchant || $user->is_employee) && $widget->store_id === currentStore()->id;
    }

    public function update(User $user, Widget $widget): bool
    {
        return ($user->is_merchant || $user->is_employee) && $widget->store_id === currentStore()->id;
    }
}
