<?php

namespace App\Policies;

use App\Models\Contact;
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

    public function addToBlacklist(User $user, Contact $contact): bool
    {
        return ($user->is_merchant || $user->is_employee) && $contact->store_id === currentStore()->id;
    }

    public function removeFromBlacklist(User $user, Contact $contact): bool
    {
        return ($user->is_merchant || $user->is_employee) && $contact->store_id === currentStore()->id;
    }
}
