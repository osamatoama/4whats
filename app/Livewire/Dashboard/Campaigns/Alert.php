<?php

namespace App\Livewire\Dashboard\Campaigns;

use App\Services\Campaigns\CampaignsService;
use Illuminate\View\View;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
#[On('campaign-started')]
class Alert extends Component
{
    public function render(): View
    {
        $service = new CampaignsService(
            store: currentStore(),
        );

        $count = $service->getRunningCampaignsCount();
        $hasRunningCampaigns = $count > 0;

        return view(
            view: 'livewire.dashboard.campaigns.alert',
            data: [
                'count' => $count,
                'hasRunningCampaigns' => $hasRunningCampaigns,
            ],
        );
    }
}
