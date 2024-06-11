<?php

namespace App\Livewire\Dashboard\Settings;

use App\Dto\SettingDto;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\Setting;
use App\Services\Setting\SettingService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class TextTableRow extends Component
{
    use InteractsWithToasts;

    public Setting $setting;

    public string $value;

    public function updateSetting(): void
    {
        Gate::authorize(
            ability: 'update',
            arguments: $this->setting,
        );

        $this->validate(
            rules: [
                'value' => ['required', 'string'],
            ],
            attributes: [
                'value' => __(
                    key: 'dashboard.pages.settings.columns.value',
                ),
            ],
        );

        (new SettingService())->update(
            setting: $this->setting,
            settingDto: SettingDto::fromModel(
                setting: $this->setting,
                value: $this->value,
            ),
        );

        $this->successToast(
            action: 'updated',
            model: 'settings.singular',
        );
    }

    public function mount(): void
    {
        $this->value = $this->setting->value;
    }

    public function render(): View
    {
        return view(
            view: 'livewire.dashboard.settings.text-table-row',
        );
    }
}
