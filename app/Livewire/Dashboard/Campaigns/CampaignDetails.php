<?php

namespace App\Livewire\Dashboard\Campaigns;

use App\Enums\CampaignType;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\QueuedJobBatch;
use Illuminate\Bus\Batch;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CampaignDetails extends Component
{
    use InteractsWithToasts;

    public QueuedJobBatch $campaign;

    public string $type;

    #[Computed]
    public function batch(): Batch
    {
        return $this->campaign->toBatch();
    }

    public function cancelCampaign(): void
    {
        $this->batch->cancel();

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
        $this->type = CampaignType::fromQueuedJobBatch(
            queuedJobBatch: $this->campaign,
        )->label();
    }

    public function render(): View
    {
        return view(
            view: 'livewire.dashboard.campaigns.campaign-details',
            data: [
                'percentage' => $this->batch->progress(),
                'isProcessing' => $this->campaign->cancelled_at === null && $this->campaign->finished_at === null,
                'isCanceled' => $this->campaign->cancelled_at !== null,
                'isFinished' => $this->campaign->cancelled_at === null && $this->campaign->finished_at !== null,
            ],
        );
    }
}
