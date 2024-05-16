<?php

namespace App\Livewire\Concerns;

trait InteractsWithToasts
{
    public function successToast(string $action, string $model): void
    {
        $this->dispatch(
            event: 'alerts.success',
            message: __(
                key: "alerts.{$action}",
                replace: ['model' => __(key: "models.{$model}")],
            ),
        );
    }
}
