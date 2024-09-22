<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <input type="text" class="form-control" placeholder="@lang('dashboard.common.search')" autocomplete="off" wire:model.live.debounce.250ms="keyword">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>@lang('dashboard.pages.stores.columns.id')</th>
                            <th>@lang('dashboard.pages.stores.columns.type')</th>
                            <th>@lang('dashboard.pages.stores.columns.name')</th>
                            <th>@lang('dashboard.pages.stores.columns.email')</th>
                            <th>@lang('dashboard.pages.stores.columns.mobile')</th>
                            <th>@lang('dashboard.pages.stores.columns.whatsapp_expired_at')</th>
                            <th>@lang('dashboard.common.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($this->stores as $store)
                            <livewire:dashboard.stores.store-table-row :store="$store" :wire:key="$store->id"/>
                        @empty
                            <tr>
                                <td class="text-center" colspan="8">
                                    @lang('dashboard.common.no_data')
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($this->stores->hasPages())
                <div class="card-footer">
                    {{ $this->stores->links(view: 'livewire.dashboard.partials.pagination.default') }}
                </div>
            @endif
        </div>
    </div>
</div>
