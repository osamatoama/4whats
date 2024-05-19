<?php

namespace App\Livewire\Concerns;

trait InteractsWithToasts
{
    public function successToast(string $action, string $model): void
    {
        $this->dispatch(
            event: 'toasts.success',
            message: __(
                key: "toasts.{$action}",
                replace: ['model' => __(key: "models.{$model}")],
            ),
        );
    }
}
