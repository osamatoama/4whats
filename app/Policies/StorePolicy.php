<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, Store $store): bool
    {
        return $user->is_admin;
    }

    public function updatePassword(User $user, Store $store): bool
    {
        return $user->is_admin;
    }

    public function extendTrial(User $user, Store $store): bool
    {
        return $user->is_admin;
    }

    public function toggleActivation(User $user, Store $store): bool
    {
        return $user->is_admin;
    }
}
