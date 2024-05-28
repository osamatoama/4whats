<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;

class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function update(User $user, Setting $setting): bool
    {
        return ($user->is_admin && $setting->store_id === null) || (($user->is_merchant || $user->is_employee) && $setting->store_id === currentStore()->id);
    }
}
