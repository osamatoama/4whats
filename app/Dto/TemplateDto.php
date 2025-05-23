<?php

namespace App\Dto;

use App\Enums\MessageTemplate;

final readonly class TemplateDto
{
    public function __construct(
        public int $storeId,
        public string $key,
        public string $message,
        public int $delayInSeconds,
        public bool $isEnabled,
    ) {
    }

    public static function fromMessageTemplate(int $storeId, MessageTemplate $messageTemplate): self
    {
        return new self(
            storeId: $storeId,
            key: $messageTemplate->value,
            message: $messageTemplate->defaultMessage(),
            delayInSeconds: $messageTemplate->delayInSeconds(),
            isEnabled: false,
        );
    }

    public static function fromOrderStatusMessageTemplate(int $storeId, int $orderStatusId): self
    {
        $messageTemplate = MessageTemplate::ORDER_STATUSES;

        return new self(
            storeId: $storeId,
            key: MessageTemplate::generateOrderStatusKey(
                orderStatusId: $orderStatusId,
            ),
            message: $messageTemplate->defaultMessage(),
            delayInSeconds: $messageTemplate->delayInSeconds(),
            isEnabled: false,
        );
    }
}
