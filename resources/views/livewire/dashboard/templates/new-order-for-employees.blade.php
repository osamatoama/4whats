<div class="mb-3">
    <p>@lang('dashboard.pages.templates.index.employees_mobiles')</p>
    <p>@lang('dashboard.pages.templates.index.employees_mobiles_description')</p>
    <div class="form-group">
        <input @class(['form-control', 'is-invalid' => $errors->has(key: 'mobiles')]) type="text" placeholder="+966XXXXXXXXX" wire:model.live.debounce.500ms="mobiles">
        @error('mobiles')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>
</div>
