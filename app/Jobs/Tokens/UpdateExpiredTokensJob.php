<?php

namespace App\Jobs\Tokens;

use App\Models\Token;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateExpiredTokensJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Token::query()->whereDate(column: 'expired_at', operator: '<=', value: now()->addDays(value: 2))->each(callback: function (Token $token): void {
            UpdateExpiredTokenJob::dispatch(token: $token);
        });
    }
}
