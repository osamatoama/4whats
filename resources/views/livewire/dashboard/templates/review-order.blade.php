<div class="form-group mb-3">
    <p>@lang('dashboard.pages.templates.index.order_status_to_send_notification')</p>
    <select @class(['form-select', 'is-invalid' => $errors->has(key: 'orderStatusId')]) wire:model.live="orderStatusId">
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
