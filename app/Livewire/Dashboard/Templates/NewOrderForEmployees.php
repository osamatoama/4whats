<?php

namespace App\Livewire\Dashboard\Templates;

use App\Enums\SettingKey;
use App\Livewire\Concerns\InteractsWithToasts;
use App\Models\Template;
use Illuminate\View\View;
use Livewire\Component;

class NewOrderForEmployees extends Component
{
    use InteractsWithToasts;

    public Template $template;

    public ?string $mobiles;

    public function mount(): void
    {
        $this->mobiles = settings(storeId: currentStore()->id)
            ->value(
                key: SettingKey::from(value: $this->template->key),
            );
    }

    public function updated(): void
    {
        $this->authorize(ability: 'update', arguments: $this->template);

        $this->validate(rules: [
            'mobiles' => ['nullable', 'string'],
        ]);

        settings(storeId: currentStore()->id, eager: false)
            ->find(
                key: SettingKey::from(value: $this->template->key),
            )
            ->update(attributes: [
                'value' => $this->mobiles,
            ]);

        $this->successToast(action: 'updated', model: 'templates.singular');
    }

    public function render(): View
    {
        return view(view: 'livewire.dashboard.templates.new-order-for-employees');
    }
}
