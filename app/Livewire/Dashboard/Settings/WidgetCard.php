<?php

namespace App\Livewire\Dashboard\Settings;

use App\Dto\WidgetDto;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\Widget;
use App\Services\Widget\WidgetService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class WidgetCard extends Component
{
    use InteractsWithToasts;

    public Widget $widget;

    public string $mobile;

    public ?string $message = null;

    public string $color;

    public bool $isEnabled;

    public function updateWidget(): void
    {
        Gate::authorize(
            ability: 'update',
            arguments: $this->widget,
        );

        $this->validate(
            rules: [
                'mobile' => ['required', 'string', 'max:255'],
                'message' => ['nullable', 'string', 'max:255'],
                'color' => ['required', 'hex_color'],
            ],
        );

        (new WidgetService())->update(
            widget: $this->widget,
            widgetDto: new WidgetDto(
                storeId: $this->widget->store_id,
                mobile: $this->mobile,
                message: $this->message,
                color: $this->color,
                isEnabled: $this->isEnabled,
            ),
            store: currentStore(),
        );

        $this->successToast(
            action: 'updated',
            model: 'widgets.singular',
        );
    }

    public function mount(): void
    {
        $this->mobile = $this->widget->mobile;
        $this->message = $this->widget->message;
        $this->color = $this->widget->color;
        $this->isEnabled = $this->widget->is_enabled;
    }

    public function render(): View
    {
        return view(
            view: 'livewire.dashboard.settings.widget-card',
        );
    }
}
