<?php

namespace App\Livewire\Dashboard\Stores;

use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\Store;
use Illuminate\View\View;
use Livewire\Component;

class StoreRow extends Component
{
    use InteractsWithToasts;

    public Store $store;

    public ?string $fourWhatsApiKey;

    public ?string $instanceToken;

    public function updateStore(): void
    {
        $this->validate(rules: [
            'fourWhatsApiKey' => ['required', 'string'],
            'instanceToken' => ['required', 'string'],
        ]);

        $this->store->user->fourWhatsCredential()->update(values: [
            'api_key' => $this->fourWhatsApiKey,
        ]);

        $this->store->whatsappAccount()->update(values: [
            'instance_token' => $this->instanceToken,
        ]);

        $this->successToast(action: 'updated', model: 'stores.singular');
    }

    public function mount(): void
    {
        $this->fourWhatsApiKey = $this->store->user->fourWhatsCredential?->api_key;
        $this->instanceToken = $this->store->whatsappAccount->instance_token;
    }

    public function render(): View
    {
        return view(view: 'livewire.dashboard.stores.store-row');
    }
}
