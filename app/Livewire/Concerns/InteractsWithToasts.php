<?php

namespace App\Livewire\Concerns;

trait InteractsWithToasts
{
    public function successToast(string $action, string $model): void
    {
        $this->customSuccessToast(
            message: __(
                key: "toasts.{$action}",
                replace: ['model' => __(key: "models.{$model}")],
            ),
        );
    }

    public function warningToast(string $action, string $model): void
    {
        $this->customWarningToast(
            message: __(
                key: "toasts.{$action}",
                replace: ['model' => __(key: "models.{$model}")],
            ),
        );
    }

    public function errorToast(string $action, string $model): void
    {
        $this->customErrorToast(
            message: __(
                key: "toasts.{$action}",
                replace: ['model' => __(key: "models.{$model}")],
            ),
        );
    }

    public function customSuccessToast(string $message): void
    {
        $this->dispatch(
            event: 'toasts.success',
            message: $message,
        );
    }

    public function customWarningToast(string $message): void
    {
        $this->dispatch(
            event: 'toasts.warning',
            message: $message,
        );
    }

    public function customErrorToast(string $message): void
    {
        $this->dispatch(
            event: 'toasts.error',
            message: $message,
        );
    }
}
