<?php

namespace App\Http\Controllers\Dashboard\Campaign;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CampaignSendController extends Controller
{
    public function __invoke(): View
    {
        Gate::authorize(
            ability: 'sendCampaigns',
            arguments: User::class,
        );

        return view(
            view: 'dashboard.pages.campaigns.send',
        );
    }
}
