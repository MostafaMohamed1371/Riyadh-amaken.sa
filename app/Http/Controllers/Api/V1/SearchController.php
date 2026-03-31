<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EventListResource;
use App\Http\Resources\V1\PlaceListResource;
use App\Models\Event;
use App\Models\Place;
use App\Support\ApiResponse;

class SearchController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $query = trim((string) request('q'));
        $type = request('type', 'all');

        if ($query === '') {
            return $this->successResponse([
                'type' => $type,
                'places' => [],
                'events' => [],
            ]);
        }

        $payload = ['type' => $type];

        if (in_array($type, ['all', 'places'], true)) {
            $places = Place::active()->with(['category', 'tags'])
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('short_description', 'like', "%{$query}%")
                        ->orWhere('full_description', 'like', "%{$query}%");
                })
                ->take(20)
                ->get();

            $payload['places'] = PlaceListResource::collection($places);
        }

        if (in_array($type, ['all', 'events'], true)) {
            $events = Event::active()->with('tags')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('short_description', 'like', "%{$query}%")
                        ->orWhere('full_description', 'like', "%{$query}%");
                })
                ->take(20)
                ->get();

            $payload['events'] = EventListResource::collection($events);
        }

        return $this->successResponse($payload);
    }
}
