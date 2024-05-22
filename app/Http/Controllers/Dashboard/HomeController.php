<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view(view: 'dashboard.pages.home.index', data: [
            'contactsCount' => currentStore()->contacts()->count(),
            'messagesCount' => currentStore()->messages()->count(),
        ]);
    }
}
