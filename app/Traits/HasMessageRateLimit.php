<?php

namespace App\Traits;

use Illuminate\Queue\Middleware\RateLimited;

trait HasMessageRateLimit
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    public function middleware(): array
    {
        return [new RateLimited('campaign-whatsapp-messages')];
    }

}