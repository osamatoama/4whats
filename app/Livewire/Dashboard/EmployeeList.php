<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeList extends Component
{
    use InteractsWithToasts, WithPagination;

    #[Url(as: 'q')]
    public ?string $keyword = null;

    #[Computed]
    public function employees(): LengthAwarePaginator
    {
        return auth()
            ->user()
            ->children()
            ->when(
                value: $this->keyword !== null,
                callback: fn (Builder $query): Builder => $query->whereAny(
                    columns: ['name', 'email'],
                    operator: 'LIKE',
                    value: "%{$this->keyword}%",
                ),
            )
            ->latest()
            ->paginate();
    }

    public function destroy(User $employee): void
    {
        $this->authorize(ability: 'deleteEmployee', arguments: $employee);

        $employee->delete();

        $this->successToast(action: 'deleted', model: 'employees.singular');
    }

    public function updatedKeyword(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view(view: 'livewire.dashboard.employee-list');
    }
}
