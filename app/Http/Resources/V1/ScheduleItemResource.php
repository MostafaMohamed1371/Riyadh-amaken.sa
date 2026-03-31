<?php

namespace App\Http\Resources\V1;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $scheduleable = $this->scheduleable;
        $type = $scheduleable instanceof Event ? 'event' : 'place';

        return [
            'id' => $this->id,
            'sort_order' => $this->sort_order,
            'type' => $type,
            'item' => $type === 'event'
                ? new EventListResource($scheduleable)
                : new PlaceListResource($scheduleable),
        ];
    }
}
