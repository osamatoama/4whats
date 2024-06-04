<?php

namespace App\Livewire\Dashboard\Templates;

use App\Enums\Jobs\BatchName;
use App\Enums\MessageTemplate;
use App\Enums\ProviderType;
use App\Jobs\Salla\Pull\OrderStatuses\SallaPullOrderStatusesJob;
use App\Jobs\Zid\Pull\OrderStatuses\PullOrderStatusesJob as ZidPullOrderStatusesJob;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\Template;
use App\Services\Queue\BatchService;
use Illuminate\Database\Eloquent\Collection;
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

        $batchName = match ($store->provider_type) {
            ProviderType::SALLA => BatchName::SALLA_PULL_ORDER_STATUSES,
            ProviderType::ZID => BatchName::ZID_PULL_ORDER_STATUSES,
        };

        if (BatchService::hasRunningBatches(
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

        $accessToken = match ($store->provider_type) {
            ProviderType::SALLA => $store->user->sallaToken->access_token,
            ProviderType::ZID => null, // $store->user->zidToken->access_token,
        };

        $job = match ($store->provider_type) {
            ProviderType::SALLA => new SallaPullOrderStatusesJob(
                accessToken: $accessToken,
                storeId: $store->id,
            ),
            ProviderType::ZID => new ZidPullOrderStatusesJob(
                storeId: $store->id,
            ),
        };

        BatchService::createPendingBatch(
            jobs: $job,
            batchName: $batchName,
            storeId: $store->id,
        )->dispatch();

        $this->customSuccessToast(
            message: __(
                key: 'dashboard.pages.templates.index.syncing_order_statuses',
            ),
        );
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
}
