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
                        <th>@lang('dashboard.pages.messages.columns.type')</th>
                        <th>@lang('dashboard.pages.messages.columns.mobile')</th>
                        <th>@lang('dashboard.pages.messages.columns.message')</th>
                        <th>@lang('dashboard.pages.messages.columns.created_at')</th>
                        <th>@lang('dashboard.pages.messages.columns.status')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($this->messages as $message)
                        <tr>
                            <td>{{ $message->type->label() }}</td>
                            <td>{{ $message->mobile }}</td>
                            <td>
                                @if($message->body !== null)
                                    <textarea class="form-control" readonly>{{ $message->body }}</textarea>
                                @endif
                                @if($message->attachments !== null)
                                    <div @class(['mt-1' => $message->body !== null])>
                                        <span>@lang('dashboard.pages.messages.index.attachments'): </span>
                                        @foreach($message->attachments as $attachment)
                                            <a href="{{ $attachment['url'] }}" class="btn btn-xs btn-primary" target="_blank" title="{{ $attachment['name'] }}">
                                                @lang('dashboard.common.click_here') ({{ str($attachment['name'])->afterLast(search: '.') }})
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
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
