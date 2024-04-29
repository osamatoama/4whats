@use(\App\Models\User)

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        @lang('dashboard.pages.employees.create.employee_details')
                    </h4>
                    <div class="card-tools">
                        @can('viewAny', User::class)
                            <a href="{{ route(name: 'dashboard.employees.index') }}" class="btn btn-danger">
                                @lang('dashboard.common.back')
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            <form action="{{ route('dashboard.employees.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group mb-2">
                        <x-dashboard.forms.label for="name" :value="__(key: 'dashboard.pages.employees.columns.name')"/>
                        <x-dashboard.forms.input
                            :is-invalid="$errors->has('name')"
                            type="text"
                            id="name"
                            name="name"
                            :placeholder="__(key: 'dashboard.pages.employees.columns.name')"
                            :value="old(key: 'name')"
                            required
                        />
                        <x-dashboard.forms.error key="name"/>
                    </div>
                    <div class="form-group">
                        <x-dashboard.forms.label for="email" :value="__(key: 'dashboard.pages.employees.columns.email')"/>
                        <x-dashboard.forms.input
                            :is-invalid="$errors->has('email')"
                            type="email"
                            id="email"
                            name="email"
                            :placeholder="__(key: 'dashboard.pages.employees.columns.email')"
                            :value="old(key: 'email')"
                            required
                        />
                        <x-dashboard.forms.error key="email"/>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">
                        @lang('dashboard.common.create')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
