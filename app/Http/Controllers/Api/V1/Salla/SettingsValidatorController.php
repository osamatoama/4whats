<?php

namespace App\Http\Controllers\Api\V1\Salla;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class SettingsValidatorController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make(
            data: $request->input(key: 'data'),
            rules: [
                'widget_message' => ['nullable', 'string', 'max:255'],
                'widget_color' => ['required', 'hex_color'],
                'widget_is_enabled' => ['required', 'boolean'],
            ],
        );

        if ($validator->fails()) {
            return response()->json(data: [
                'status' => 422,
                'success' => false,
                'code' => 'error',
                'message' => 'خطأ في التحقق، يرجى مراجعة البيانات المدخلة.',
                'fields' => Arr::mapWithKeys(array: $validator->messages()->toArray(), callback: fn(array $messages, string $field): array => [$field => $messages[0]]),
            ], status: 422);
        }

        return response()->json();
    }
}
