<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(): View
    {
        return view(view: 'dashboard.pages.stores.index');
    }
}
