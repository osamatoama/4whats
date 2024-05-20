<div class="d-flex justify-content-between align-items-center mt-3 border rounded p-3">
    <div>
        @if($this->isConnected)
            @lang('dashboard.whatsapp.connected') ({{ $this->whatsappAccount->connected_mobile }})
        @else
            @lang('dashboard.whatsapp.disconnected')
        @endif
    </div>
    <div>
        @if($this->isConnected)
            @if($this->isDisconnecting)
                <button class="btn btn-danger btn-sm" wire:poll.keep-alive.5s disabled>
                    @lang('dashboard.whatsapp.disconnecting')
                </button>
            @else
                <button class="btn btn-danger btn-sm" wire:confirm="@lang('dashboard.common.are_you_sure')" wire:click="disconnect" wire:loading.attr="disabled">
                    @lang('dashboard.whatsapp.disconnect')
                </button>
            @endif
        @endif
    </div>
</div>
