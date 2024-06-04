<div class="form-group mb-3">
    <p>@lang('dashboard.pages.templates.index.order_status_to_send_notification')</p>
    <select @class(['form-select', 'is-invalid' => $errors->has(key: 'orderStatusId')]) wire:model.live="orderStatusId">
        <option value="0" wire:key="0">
            @lang('dashboard.common.please_choose')
        </option>
        @foreach($orderStatuses as $orderStatus)
            <option value="{{ $orderStatus->id }}" wire:key="{{ $orderStatus->id }}">
                {{ $orderStatus->name }}
            </option>
        @endforeach
    </select>
    @error('orderStatusId')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
