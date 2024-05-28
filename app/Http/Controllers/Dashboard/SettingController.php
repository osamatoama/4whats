<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view(view: 'dashboard.pages.settings.index', data: $this->getData());
    }

    protected function getData(): array
    {
        return auth()->user()->is_admin ? $this->getAdminData() : $this->getMerchantData();
    }

    protected function getAdminData(): array
    {
        return [];
    }

    protected function getMerchantData(): array
    {
        return [
            'widget' => currentStore()->widget,
        ];
    }
}
