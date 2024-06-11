<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\InteractsWithToasts;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Livewire\Component;

class UpdatePasswordForm extends Component
{
    use InteractsWithToasts;

    public string $currentPassword;

    public string $newPassword;

    public string $newPasswordConfirmation;

    public function updatePassword(): void
    {
        $this->validate(
            rules: [
                'currentPassword' => ['required', 'current_password'],
                'newPassword' => ['required', Password::default(), 'same:newPasswordConfirmation'],
            ],
            attributes: [
                'currentPassword' => __(
                    key: 'dashboard.pages.settings.index.password.current_password',
                ),
                'newPassword' => __(
                    key: 'dashboard.pages.settings.index.password.new_password',
                ),
                'newPasswordConfirmation' => __(
                    key: 'dashboard.pages.settings.index.password.new_password_confirmation',
                ),
            ],
        );

        auth()->user()->update(
            attributes: [
                'password' => $this->newPassword,
            ],
        );

        $this->reset();

        $this->customSuccessToast(
            message: __(
                key: 'dashboard.pages.settings.index.password.updated',
            ),
        );
    }

    public function render(): View
    {
        return view(
            view: 'livewire.dashboard.update-password-form',
        );
    }
}
