<form action="{{ route(name: 'dashboard.password.email') }}" method="POST" class="my-4">
    @csrf

    <div class="form-group mb-2">
        <x-dashboard.forms.label for="email" :value="__(key: 'dashboard.pages.auth.forgot_password.email')"/>
        <x-dashboard.forms.input
            :is-invalid="$errors->has('email')"
            type="email"
            id="email"
            name="email"
            :placeholder="__(key: 'dashboard.pages.auth.forgot_password.email')"
            :value="old(key: 'email')"
            required
        />
        <x-dashboard.forms.error key="email"/>
    </div>

    <div class="form-group mb-0 row">
        <div class="col-12">
            <div class="d-grid mt-3">
                <button class="btn btn-primary" type="submit">
                    @lang('dashboard.pages.auth.forgot_password.send')
                    <i class="fas fa-sign-in-alt ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</form>

<div class="text-center text-muted">
    <p class="mb-1">
        @lang('dashboard.pages.auth.forgot_password.remember_it')
        <a href="{{ route(name: 'dashboard.login') }}" class="text-primary ms-2">
            @lang('dashboard.pages.auth.forgot_password.login_here')
        </a>
    </p>
</div>
