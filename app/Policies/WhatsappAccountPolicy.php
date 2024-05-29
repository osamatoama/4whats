<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WhatsappAccount;

class WhatsappAccountPolicy
{
    public function connect(User $user, WhatsappAccount $whatsappAccount): bool
    {
        return $user->is_merchant && $whatsappAccount->store_id === currentStore()->id;
    }

    public function disconnect(User $user, WhatsappAccount $whatsappAccount): bool
    {
        return $user->is_merchant && $whatsappAccount->store_id === currentStore()->id;
    }

    public function disableSending(User $user, WhatsappAccount $whatsappAccount): bool
    {
        return $user->is_merchant && $whatsappAccount->store_id === currentStore()->id;
    }

    public function enableSending(User $user, WhatsappAccount $whatsappAccount): bool
    {
        return $user->is_merchant && $whatsappAccount->store_id === currentStore()->id;
    }
}
