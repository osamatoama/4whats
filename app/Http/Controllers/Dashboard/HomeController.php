<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Message;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view(view: 'dashboard.pages.home.index', data: $this->getData());
    }

    protected function getData(): array
    {
        return auth()->user()->is_admin ? $this->getAdminData() : $this->getMerchantData();
    }

    protected function getAdminData(): array
    {
        return [
            'contactsCount' => Contact::count(),
            'messagesCount' => Message::count(),
        ];
    }

    protected function getMerchantData(): array
    {
        return [
            'contactsCount' => currentStore()->contacts()->count(),
            'messagesCount' => currentStore()->messages()->count(),
        ];
    }
}
