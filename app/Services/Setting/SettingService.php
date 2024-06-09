<?php

namespace App\Services\Setting;

use App\Dto\SettingDto;
use App\Enums\SettingKey;
use App\Models\Setting;
use App\Models\Store;

class SettingService
{
    public function create(SettingDto $settingDto): Setting
    {
        return Setting::query()->create(
            attributes: [
                'store_id' => $settingDto->storeId,
                'key' => $settingDto->key,
                'value' => $settingDto->value,
            ],
        );
    }

    public function update(Setting $setting, SettingDto $settingDto): Setting
    {
        $setting->update(
            attributes: [
                'value' => $settingDto->value,
            ],
        );

        return $setting;
    }

    /**
     * @return Setting[]
     */
    public function createDefaultSettings(int $storeId): array
    {
        return [
            $this->create(
                settingDto: new SettingDto(
                    storeId: $storeId,
                    key: SettingKey::STORE_ORDER_STATUS_ID_FOR_REVIEW_ORDER_MESSAGE,
                    value: null,
                ),
            ),
            $this->create(
                settingDto: new SettingDto(
                    storeId: $storeId,
                    key: SettingKey::STORE_EMPLOYEES_MOBILES_FOR_NEW_ORDER_MESSAGE,
                    value: null,
                ),
            ),
        ];
    }

    public static function updateOrderStatusId(Store $store, int $orderStatuesId): void
    {
        $store->settings()
            ->where(
                column: 'key',
                operator: '=',
                value: SettingKey::STORE_ORDER_STATUS_ID_FOR_REVIEW_ORDER_MESSAGE,
            )
            ->update(
                values: [
                    'value' => $orderStatuesId,
                ],
            );
    }

    public static function updateEmployeesMobiles(Store $store, ?string $mobiles): void
    {
        $store->settings()
            ->where(
                column: 'key',
                operator: '=',
                value: SettingKey::STORE_EMPLOYEES_MOBILES_FOR_NEW_ORDER_MESSAGE,
            )
            ->update(
                values: [
                    'value' => $mobiles,
                ],
            );
    }
}
