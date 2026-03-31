<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'location' => $this->location,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'start_time' => $this->start_time?->format('H:i'),
            'end_time' => $this->end_time?->format('H:i'),
            'ticket_price' => $this->ticket_price,
            'image' => $this->image,
            'is_featured' => $this->is_featured,
            'category' => $this->when(
                $this->relationLoaded('category'),
                fn () => $this->category
                    ? (new CategorySummaryResource($this->category))->resolve($request)
                    : null
            ),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
