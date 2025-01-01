<?php

namespace App\Jobs\IncomingWebhook;

use App\Dto\IncomingWebhookDto;
use App\Services\IncomingWebhook\IncomingWebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SaveIncomingWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly IncomingWebhookDto $incomingWebhookDto,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            app(
                abstract: IncomingWebhookService::class
            )->create(
                incomingWebhookDto: $this->incomingWebhookDto,
            );
        } catch (Throwable $e) {
            logger()->error('SaveIncomingWebhookJob failed', [
                'error'       => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
