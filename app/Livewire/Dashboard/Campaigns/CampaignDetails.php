<?php

namespace App\Livewire\Dashboard\Campaigns;

use App\Enums\CampaignType;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Services\Queue\BatchService;
use Illuminate\Bus\Batch;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CampaignDetails extends Component
{
    use InteractsWithToasts;

    public string $batchId;

    public string $type;

    #[Computed]
    public function campaign(): Batch
    {
        return BatchService::find(
            id: $this->batchId,
        );
    }

    public function cancelCampaign(): void
    {
        $this->campaign->cancel();

        $this->dispatch(
            event: 'campaign-canceled',
        );

        $this->customSuccessToast(
            message: __(
                key: 'dashboard.pages.campaigns.current.messages.canceled',
            ),
        );
    }

    public function mount(): void
    {
        $this->type = CampaignType::fromBatch(
            batch: $this->campaign,
        )->label();
    }

    public function render(): View
    {
        return view(
            view: 'livewire.dashboard.campaigns.campaign-details',
            data: [
                'percentage' => $this->campaign->progress(),
                'createdAt' => $this->campaign->createdAt->format('d-m-Y h:i:s'),
                'isProcessing' => ! $this->campaign->canceled() && ! $this->campaign->finished(),
                'isCanceled' => $this->campaign->canceled(),
                'isFinished' => ! $this->campaign->canceled() && $this->campaign->finished(),
            ],
        );
    }
}
