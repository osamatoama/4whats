<div class="d-flex justify-content-between align-items-center mt-3 border rounded p-3">
    <div>
        @if($this->isConnected)
            @lang('dashboard.whatsapp.connected') ({{ $this->whatsappAccount->connected_mobile }})
        @else
            @lang('dashboard.whatsapp.disconnected')
        @endif
    </div>
    <div class="d-flex flex-column flex-md-row gap-1">
        @if($this->isConnected)
            @if($this->isDisconnecting)
                <div>
                    <button class="btn btn-sm btn-danger" wire:poll.keep-alive.5s disabled>
                        @lang('dashboard.whatsapp.disconnecting')
                    </button>
                </div>
            @else
                @can('disconnect', $this->whatsappAccount)
                    <div>
                        <button class="btn btn-sm btn-danger" wire:confirm="@lang('dashboard.common.are_you_sure')" wire:click="disconnect" wire:loading.attr="disabled">
                            @lang('dashboard.whatsapp.disconnect')
                        </button>
                    </div>
                @endcan

                @can('disableSending', $this->whatsappAccount)
                    @if($this->whatsappAccount->is_sending_enabled)
                        <div>
                            <button class="btn btn-sm btn-danger" wire:confirm="@lang('dashboard.common.are_you_sure')" wire:click="disableSending" wire:loading.attr="disabled">
                                @lang('dashboard.whatsapp.disable_sending')
                            </button>
                        </div>
                    @endif
                @endcan

                @can('enableSending', $this->whatsappAccount)
                    @if($this->whatsappAccount->is_sending_disabled)
                        <div>
                            <button class="btn btn-sm btn-success" wire:confirm="@lang('dashboard.common.are_you_sure')" wire:click="enableSending" wire:loading.attr="disabled">
                                @lang('dashboard.whatsapp.enable_sending')
                            </button>
                        </div>
                    @endif
                @endcan
            @endif
        @endif
    </div>
</div>
