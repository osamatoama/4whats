@use(\App\Models\User)

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <input type="text" class="form-control" placeholder="@lang('dashboard.common.search')" wire:model.live.debounce.250ms="keyword">
                </div>
                <div>
                    @can('createEmployee', User::class)
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
                    @forelse($this->employees as $employee)
                        <tr>
                            <td>{{ $employee->id }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>
                                @can('delete', $employee)
                                    <button class="btn btn-danger" wire:confirm="@lang('dashboard.common.are_you_sure')" wire:click="destroy({{ $employee->id }})">
                                        @lang('dashboard.common.delete')
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="4">
                                @lang('dashboard.common.no_data')
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($this->employees->hasPages())
            <div class="card-footer">
                {{ $this->employees->links(view: 'livewire.dashboard.partials.pagination.default') }}
            </div>
        @endif
    </div>
</div>
