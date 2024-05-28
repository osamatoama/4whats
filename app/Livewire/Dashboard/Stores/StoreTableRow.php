<?php

namespace App\Livewire\Dashboard\Stores;

use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\Store;
use Illuminate\View\View;
use Livewire\Component;

class StoreTableRow extends Component
{
    use InteractsWithToasts;

    public Store $store;

    public ?int $fourWhatsProviderId = null;

    public ?string $fourWhatsApiKey = null;

    public ?int $instanceId;

    public ?string $instanceToken;

    public function updateStore(): void
    {
        $this->validate(rules: [
            'fourWhatsProviderId' => ['required', 'integer'],
            'fourWhatsApiKey' => ['required', 'string'],
            'instanceId' => ['required', 'integer'],
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
        $this->fourWhatsProviderId = $this->store->user->fourWhatsCredential?->provider_id;
        $this->fourWhatsApiKey = $this->store->user->fourWhatsCredential?->api_key;
        $this->instanceId = $this->store->whatsappAccount->instance_id;
        $this->instanceToken = $this->store->whatsappAccount->instance_token;
    }

    public function render(): View
    {
        return view(view: 'livewire.dashboard.stores.store-table-row');
    }
}
