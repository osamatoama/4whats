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
                        <th>@lang('dashboard.pages.messages.columns.mobile')</th>
                        <th>@lang('dashboard.pages.messages.columns.message')</th>
                        <th>@lang('dashboard.pages.messages.columns.created_at')</th>
                        <th>@lang('dashboard.pages.messages.columns.status')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($this->messages as $message)
                        <tr>
                            <td>{{ $message->mobile }}</td>
                            <td>
                                <textarea class="form-control" readonly>{{ $message->body }}</textarea>
                            </td>
                            <td>{{ $message->created_at->format(format: 'd-m-Y H:i:s') }}</td>
                            <td>{{ $message->status->label() }}</td>
                        </tr>
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
        @if($this->messages->hasPages())
            <div class="card-footer">
                {{ $this->messages->links(view: 'livewire.dashboard.partials.pagination.default') }}
            </div>
        @endif
    </div>
</div>
