<tr>
    <td>
        {{ $type }}
    </td>
    <td>
        {{ $createdAt }}
    </td>
    <td>
        <div class="progress" style="height: 25px;">
            <div class="progress-bar" role="progressbar" @style(["width: {$percentage}%" => $percentage > 0]) aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                {{ $percentage }}%
            </div>
        </div>
    </td>
    <td>
        @if($isProcessing)
            <button class="btn btn-danger" wire:click="cancelCampaign" wire:confirm="@lang('dashboard.common.are_you_sure')" wire:loading.attr="disabled" wire:poll>
                @lang('dashboard.pages.campaigns.current.actions.cancel')
            </button>
        @endif

        @if($isCanceled)
            <button class="btn btn-danger" disabled>
                @lang('dashboard.pages.campaigns.current.actions.canceled')
            </button>
        @endif

        @if($isFinished)
            <button class="btn btn-danger" disabled>
                @lang('dashboard.pages.campaigns.current.actions.finished')
            </button>
        @endif
    </td>
</tr>
