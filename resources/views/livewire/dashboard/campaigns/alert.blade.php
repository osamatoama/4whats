<div @class(['mt-3 alert alert-info d-flex justify-content-between align-items-center' => $hasRunningCampaigns, 'd-none' => !$hasRunningCampaigns])>
    @if($hasRunningCampaigns)
        <div wire:poll.5s>
            @lang('dashboard.pages.campaigns.sending_campaigns', ['count' => $count])
        </div>
        <div>
            <a href="{{ route(name: 'dashboard.campaigns.current') }}" class="btn btn-sm btn-danger">
                @lang('dashboard.pages.campaigns.click_here_to_stop_sending')
            </a>
        </div>
    @endif
</div>
