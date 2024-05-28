<?php

namespace App\Livewire\Dashboard\Settings;

use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\Widget;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class WidgetCard extends Component
{
    use InteractsWithToasts;

    public Widget $widget;

    public ?string $message = null;

    public string $color;

    public bool $isEnabled;

    public function updateWidget(): void
    {
        Gate::authorize(ability: 'update', arguments: $this->widget);

        $this->validate(rules: [
            'message' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'hex_color'],
        ]);

        $this->widget->update(attributes: [
            'message' => $this->message,
            'color' => $this->color,
            'is_enabled' => $this->isEnabled,
        ]);

        $this->successToast(action: 'updated', model: 'widgets.singular');
    }

    public function mount(): void
    {
        $this->message = $this->widget->message;
        $this->color = $this->widget->color;
        $this->isEnabled = $this->widget->is_enabled;
    }

    public function render(): View
    {
        return view(view: 'livewire.dashboard.settings.widget-card');
    }
}
