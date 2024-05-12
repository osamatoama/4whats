<?php

namespace Database\Seeders;

use App\Enums\Settings\SystemSettings;
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
            'key' => SystemSettings::FOUR_WHATS_VOUCHER->value,
            'value' => config(key: 'services.four_whats.voucher'),
        ]);
    }
}
