<?php

namespace App\Jobs\IncomingWebhook;

use App\Dto\IncomingWebhookDto;
use App\Services\IncomingWebhook\IncomingWebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        (new IncomingWebhookService())
            ->create(
                incomingWebhookDto: $this->incomingWebhookDto,
            );
    }
}
