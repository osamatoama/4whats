<tr>
    <td>{{ $this->store->provider_id }}</td>
    <td>{{ $this->store->provider_type->label() }}</td>
    <td>{{ $this->store->email }}</td>
    <td>{{ $this->store->user->fourWhatsCredential->provider_id }}</td>
    <td>
        <div class="form-group">
            <input type="text" @class(['form-control', 'is-invalid'=> $errors->has(key: 'fourWhatsApiKey')]) wire:model="fourWhatsApiKey">
            @error('fourWhatsApiKey')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </td>
    <td>{{ $this->store->user->id }}</td>
    <td>
        <div class="form-group">
            <input type="text" @class(['form-control', 'is-invalid'=> $errors->has(key: 'instanceToken')]) wire:model="instanceToken">
            @error('instanceToken')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </td>
    <td>
        @can('update', $this->store)
            <button class="btn btn-warning" wire:click="updateStore" wire:loading.attr="disabled">
                @lang('dashboard.common.edit')
            </button>
        @endcan
    </td>
</tr>
