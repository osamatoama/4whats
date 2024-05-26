<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user): bool
    {
        return $user->is_admin;
    }

    public function viewAnyEmployee(User $user): bool
    {
        return $user->is_merchant;
    }

    public function createEmployee(User $user): bool
    {
        return $user->is_merchant;
    }

    public function deleteEmployee(User $user, User $model): bool
    {
        return $user->is_merchant && $model->user_id === $user->id;
    }
}
