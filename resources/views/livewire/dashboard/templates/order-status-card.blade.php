<div class="mt-3">
    <div class="d-flex justify-content-between align-items-center">
        <h6>
            {{ $template->order_status->name }}
        </h6>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" wire:model.live="isEnabled">
        </div>
    </div>

    <p>
        {{ $template->enum->description() }}
        @foreach($template->enum->placeholders() as $placeholder)
            <button class="btn btn-sm btn-outline-info" wire:click="appendPlaceholder('{{ $placeholder }}')">
                {{ $placeholder }}
            </button>
        @endforeach
    </p>

    <div class="form-group mb-3">
        <textarea @class(['form-control', 'is-invalid' => $errors->has(key: 'message')]) rows="5" wire:model.live.debounce.500ms="message"></textarea>
        @error('message')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <p>@lang('dashboard.pages.templates.index.waiting_before_sending')</p>
        <p>@lang('dashboard.pages.templates.index.in_hours')</p>
        <output class="w-100 text-center">{{ $template->delay_in_hours }}</output>
        <input type="range" class="form-range" min="0" max="72" step="1" oninput="this.previousElementSibling.value = this.value" wire:model.live.debounce.500ms="delayInHours">
    </div>
</div>
