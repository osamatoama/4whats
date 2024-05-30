<div class="row">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                @lang('dashboard.pages.settings.index.password.title')
            </h3>
        </div>
        <div class="card-body">
            <div class="form-group mb-3">
                <label class="form-label" for="name">
                    @lang('dashboard.pages.settings.index.password.current_password')
                </label>
                <input type="password" @class(['form-control', 'is-invalid' => $errors->has(key: 'currentPassword')]) wire:model="currentPassword">
                @error('currentPassword')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group mb-3">
                <label class="form-label" for="name">
                    @lang('dashboard.pages.settings.index.password.new_password')
                </label>
                <input type="password" @class(['form-control', 'is-invalid' => $errors->has(key: 'newPassword')]) wire:model="newPassword">
                @error('newPassword')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="name">
                    @lang('dashboard.pages.settings.index.password.new_password_confirmation')
                </label>
                <input type="password" class="form-control" wire:model="newPasswordConfirmation">
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-warning" wire:click="updatePassword" wire:loading.attr="disabled">
                @lang('dashboard.common.edit')
                @lang('dashboard.pages.settings.index.password.title')
            </button>
        </div>
    </div>
</div>
