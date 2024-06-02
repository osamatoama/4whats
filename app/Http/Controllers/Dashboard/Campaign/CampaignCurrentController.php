<?php

namespace App\Http\Controllers\Dashboard\Campaign;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Campaigns\CampaignsService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CampaignCurrentController extends Controller
{
    public function __invoke(): View
    {
        Gate::authorize(
            ability: 'viewCampaigns',
            arguments: User::class,
        );

        $service = new CampaignsService(
            store: currentStore(),
        );

        return view(
            view: 'dashboard.pages.campaigns.current',
            data: [
                'campaigns' => $service->getRunningCampaigns(),
            ],
        );
    }
}
