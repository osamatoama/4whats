<?php

namespace App\Http\Controllers\Api\V1\Salla;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $widget = Store::query()->salla(providerId: $request->query(key: 'store_id', default: 0))->firstOrFail()->widget;

        return response()->json(data: [
            'message' => $widget->message,
            'color' => $widget->color,
            'is_enabled' => $widget->is_enabled,
        ]);
    }
}
