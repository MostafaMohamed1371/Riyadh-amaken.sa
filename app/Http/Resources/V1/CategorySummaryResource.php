<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Minimal category payload for schedule-related responses (id, title, image).
 */
class CategorySummaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $icon = $this->icon;

        $image = null;
        if ($icon) {
            $image = str_starts_with($icon, 'http://') || str_starts_with($icon, 'https://')
                ? $icon
                : asset('storage/'.$icon);
        }

        return [
            'id' => $this->id,
            'title' => $this->name,
            'image' => $image,
        ];
    }
}
