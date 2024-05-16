<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\StoreMessageTemplate;
use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View
    {
        Gate::authorize(ability: 'viewAny', arguments: MessageTemplate::class);

        $templates = currentStore()->messageTemplates;
        $orderStatusesTemplates = $templates->filter(callback: fn (MessageTemplate $messageTemplate): bool => $messageTemplate->enum === StoreMessageTemplate::ORDER_STATUSES);
        $templates = $templates->reject(callback: fn (MessageTemplate $messageTemplate): bool => $messageTemplate->enum === StoreMessageTemplate::ORDER_STATUSES);

        return view(view: 'dashboard.pages.templates.index', data: [
            'templates' => $templates,
            'orderStatusesTemplates' => $orderStatusesTemplates,
        ]);
    }
}
