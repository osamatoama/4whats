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

class MessageList extends Component
{
    use InteractsWithToasts, WithPagination;

    #[Url(as: 'q')]
    public ?string $keyword = null;

    #[Computed]
    public function messages(): LengthAwarePaginator
    {
        return currentStore()
            ->messageHistories()
            ->when(
                value: $this->keyword !== null,
                callback: fn (Builder $query): Builder => $query->where(
                    column: 'to',
                    operator: 'LIKE',
                    value: "%{$this->keyword}%",
                )
            )
            ->latest()
            ->paginate();
    }

    public function export(): void
    {
        // TODO:Export
    }

    public function updatedKeyword(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view(view: 'livewire.dashboard.message-list');
    }
}
