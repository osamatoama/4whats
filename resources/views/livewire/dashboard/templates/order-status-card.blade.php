<div class="mt-3">
    <div class="d-flex justify-content-between align-items-center">
        <h6>
            {{ $template->order_status->name }}
        </h6>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" wire:model.live="isEnabled">
        </div>
    </div>

    <p>{{ $template->enum->description() }}</p>

    <textarea class="form-control mb-3" rows="5" wire:model.live.debounce.500ms="message"></textarea>
    <div>
        <p>@lang('dashboard.pages.templates.index.waiting_before_sending')</p>
        <p>@lang('dashboard.pages.templates.index.in_hours')</p>
        <output class="w-100 text-center">{{ $template->delay_in_hours }}</output>
        <input type="range" class="form-range" min="0" max="72" step="1" oninput="this.previousElementSibling.value = this.value" wire:model.live.debounce.500ms="delayInHours">
    </div>
</div>
