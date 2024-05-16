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
            <p>{{ $template->enum->description() }}</p>

            <textarea class="form-control mb-3" rows="5" wire:model.live.debounce.500ms="message"></textarea>

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
