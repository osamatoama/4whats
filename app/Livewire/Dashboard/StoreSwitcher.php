<?php

namespace App\Livewire\Dashboard;

use App\Models\Store;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class StoreSwitcher extends Component
{
    public function switch(int $storeId): Redirector
    {
        session()->put(
            key: 'current_store',
            value: parentUser()->stores()->findOrFail(id: $storeId),
        );

        return redirect(
            to: request()->header(key: 'referer'),
        );
    }

    public function render(): View
    {
        $currentStore = currentStore();
        $stores = parentUser()->stores->reject(callback: fn (Store $store) => $store->is(model: $currentStore));

        return view(view: 'livewire.dashboard.store-switcher', data: [
            'currentStore' => $currentStore,
            'stores' => parentUser()->stores->reject(callback: fn (Store $store) => $store->is(model: $currentStore)),
            'hasMoreStores' => $stores->isNotEmpty(),
        ]);
    }
}
