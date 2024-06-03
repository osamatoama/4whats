<?php

namespace App\Services\Template;

use App\Dto\TemplateDto;
use App\Enums\MessageTemplate;
use App\Models\Template;

class TemplateService
{
    public function create(TemplateDto $templateDto): Template
    {
        return Template::query()->create(
            attributes: [
                'store_id' => $templateDto->storeId,
                'key' => $templateDto->key,
                'message' => $templateDto->message,
                'delay_in_seconds' => $templateDto->delayInSeconds,
                'is_enabled' => $templateDto->isEnabled,
            ],
        );
    }

    /**
     * @param  MessageTemplate[]  $messageTemplates
     * @return array<Template>
     */
    public function bulkCreate(int $storeId, array $messageTemplates, bool $escapeOrderStatuses = true): array
    {
        $createdTemplates = [];

        foreach ($messageTemplates as $messageTemplate) {
            if ($escapeOrderStatuses && $messageTemplate === MessageTemplate::ORDER_STATUSES) {
                continue;
            }

            $createdTemplates[] = $this->create(
                templateDto: TemplateDto::fromMessageTemplate(
                    storeId: $storeId,
                    messageTemplate: $messageTemplate,
                ),
            );
        }

        return $createdTemplates;
    }
}
