<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <input type="text" class="form-control" placeholder="@lang('dashboard.common.search')" wire:model.live.debounce.250ms="keyword">
                </div>
                <div>
                    <button class="btn btn-primary" wire:click="export" wire:loading.remove>
                        @lang('dashboard.common.export')
                    </button>
                    <button class="btn btn-primary" wire:loading wire:target="export" disabled>
                        @lang('dashboard.common.exporting')
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>@lang('dashboard.pages.contacts.columns.id')</th>
                        <th>@lang('dashboard.pages.contacts.columns.name')</th>
                        <th>@lang('dashboard.pages.contacts.columns.email')</th>
                        <th>@lang('dashboard.pages.contacts.columns.mobile')</th>
                        <th>@lang('dashboard.pages.contacts.columns.created_at')</th>
                        <th>@lang('dashboard.common.actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($this->contacts as $contact)
                        <tr>
                            <td>{{ $contact->provider_id }}</td>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->email ?? '-----' }}</td>
                            <td>{{ $contact->mobile }}</td>
                            <td>{{ $contact->created_at->format(format: 'd-m-Y') }}</td>
                            <td>
                                @if(isNotInBlacklistedMobiles(mobile: $contact->mobile))
                                    @can('addToBlacklist', $contact)
                                        <button class="btn btn-danger" wire:click="addToBlacklist({{ $contact->id }})" wire:confirm="@lang('dashboard.common.are_you_sure')">
                                            @lang('dashboard.pages.contacts.actions.add_to_blacklist')
                                        </button>
                                    @endcan
                                @endif

                                @if(isInBlacklistedMobiles(mobile: $contact->mobile))
                                    @can('removeFromBlacklist', $contact)
                                        <button class="btn btn-danger" wire:click="removeFromBlacklist({{ $contact->id }})" wire:confirm="@lang('dashboard.common.are_you_sure')">
                                            @lang('dashboard.pages.contacts.actions.remove_from_blacklist')
                                        </button>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="6">
                                @lang('dashboard.common.no_data')
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($this->contacts->hasPages())
            <div class="card-footer">
                {{ $this->contacts->links(view: 'livewire.dashboard.partials.pagination.default') }}
            </div>
        @endif
    </div>
</div>
