<?php

namespace App\Livewire\Dashboard\Templates;

use App\Enums\Jobs\BatchName;
use App\Enums\MessageTemplate;
use App\Enums\ProviderType;
use App\Jobs\Salla\Pull\OrderStatuses\SallaPullOrderStatusesJob;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\QueuedJobBatch;
use App\Models\Store;
use App\Models\Template;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class OrderStatuses extends Component
{
    use InteractsWithToasts;

    public Collection $templates;

    public int $currentTemplateId;

    public Template $currentTemplate;

    public function syncOrderStatuses(): void
    {
        $store = currentStore();

        if ($store->provider_type === ProviderType::SALLA) {
            $this->syncSallaOrderStatuses(store: $store);
        }
    }

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

    protected function syncSallaOrderStatuses(Store $store): void
    {
        $batchName = BatchName::SALLA_PULL_ORDER_STATUSES;
        if (QueuedJobBatch::hasRunningBatches(
            batchName: $batchName,
            storeId: $store->id,
        )) {
            $this->customWarningToast(
                message: __(
                    key: 'dashboard.pages.templates.index.syncing_order_statuses_please_wait',
                ),
            );

            return;
        }

        $accessToken = $store->user->sallaToken->access_token;
        Bus::batch(
            jobs: new SallaPullOrderStatusesJob(
                accessToken: $accessToken,
                storeId: $store->id,
            ),
        )->name(
            name: $batchName->generate(
                storeId: $store->id,
            ),
        )->dispatch();

        $this->customSuccessToast(
            message: __(
                key: 'dashboard.pages.templates.index.syncing_order_statuses',
            ),
        );
    }
}
