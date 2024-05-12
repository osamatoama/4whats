<?php

namespace App\Jobs\FourWhats;

use App\Jobs\Concerns\InteractsWithException;
use App\Models\User;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FourWhatsCreateUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public string $mobile,
        public string $password,
    ) {
        $this->maxAttempts = 1;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new FourWhatsService();

        try {
            $response = $service->user()->create(
                name: $this->user->name,
                email: $this->user->email,
                mobile: $this->mobile,
                password: $this->password,
            );
        } catch (FourWhatsException $e) {
            $this->handleException(
                e: new FourWhatsException(
                    message: "Exception while creating four whats user | User: {$this->user->id} | Message: {$e->getMessage()}",
                    code: $e->getCode(),
                ),
            );

            return;
        }

        $this->user->update([
            'four_whats_id' => $response['user']['id'],
            'four_whats_api_key' => $response['user']['apikey'],
        ]);
    }
}
