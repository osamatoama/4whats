<form action="{{ route(name: 'dashboard.password.store') }}" method="POST" class="my-4">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group mb-2">
        <x-dashboard.forms.label for="email" :value="__(key: 'dashboard.pages.auth.reset_password.email')"/>
        <x-dashboard.forms.input
            :is-invalid="$errors->has('email')"
            type="email"
            id="email"
            name="email"
            :placeholder="__(key: 'dashboard.pages.auth.reset_password.email')"
            :value="old(key: 'email', default: $email)"
            required
        />
        <x-dashboard.forms.error key="email"/>
    </div>

    <div class="form-group mb-2">
        <x-dashboard.forms.label for="password" :value="__(key: 'dashboard.pages.auth.reset_password.password')"/>
        <x-dashboard.forms.input
            :is-invalid="$errors->has('password')"
            type="password"
            id="password"
            name="password"
            :placeholder="__(key: 'dashboard.pages.auth.reset_password.password')"
            required
        />
        <x-dashboard.forms.error key="password"/>
    </div>

    <div class="form-group">
        <x-dashboard.forms.label for="password_confirmation" :value="__(key: 'dashboard.pages.auth.reset_password.password_confirmation')"/>
        <x-dashboard.forms.input
            :is-invalid="$errors->has('password_confirmation')"
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            :placeholder="__(key: 'dashboard.pages.auth.reset_password.password_confirmation')"
            required
        />
        <x-dashboard.forms.error key="password_confirmation"/>
    </div>

    <div class="form-group mb-0 row">
        <div class="col-12">
            <div class="d-grid mt-3">
                <button class="btn btn-primary" type="submit">
                    @lang('dashboard.pages.auth.reset_password.reset')
                    <i class="fas fa-sign-in-alt ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</form>
