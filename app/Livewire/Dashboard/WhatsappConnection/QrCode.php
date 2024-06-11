<?php

namespace App\Livewire\Dashboard\WhatsappConnection;

use App\Enums\Whatsapp\InstanceStatus;
use App\Enums\Whatsapp\QrCodeStatus;
use App\Models\WhatsappAccount;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
#[On('whatsapp-account-disconnected')]
class QrCode extends Component
{
    public ?string $status = null;

    #[Computed]
    public function whatsappAccount(): WhatsappAccount
    {
        return currentStore()->whatsappAccount;
    }

    #[Computed]
    public function qrCode(): ?string
    {
        $fourWhatsService = new FourWhatsService();

        try {
            $response = $fourWhatsService->instance(
                instanceId: $this->whatsappAccount->instance_id,
                instanceToken: $this->whatsappAccount->instance_token,
            )->qrCode();
        } catch (FourWhatsException) {
            return null;
        }

        if ($response['status'] === QrCodeStatus::AUTHENTICATED) {
            if ($this->status === InstanceStatus::DISCONNECTED->name) {
                $this->status = InstanceStatus::CONNECTED->name;
                $this->dispatch(
                    event: 'whatsapp-account-connected',
                );
            }

            return null;
        }

        return $response['qr_code'];
    }

    public function mount(): void
    {
        $this->status = $this->qrCode === null ? InstanceStatus::CONNECTED->name : InstanceStatus::DISCONNECTED->name;
    }

    public function render(): View
    {
        return view(
            view: 'livewire.dashboard.whatsapp-connection.qr-code',
        );
    }
}
