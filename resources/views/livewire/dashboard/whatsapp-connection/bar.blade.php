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
                <button class="btn btn-sm btn-danger" wire:poll.keep-alive.5s disabled>
                    @lang('dashboard.whatsapp.disconnecting')
                </button>
            @else
                <button class="btn btn-sm btn-danger" wire:confirm="@lang('dashboard.common.are_you_sure')" wire:click="disconnect" wire:loading.attr="disabled">
                    @lang('dashboard.whatsapp.disconnect')
                </button>

                @if($this->whatsappAccount->is_sending_enabled)
                    <button class="btn btn-sm btn-danger" wire:confirm="@lang('dashboard.common.are_you_sure')" wire:click="disableSending" wire:loading.attr="disabled">
                        @lang('dashboard.whatsapp.disable_sending')
                    </button>
                @endif

                @if($this->whatsappAccount->is_sending_disabled)
                    <button class="btn btn-sm btn-success" wire:confirm="@lang('dashboard.common.are_you_sure')" wire:click="enableSending" wire:loading.attr="disabled">
                        @lang('dashboard.whatsapp.enable_sending')
                    </button>
                @endif
            @endif
        @endif
    </div>
</div>
