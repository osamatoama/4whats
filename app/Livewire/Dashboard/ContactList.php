<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\InteractsWithToasts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ContactList extends Component
{
    use InteractsWithToasts, WithPagination;

    #[Url(as: 'q')]
    public ?string $keyword = null;

    #[Computed]
    public function contacts(): LengthAwarePaginator
    {
        return currentStore()
            ->contacts()
            ->when(
                value: $this->keyword !== null,
                callback: fn(Builder $query): Builder => $query->whereAny(
                    columns: ['first_name', 'last_name', 'email', 'mobile'],
                    operator: 'LIKE',
                    value: "%{$this->keyword}%",
                )
            )
            ->paginate();
    }

    public function export(): void
    {
        // TODO:Export
    }

    public function updated(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view(view: 'livewire.dashboard.contact-list');
    }
}
