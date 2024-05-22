<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\MessageTemplate;
use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View
    {
        Gate::authorize(ability: 'viewAny', arguments: Template::class);

        $templates = currentStore()->templates;
        $orderStatusesTemplates = $templates->filter(callback: fn (Template $template): bool => $template->enum === MessageTemplate::ORDER_STATUSES);
        $templates = $templates->reject(callback: fn (Template $template): bool => $template->enum === MessageTemplate::ORDER_STATUSES);

        return view(view: 'dashboard.pages.templates.index', data: [
            'templates' => $templates,
            'orderStatusesTemplates' => $orderStatusesTemplates,
        ]);
    }
}
