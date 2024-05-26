<?php

namespace App\Livewire\Dashboard\Stores;

use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StoreList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public ?string $keyword = null;

    #[Computed]
    public function stores(): LengthAwarePaginator
    {
        return Store::query()
            ->with(relations: ['user.fourWhatsCredential', 'whatsappAccount'])
            ->when(
                value: $this->keyword !== null,
                callback: fn (Builder $query): Builder => $query->where(
                    column: 'email',
                    operator: 'LIKE',
                    value: "%{$this->keyword}%",
                ),
            )
            ->latest()
            ->paginate();
    }

    public function updatedKeyword(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view(view: 'livewire.dashboard.stores.store-list');
    }
}
