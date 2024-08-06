<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class WidgetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'mobile' => Str::replaceEnd(
                search: '+',
                replace: '',
                subject: $this->resource->mobile,
            ),
            'message' => $this->resource->message,
            'color' => $this->resource->color,
            'is_enabled' => $this->resource->is_enabled,
        ];
    }
}
