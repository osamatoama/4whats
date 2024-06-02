<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>
                            @lang('dashboard.pages.campaigns.columns.type.label')
                        </th>
                        <th>
                            @lang('dashboard.pages.campaigns.columns.created_at.label')
                        </th>
                        <th>
                            @lang('dashboard.pages.campaigns.columns.percentage.label')
                        </th>
                        <th>
                            @lang('dashboard.common.actions')
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($campaigns as $campaign)
                        <livewire:dashboard.campaigns.campaign-details :campaign="$campaign" wire:key="{{ $campaign->id }}"/>
                    @empty
                        <tr>
                            <td class="text-center" colspan="4">
                                @lang('dashboard.common.no_data')
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
