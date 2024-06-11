<?php

namespace App\Http\Controllers\Api\V1\Zid;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\WidgetResource;
use App\Models\Store;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function __invoke(Request $request): WidgetResource
    {
        $widget = Store::query()
            ->zid(
                providerUUID: $request->header(
                    key: 'X-Zid-Provider-UUID',
                    default: 0,
                ),
            )
            ->firstOrFail()
            ->widget;

        return WidgetResource::make(
            resource: $widget,
        );
    }
}
