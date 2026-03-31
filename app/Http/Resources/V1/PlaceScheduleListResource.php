<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Place list row for schedule suggestions; category is id, title, image only.
 */
class PlaceScheduleListResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'city' => $this->city,
            'image' => $this->image,
            'rating' => $this->rating,
            'price_range' => $this->price_range,
            'is_featured' => $this->is_featured,
            'category' => $this->when(
                $this->relationLoaded('category') && $this->category,
                fn () => new CategorySummaryResource($this->category)
            ),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
