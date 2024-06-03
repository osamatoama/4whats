<?php

namespace App\Services\Setting;

use App\Dto\SettingDto;
use App\Enums\ProviderType;
use App\Enums\SettingKey;
use App\Models\Setting;

class SettingService
{
    public function create(SettingDto $settingDto): Setting
    {
        return Setting::query()->create(
            attributes: [
                'store_id' => $settingDto->storeId,
                'key' => $settingDto->settingKey,
                'value' => $settingDto->value,
            ],
        );
    }

    /**
     * @return array<Setting>
     */
    public function createDefaultSettings(int $storeId, ProviderType $providerType): array
    {
        return match ($providerType) {
            ProviderType::SALLA => $this->createDefaultSettingsForSalla(
                storeId: $storeId,
            ),
            ProviderType::ZID => $this->createDefaultSettingsForZid(
                storeId: $storeId,
            ),
        };
    }

    /**
     * @return array<Setting>
     */
    protected function createDefaultSettingsForSalla(int $storeId): array
    {
        return [
            $this->create(
                settingDto: new SettingDto(
                    storeId: $storeId,
                    settingKey: SettingKey::STORE_SALLA_CUSTOM_REVIEW_ORDER,
                    value: null,
                ),
            ),
            $this->create(
                settingDto: new SettingDto(
                    storeId: $storeId,
                    settingKey: SettingKey::STORE_SALLA_CUSTOM_NEW_ORDER_FOR_EMPLOYEES,
                    value: null,
                ),
            ),
        ];
    }

    /**
     * @return array<Setting>
     */
    protected function createDefaultSettingsForZid(int $storeId): array
    {
        return []; // TODO:createDefaultSettingsForZid
    }
}
