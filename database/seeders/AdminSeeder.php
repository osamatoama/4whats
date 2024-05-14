<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create(attributes: [
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ])->assignRole(UserRole::ADMIN->asModel());
    }
}
