<?php

namespace App\Livewire\Dashboard\Templates;

use App\Enums\SettingKey;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\Template;
use App\Services\Setting\SettingService;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class ReviewOrder extends Component
{
    use InteractsWithToasts;

    public Template $template;

    public int $orderStatusId;

    public function mount(): void
    {
        $this->orderStatusId = settings(
            storeId: currentStore()->id,
        )->value(
            key: SettingKey::STORE_ORDER_STATUS_ID_FOR_REVIEW_ORDER_MESSAGE,
        );
    }

    public function updated(): void
    {
        $this->authorize(
            ability: 'update',
            arguments: $this->template,
        );

        $this->validate(
            rules: [
                'orderStatusId' => [
                    'required',
                    'integer',
                    Rule::exists(
                        table: 'order_statuses',
                        column: 'id',
                    )->where(
                        column: 'store_id',
                        value: currentStore()->id,
                    ),
                ],
            ],
            attributes: [
                'orderStatusId' => __(
                    key: 'dashboard.pages.templates.columns.review_order_status.label',
                ),
            ],
        );

        SettingService::updateOrderStatusId(
            store: currentStore(),
            orderStatuesId: $this->orderStatusId,
        );

        $this->successToast(
            action: 'updated',
            model: 'templates.singular',
        );
    }

    public function render(): View
    {
        return view(
            view: 'livewire.dashboard.templates.review-order',
            data: [
                'orderStatuses' => currentStore()->orderStatuses,
            ],
        );
    }
}
