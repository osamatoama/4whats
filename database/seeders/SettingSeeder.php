<?php

namespace Database\Seeders;

use App\Enums\SettingKey;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::query()->firstOrCreate(attributes: [
            'key' => SettingKey::SYSTEM_FOUR_WHATS_VOUCHER,
            'value' => config(key: 'services.four_whats.voucher'),
        ]);
    }
}
