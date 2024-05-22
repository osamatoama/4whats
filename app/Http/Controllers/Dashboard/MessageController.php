<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MessageHistory;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        Gate::authorize(ability: 'viewAny', arguments: MessageHistory::class);

        return view(view: 'dashboard.pages.messages.index');
    }
}
