<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\SettingKey;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view(
            view: 'dashboard.pages.settings.index',
            data: $this->getData(),
        );
    }

    protected function getData(): array
    {
        return auth()->user()->is_admin ? $this->getAdminData() : $this->getMerchantData();
    }

    protected function getAdminData(): array
    {
        return [
            'voucher' => settings()->find(
                key: SettingKey::SYSTEM_FOUR_WHATS_VOUCHER,
            ),
        ];
    }

    protected function getMerchantData(): array
    {
        return [
            'widget' => currentStore()->widget,
        ];
    }
}
