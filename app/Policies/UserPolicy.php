<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
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

    public function sendCampaigns(User $user): bool
    {
        return $user->is_merchant || $user->is_employee;
    }

    public function viewCampaigns(User $user): bool
    {
        return $user->is_merchant || $user->is_employee;
    }
}
