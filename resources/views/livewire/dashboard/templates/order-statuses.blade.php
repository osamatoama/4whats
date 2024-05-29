<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title">
                    {{ $label }}
                </h4>
                <div class="card-tools d-flex align-items-center gap-1">
                    <div style="width: 25px;">
                        @if($hint !== null)
                            <i class="ti ti-alert-circle" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ $hint }}"></i>
                        @endif
                    </div>
                    <button class="btn btn-sm btn-primary" wire:click="syncOrderStatuses" wire:loading.attr="disabled">
                        @lang('dashboard.common.sync')
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body w-75 mx-auto">
            <select @class(['form-select', 'is-invalid' => $errors->has(key: 'currentTemplateId')]) wire:model.live="currentTemplateId">
                @foreach($templates as $template)
                    <option value="{{ $template->id }}" wire:key="{{ $template->id }}">
                        {{ $template->order_status->name }}
                    </option>
                @endforeach
            </select>
            @error('currentTemplateId')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <livewire:dashboard.templates.card :template="$currentTemplate" wire:key="{{ $currentTemplate->id }}"/>
        </div>
    </div>
</div>
