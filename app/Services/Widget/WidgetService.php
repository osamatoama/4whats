<?php

namespace App\Services\Widget;

use App\Dto\WidgetDto;
use App\Enums\ProviderType;
use App\Jobs\Salla\Push\Settings\SallaPushWidgetJob;
use App\Models\Store;
use App\Models\Widget;

class WidgetService
{
    public function create(WidgetDto $widgetDto): Widget
    {
        return Widget::query()->create(
            attributes: [
                'store_id' => $widgetDto->storeId,
                'mobile' => $widgetDto->mobile,
                'message' => $widgetDto->message,
                'color' => $widgetDto->color,
                'is_enabled' => $widgetDto->isEnabled,
            ],
        );
    }

    public function update(Widget $widget, WidgetDto $widgetDto, bool $updateSallaWidget = true, ?Store $store = null): Widget
    {
        $widget->update(
            attributes: [
                'store_id' => $widgetDto->storeId,
                'mobile' => $widgetDto->mobile,
                'message' => $widgetDto->message,
                'color' => $widgetDto->color,
                'is_enabled' => $widgetDto->isEnabled,
            ],
        );

        if ($updateSallaWidget) {
            $store ??= $widget->store;
            if ($store->provider_type === ProviderType::SALLA) {
                SallaPushWidgetJob::dispatch(
                    accessToken: currentStore()->user->sallaToken->access_token,
                    storeId: currentStore()->id,
                    widgetDto: $widgetDto,
                );
            }
        }

        return $widget;
    }
}
