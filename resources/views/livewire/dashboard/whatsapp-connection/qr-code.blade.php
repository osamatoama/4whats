<div class="col-12">
    @if($this->qrCode !== null)
        <div class="w-100 border rounded py-5 px-3 d-flex flex-column justify-content-center align-items-center">
            <h4>
                @lang('dashboard.pages.home.scan_qr_code')
            </h4>
            <img src="{{ $this->qrCode }}" alt="qrCode" wire:poll.keep-alive.30s>
            <p wire:key="{{ time() }}" x-data="{ countdown: 30 }" x-init="setInterval(() => {countdown--; }, 1000);">
                @lang('dashboard.whatsapp.will_refresh_after')
                <span x-text="countdown">30</span>
                @lang('dashboard.common.second')
            </p>
        </div>
    @endif
</div>
