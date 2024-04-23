<?php

namespace App\Jobs\Concerns;

use Exception;

trait HandleException
{
    public int $tries = 0;

    public ?int $maxAttempts = null;

    public bool $shouldLog = true;

    protected function handleException(Exception $e): void
    {
        if ($this->shouldLog) {
            logger()->error(message: $e->getMessage());
        }

        if ($this->isAttemptedTooManyTimes()) {
            $this->fail(exception: $e);

            return;
        }

        $this->release(delay: $this->getDelayInSeconds(code: $e->getCode()));
    }

    protected function isAttemptedTooManyTimes(): bool
    {
        return $this->maxAttempts !== null && $this->attempts() >= $this->maxAttempts;
    }

    protected function getDelayInSeconds(int $code): int
    {
        return match ($code) {
            429 => 60,
            default => 0,
        };
    }
}
