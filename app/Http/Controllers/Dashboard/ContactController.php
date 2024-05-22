<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        Gate::authorize(ability: 'viewAny', arguments: Contact::class);

        return view(view: 'dashboard.pages.contacts.index');
    }
}
