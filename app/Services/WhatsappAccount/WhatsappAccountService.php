<?php

namespace App\Services\WhatsappAccount;

use App\Dto\WhatsappAccountDto;
use App\Models\WhatsappAccount;

class WhatsappAccountService
{
    public function create(WhatsappAccountDto $whatsappAccountDto): WhatsappAccount
    {
        return WhatsappAccount::query()->create(
            attributes: [
                'store_id' => $whatsappAccountDto->storeId,
                'label' => $whatsappAccountDto->label,
                'connected_mobile' => $whatsappAccountDto->connectedMobile,
                'instance_id' => $whatsappAccountDto->instanceId,
                'instance_token' => $whatsappAccountDto->instanceToken,
                'is_sending_enabled' => $whatsappAccountDto->isSendingEnabled,
                'expired_at' => $whatsappAccountDto->expiredAt,
            ],
        );
    }
}
