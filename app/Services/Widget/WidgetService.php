<?php

namespace App\Services\Widget;

use App\Dto\WidgetDto;
use App\Models\Widget;

class WidgetService
{
    public function create(WidgetDto $widgetDto): Widget
    {
        return Widget::query()->create(
            attributes: [
                'store_id' => $widgetDto->storeId,
                'message' => $widgetDto->message,
                'color' => $widgetDto->color,
                'is_enabled' => $widgetDto->isEnabled,
            ],
        );
    }
}
