<?php

namespace App\Livewire\Dashboard;

use App\Exports\MessagesExport;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MessageList extends Component
{
    use InteractsWithToasts, WithPagination;

    #[Url(as: 'q')]
    public ?string $keyword = null;

    #[Computed]
    public function messages(): LengthAwarePaginator
    {
        return currentStore()
            ->messages()
            ->when(
                value: $this->keyword !== null,
                callback: fn (Builder $query): Builder => $query->where(
                    column: 'to',
                    operator: 'LIKE',
                    value: "%{$this->keyword}%",
                ),
            )
            ->latest()
            ->paginate();
    }

    public function export(): Response|BinaryFileResponse
    {
        Gate::authorize(ability: 'export', arguments: Message::class);

        $store = currentStore();

        return (new MessagesExport(
            store: $store,
            messages: $store->messages,
        ))->download(
            fileName: 'messages--'.now()->format(format: 'H.i.s--d-m-Y').'.xlsx',
        );
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
