<?php

namespace App\Livewire\Dashboard\Campaigns;

use App\Enums\CampaignType;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\User;
use App\Services\Campaigns\CampaignsService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class SendingForm extends Component
{
    use InteractsWithToasts;

    public array $types;

    public CampaignType $currentType;

    public string $message;

    public function sendCampaign(): void
    {
        Gate::authorize(
            ability: 'sendCampaigns',
            arguments: User::class,
        );

        $this->validate(
            rules: [
                'message' => ['required', 'string'],
            ],
            attributes: [
                'message' => __(
                    key: 'dashboard.pages.campaigns.send.columns.message.label'
                ),
            ],
        );

        if (currentStore()->is_expired) {
            $this->customErrorToast(
                message: __(
                    key: 'dashboard.common.store_expired_message',
                ),
            );

            return;
        }

        $service = new CampaignsService(
            store: currentStore(),
        );

        $service->send(
            campaignType: $this->currentType,
            message: $this->message,
        );

        $this->dispatch(
            event: 'campaign-started',
        );

        $this->customSuccessToast(
            message: __(
                key: 'dashboard.pages.campaigns.send.messages.sending',
            ),
        );
    }

    public function updated(): void
    {
        $this->validate(
            rules: [
                'currentType' => [
                    'required',
                    Rule::enum(
                        type: CampaignType::class,
                    ),
                ],
            ],
            attributes: [
                'currentType' => __(
                    key: 'dashboard.pages.campaigns.send.columns.type'
                ),
            ],
        );
    }

    public function mount(): void
    {
        $this->types = CampaignType::cases();
        $this->currentType = Arr::first(
            array: $this->types,
        );
    }

    public function render(): View
    {
        return view(
            view: 'livewire.dashboard.campaigns.sending-form',
        );
    }
}
