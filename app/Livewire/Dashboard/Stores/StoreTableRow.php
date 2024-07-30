<?php

namespace App\Livewire\Dashboard\Stores;

use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\Store;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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

    public string $newPassword;

    public string $newPasswordConfirmation;

    public function updateStore(): void
    {
        $this->authorize(
            ability: 'update',
            arguments: $this->store,
        );

        $this->validate(
            rules: [
                'fourWhatsProviderId' => ['required', 'integer'],
                'fourWhatsApiKey' => ['required', 'string'],
                'instanceId' => ['required', 'integer'],
                'instanceToken' => ['required', 'string'],
            ],
            attributes: [
                'fourWhatsProviderId' => __(
                    key: 'dashboard.pages.stores.columns.four_whats_provider_id',
                ),
                'fourWhatsApiKey' => __(
                    key: 'dashboard.pages.stores.columns.four_whats_api_key',
                ),
                'instanceId' => __(
                    key: 'dashboard.pages.stores.columns.whatsapp_instance_id',
                ),
                'instanceToken' => __(
                    key: 'dashboard.pages.stores.columns.whatsapp_instance_token',
                ),
            ],
        );

        $this->store->user->fourWhatsCredential()->update(
            values: [
                'provider_id' => $this->fourWhatsProviderId,
                'api_key' => $this->fourWhatsApiKey,
            ],
        );

        $this->store->whatsappAccount()->update(
            values: [
                'instance_id' => $this->instanceId,
                'instance_token' => $this->instanceToken,
            ],
        );

        $this->successToast(
            action: 'updated',
            model: 'stores.singular',
        );
    }

    public function updatePassword(): void
    {
        $this->authorize(
            ability: 'updatePassword',
            arguments: $this->store,
        );

        $this->validate(
            rules: [
                'newPassword' => ['required', Password::default(), 'same:newPasswordConfirmation'],
            ],
            attributes: [
                'newPassword' => __(
                    key: 'dashboard.pages.stores.columns.new_password',
                ),
                'newPasswordConfirmation' => __(
                    key: 'dashboard.pages.stores.columns.new_password_confirmation',
                ),
            ],
        );

        $this->store->user()->update(
            values: [
                'password' => Hash::make(
                    value: $this->newPassword,
                ),
            ],
        );

        $this->reset([
            'newPassword',
            'newPasswordConfirmation',
        ]);

        $this->successToast(
            action: 'updated',
            model: 'stores.singular',
        );
    }

    public function extendTrial(): void
    {
        $this->authorize(
            ability: 'extendTrial',
            arguments: $this->store,
        );

        try {
            (new FourWhatsService())
                ->instances(
                    apiKey: $this->fourWhatsApiKey,
                )
                ->renew(
                    email: $this->store->user->fourWhatsCredential->email,
                    instanceId: $this->instanceId,
                    packageId: 1,
                );
        } catch (FourWhatsException $e) {
            $this->customErrorToast(
                message: $e->getMessage(),
            );

            return;
        }

        $whatsappAccount = $this->store->whatsappAccount;
        $date = $whatsappAccount->expired_at->lessThanOrEqualTo(
            date: now(),
        ) ? now() : $whatsappAccount->expired_at;

        $whatsappAccount->update(
            attributes: [
                'expired_at' => $date->addDays(
                    value: 7,
                ),
            ],
        );

        $this->customSuccessToast(
            message: __(
                key: 'dashboard.pages.stores.index.trail_extended',
            ),
        );
    }

    public function toggle(): void
    {
        $this->authorize(
            ability: 'extendTrial',
            arguments: $this->store,
        );

        DB::transaction(
            callback: function (): void {
                $this->store->update(
                    attributes: [
                        'is_uninstalled' => ! $this->store->is_uninstalled,
                    ],
                );

                $this->store->user->update(
                    attributes: [
                        'is_uninstalled' => ! $this->store->user->is_uninstalled,
                    ],
                );
            },
        );

        $this->successToast(
            action: 'updated',
            model: 'stores.singular',
        );
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
        return view(
            view: 'livewire.dashboard.stores.store-table-row',
        );
    }
}
