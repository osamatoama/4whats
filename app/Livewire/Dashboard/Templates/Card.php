<?php

namespace App\Livewire\Dashboard\Templates;

use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\Template;
use Illuminate\View\View;
use Livewire\Component;

class Card extends Component
{
    use InteractsWithToasts;

    public Template $template;

    public string $message;

    public int $delayInHours;

    public bool $isEnabled;

    public function mount(): void
    {
        $this->message = $this->template->message;
        $this->delayInHours = $this->template->delay_in_hours;
        $this->isEnabled = $this->template->is_enabled;
    }

    public function updated(): void
    {
        $this->authorize(
            ability: 'update',
            arguments: $this->template,
        );

        $this->validate(
            rules: [
                'message' => ['required', 'string', 'max:5000'],
                'delayInHours' => ['required', 'integer', 'min:0', 'max:72'],
            ],
            attributes: [
                'message' => __(
                    key: 'dashboard.pages.templates.columns.message.label',
                ),
                'delayInHours' => __(
                    key: 'dashboard.pages.templates.columns.delay_in_hours.label',
                ),
            ],
        );

        $this->template->update(
            attributes: [
                'message' => $this->message,
                'delay_in_seconds' => $this->delayInHours * 60 * 60,
                'is_enabled' => $this->isEnabled,
            ],
        );

        $this->successToast(
            action: 'updated',
            model: 'templates.singular',
        );
    }

    public function render(): View
    {
        if ($this->template->is_order_status) {
            return view(
                view: 'livewire.dashboard.templates.order-status-card',
            );
        }

        return view(
            view: 'livewire.dashboard.templates.card',
        );
    }
}
