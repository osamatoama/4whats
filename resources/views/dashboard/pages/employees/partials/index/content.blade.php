@use(\App\Models\User)

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        @lang('dashboard.pages.employees.index.employees_details')
                    </h4>
                    <div class="card-tools">
                        @can('create', User::class)
                            <a href="{{ route(name: 'dashboard.employees.create') }}" class="btn btn-primary">
                                @lang('dashboard.pages.employees.index.create_a_new_employee')
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>@lang('dashboard.pages.employees.columns.id')</th>
                            <th>@lang('dashboard.pages.employees.columns.name')</th>
                            <th>@lang('dashboard.pages.employees.columns.email')</th>
                            <th>@lang('dashboard.common.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>
                                    @can('delete', $employee)
                                        <form action="{{ route(name: 'dashboard.employees.destroy',parameters:  $employee) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" onclick="return confirm('@lang('dashboard.common.are_you_sure')')">
                                                @lang('dashboard.common.delete')
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="4">
                                    @lang('dashboard.pages.employees.index.no_employees')
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</div>
