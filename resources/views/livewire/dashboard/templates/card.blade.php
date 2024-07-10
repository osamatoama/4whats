<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title">
                    {{ $template->enum->label() }}
                </h4>
                <div class="card-tools d-flex align-items-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" wire:model.live="isEnabled">
                    </div>
                    <div style="width: 25px;">
                        @if($template->enum->hint() !== null)
                            <i class="ti ti-alert-circle" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ $template->enum->hint() }}"></i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body w-75 mx-auto">
            <p>
                {{ $template->enum->description() }}
                @foreach($template->enum->placeholders() as $placeholder)
                    <button class="btn btn-sm btn-outline-info" wire:click="appendPlaceholder('{{ $placeholder }}')">
                        {{ $placeholder }}
                    </button>
                @endforeach
                @if($template->is_salla_review_order)
                    <span class="d-block text-danger fw-bold">@lang('dashboard.pages.templates.index.salla_review_order_warning')</span>
                @endif
            </p>

            <div class="form-group mb-3">
                <textarea @class(['form-control', 'is-invalid' => $errors->has(key: 'message')]) rows="5" wire:model.live.debounce.500ms="message"></textarea>
                @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if($template->is_review_order)
                <livewire:dashboard.templates.review-order :template="$template"/>
            @endif

            @if($template->is_new_order_for_employees)
                <livewire:dashboard.templates.new-order-for-employees :template="$template"/>
            @endif

            @if($template->enum->shouldShowDelay())
                <div>
                    <p>@lang('dashboard.pages.templates.index.waiting_before_sending')</p>
                    <p>@lang('dashboard.pages.templates.index.in_hours')</p>
                    <output class="w-100 text-center">{{ $template->delay_in_hours }}</output>
                    <input type="range" class="form-range" min="0" max="72" step="1" oninput="this.previousElementSibling.value = this.value" wire:model.live.debounce.500ms="delayInHours">
                </div>
            @endif
        </div>
    </div>
</div>
