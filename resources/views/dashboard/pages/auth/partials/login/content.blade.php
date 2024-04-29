<form class="my-4" action="{{ route('dashboard.login') }}" method="POST">
    @csrf
    <div class="form-group mb-2">
        <x-dashboard.forms.label for="email" :value="__(key: 'dashboard.pages.auth.login.email')"/>
        <x-dashboard.forms.input
            :is-invalid="$errors->has('email')"
            type="email"
            id="email"
            name="email"
            :placeholder="__(key: 'dashboard.pages.auth.login.email')"
            :value="old(key: 'email')"
            required
        />
        <x-dashboard.forms.error key="email"/>
    </div>

    <div class="form-group">
        <x-dashboard.forms.label for="password" :value="__(key: 'dashboard.pages.auth.login.password')"/>
        <x-dashboard.forms.input
            :is-invalid="$errors->has('password')"
            type="password"
            id="password"
            name="password"
            :placeholder="__(key: 'dashboard.pages.auth.login.password')"
            required
        />
    </div>

    <div class="form-group row mt-3">
        <div class="col-sm-6">
            <div class="form-check form-switch form-switch-success">
                <input class="form-check-input" name="remember" type="checkbox" id="remember">
                <label class="form-check-label" for="remember">@lang('dashboard.pages.auth.login.remember_me')</label>
            </div>
        </div>
    </div>

    <div class="form-group mb-0 row">
        <div class="col-12">
            <div class="d-grid mt-3">
                <button class="btn btn-primary" type="submit">
                    @lang('dashboard.pages.auth.login.login')
                    <i class="fas fa-sign-in-alt ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</form>
