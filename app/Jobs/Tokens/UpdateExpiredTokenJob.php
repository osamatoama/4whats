<?php

namespace App\Jobs\Tokens;

use App\Jobs\Concerns\InteractsWithException;
use App\Models\Token;
use App\Services\Salla\OAuth\SallaOAuthException;
use App\Services\Token\TokenService;
use App\Services\Zid\OAuth\ZidOAuthException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateExpiredTokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Token $token,
    ) {
        $this->maxAttempts = 5;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            (new TokenService())->getNewAccessToken($this->token);
        } catch (SallaOAuthException $e) {
            $this->handleException(
                e: new SallaOAuthException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Exception while updating salla access token',
                            "Token: {$this->token->id}",
                            "Reason: {$e->getMessage()}",
                        ],
                    ),
                    code: $e->getCode(),
                ),
            );
        } catch (ZidOAuthException $e) {
            $this->handleException(
                e: new ZidOAuthException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Exception while updating zid access token',
                            "Token: {$this->token->id}",
                            "Reason: {$e->getMessage()}",
                        ],
                    ),
                    code: $e->getCode(),
                ),
            );
        }
    }
}
