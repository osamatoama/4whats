<?php

namespace App\Livewire\Dashboard\Templates;

use App\Enums\Settings\StoreSettings;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\MessageTemplate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class ReviewOrder extends Component
{
    use InteractsWithToasts;

    public MessageTemplate $template;

    public int $orderStatusId;

    public function mount(): void
    {
        $this->orderStatusId = settings(storeId: currentStore()->id)
            ->value(
                key: StoreSettings::from(value: $this->template->key),
            );
    }

    public function updated(): void
    {
        $this->authorize(ability: 'update', arguments: $this->template);

        $this->validate(rules: [
            'orderStatusId' => [
                'required',
                'integer',
                Rule::exists(table: 'order_statuses', column: 'id')->where(column: 'store_id', value: currentStore()->id),
            ],
        ]);

        settings(storeId: currentStore()->id)
            ->find(
                key: StoreSettings::from(value: $this->template->key),
            )
            ->update(attributes: [
                'value' => $this->orderStatusId,
            ]);

        $this->successToast(action: 'updated', model: 'message_templates.singular');
    }

    public function render(): View
    {
        return view(view: 'livewire.dashboard.templates.review-order', data: [
            'orderStatuses' => currentStore()->orderStatuses,
        ]);
    }
}
