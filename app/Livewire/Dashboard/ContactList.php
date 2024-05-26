<?php

namespace App\Livewire\Dashboard;

use App\Exports\ContactsExport;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\Contact;
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
                callback: fn (Builder $query): Builder => $query->whereAny(
                    columns: ['first_name', 'last_name', 'email', 'mobile'],
                    operator: 'LIKE',
                    value: "%{$this->keyword}%",
                ),
            )
            ->latest()
            ->paginate();
    }

    public function export(): null|Response|BinaryFileResponse
    {
        Gate::authorize(ability: 'export', arguments: Contact::class);

        $store = currentStore();
        if ($store->is_expired) {
            $this->customErrorToast(
                message: __(key: 'dashboard.common.store_expired_message'),
            );

            return null;
        }

        return (new ContactsExport(
            store: $store,
            contacts: $store->contacts,
        ))->download(
            fileName: 'contacts--'.now()->format(format: 'H.i.s--d-m-Y').'.xlsx',
        );
    }

    public function updatedKeyword(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        return view(view: 'livewire.dashboard.contact-list');
    }
}
