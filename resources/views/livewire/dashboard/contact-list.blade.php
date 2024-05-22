<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <input type="text" class="form-control" placeholder="@lang('dashboard.common.search')" wire:model.live.debounce.250ms="keyword">
                </div>
                <div>
                    <button class="btn btn-primary" wire:click="export">@lang('dashboard.common.export')</button>
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
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="5">
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
