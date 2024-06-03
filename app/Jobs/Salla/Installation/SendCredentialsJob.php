<?php

namespace App\Jobs\Salla\Installation;

use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCredentialsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public string $password,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new UserService())->sendCredentials(
            user: $this->user,
            password: $this->password,
        );
    }
}
