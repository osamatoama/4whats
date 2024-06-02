@use(\App\Models\User)

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="form-label">
                        @lang('dashboard.pages.campaigns.send.columns.type')
                    </label>
                    <select @class(['form-select', 'is-invalid' => $errors->has(key: 'currentType')]) wire:model.live="currentType">
                        @foreach($types as $type)
                            <option value="{{ $type->value }}" wire:key="{{ $type->value }}">
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('currentType')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">
                        @lang('dashboard.pages.campaigns.send.columns.message.label')
                    </label>
                    <textarea @class(['form-control', 'is-invalid' => $errors->has(key: 'message')]) wire:model="message"></textarea>
                    @error('message')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        @lang('dashboard.pages.campaigns.send.columns.message.description', ['placeholders' => $currentType->placeholdersAsString()])
                    </small>
                </div>
            </div>
            <div class="card-footer">
                @can('sendCampaigns', User::class)
                    <button class="btn btn-primary" wire:click="sendCampaign" wire:loading.attr="disabled">
                        @lang('dashboard.common.send')
                    </button>
                @endcan
            </div>
        </div>
    </div>
</div>
