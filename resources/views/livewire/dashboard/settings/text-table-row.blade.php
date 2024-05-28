<tr>
    <td>
        {{ $setting->key->label() }}
    </td>

    <td>
        <div class="form-group">
            <input type="text" @class(['form-control', 'is-invalid' => $errors->has(key: 'value')]) wire:model="value">
            @error('value')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </td>
    
    <td>
        @can('update', $setting)
            <button class="btn btn-warning" wire:click="updateSetting" wire:loading.attr="disabled">
                @lang('dashboard.common.edit')
            </button>
        @endcan
    </td>
</tr>
