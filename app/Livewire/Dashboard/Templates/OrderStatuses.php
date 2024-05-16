<?php

namespace App\Livewire\Dashboard\Templates;

use App\Enums\StoreMessageTemplate;
use App\Models\MessageTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class OrderStatuses extends Component
{
    public Collection $templates;

    public int $currentTemplateId;

    public MessageTemplate $currentTemplate;

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
        $this->currentTemplate = $this->templates->firstWhere(
            key: 'id',
            operator: '=',
            value: $this->currentTemplateId
        );
    }

    public function render(): View
    {
        $enum = StoreMessageTemplate::ORDER_STATUSES;

        return view(view: 'livewire.dashboard.templates.order-statuses', data: [
            'label' => $enum->label(),
            'hint' => $enum->hint(),
            'orderStatuses' => currentStore()->orderStatuses,
        ]);
    }
}
