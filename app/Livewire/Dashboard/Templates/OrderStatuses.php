<?php

namespace App\Livewire\Dashboard\Templates;

use App\Enums\MessageTemplate;
use App\Models\Template;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class OrderStatuses extends Component
{
    public Collection $templates;

    public int $currentTemplateId;

    public Template $currentTemplate;

    public function mount(): void
    {
        $this->currentTemplateId = $this->templates->first()->id;

        $this->currentTemplate = $this->templates->firstWhere(
            key: 'id',
            operator: '=',
            value: $this->currentTemplateId
        );
    }

    public function updated(): void
    {
        $this->validate(rules: [
            'currentTemplateId' => [
                'required',
                'integer', Rule::in(
                    values: $this->templates->pluck(value: 'id'),
                ),
            ],
        ]);

        $this->currentTemplate = $this->templates->firstWhere(
            key: 'id',
            operator: '=',
            value: $this->currentTemplateId
        );
    }

    public function render(): View
    {
        $enum = MessageTemplate::ORDER_STATUSES;

        return view(view: 'livewire.dashboard.templates.order-statuses', data: [
            'label' => $enum->label(),
            'hint' => $enum->hint(),
            'orderStatuses' => currentStore()->orderStatuses,
        ]);
    }
}
