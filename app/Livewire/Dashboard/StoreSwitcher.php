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
            key: 'current_store_id',
            value: parentUser()->stores()->findOr(
                id: $storeId,
                callback: fn (): Store => currentStore(),
            )->id,
        );

        return redirect(
            to: request()->header(
                key: 'referer',
            ),
        );
    }

    public function render(): View
    {
        $currentStore = currentStore();
        $stores = parentUserStores()->reject(
            callback: fn (Store $store) => $store->is(
                model: $currentStore,
            ),
        );

        return view(
            view: 'livewire.dashboard.store-switcher',
            data: [
                'currentStore' => $currentStore,
                'stores' => $stores,
                'hasMoreStores' => $stores->isNotEmpty(),
                'subscriptionExpiredAt' => $currentStore->whatsappAccount->expired_at->format(
                    format: 'Y-m-d',
                ),
                'subscriptionType' => $currentStore->subscription_type->label(),
                'subscriptionTypeCssClass' => $currentStore->subscription_type->cssClass(),
            ],
        );
    }
}
