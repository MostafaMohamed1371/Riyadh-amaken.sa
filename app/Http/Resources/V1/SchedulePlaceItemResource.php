<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Full place payload for schedule with category limited to id, title, image.
 */
class SchedulePlaceItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'full_description' => $this->full_description,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'city' => $this->city,
            'phone' => $this->phone,
            'website' => $this->website,
            'instagram' => $this->instagram,
            'image' => $this->image,
            'gallery' => $this->gallery,
            'rating' => $this->rating,
            'price_range' => $this->price_range,
            'working_hours' => $this->working_hours,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'category' => $this->when(
                $this->relationLoaded('category') && $this->category,
                fn () => new CategorySummaryResource($this->category)
            ),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
