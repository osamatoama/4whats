<?php

namespace App\Livewire\Dashboard\WhatsappConnection;

use App\Enums\Whatsapp\InstanceStatus;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\WhatsappAccount;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
#[On('whatsapp-account-connected')]
class Bar extends Component
{
    use InteractsWithToasts;

    public bool $isDisconnecting = false;

    #[Computed]
    public function whatsappAccount(): WhatsappAccount
    {
        return currentStore()->whatsappAccount;
    }

    #[Computed]
    public function isConnected(): bool
    {
        $fourWhatsService = new FourWhatsService();

        try {
            $response = $fourWhatsService->instance(instanceId: $this->whatsappAccount->instance_id, instanceToken: $this->whatsappAccount->instance_token)->info();
        } catch (FourWhatsException) {
            if ($this->isDisconnecting) {
                $this->isDisconnecting = false;
                $this->dispatch(event: 'whatsapp-account-disconnected');
            }

            return false;
        }

        if ($response['status'] === InstanceStatus::DISCONNECTED) {
            if ($this->isDisconnecting) {
                $this->isDisconnecting = false;
                $this->dispatch(event: 'whatsapp-account-disconnected');
            }

            return false;
        }

        $this->whatsappAccount->update([
            'connected_mobile' => $response['mobile'],
        ]);

        return true;
    }

    public function disconnect(FourWhatsService $fourWhatsService): void
    {
        try {
            $response = $fourWhatsService->instance(instanceId: $this->whatsappAccount->instance_id, instanceToken: $this->whatsappAccount->instance_token)->logout();
        } catch (FourWhatsException) {
            $this->customErrorToast(
                message: __(key: 'dashboard.whatsapp.cannot_disconnect'),
            );

            return;
        }

        if ($response['logged_out'] === false) {
            $this->customErrorToast(
                message: __(key: 'dashboard.whatsapp.cannot_disconnect'),
            );

            return;
        }

        $this->isDisconnecting = true;

        $this->customSuccessToast(
            message: __(key: 'dashboard.whatsapp.disconnecting'),
        );
    }

    public function placeholder(): View
    {
        return view(view: 'livewire.dashboard.whatsapp-connection.bar-placeholder');
    }

    public function render(): View
    {
        return view(view: 'livewire.dashboard.whatsapp-connection.bar');
    }
}
