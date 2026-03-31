<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EventDetailResource;
use App\Http\Resources\V1\EventListResource;
use App\Models\Event;
use App\Support\ApiResponse;

class EventController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $events = Event::query()
            ->with(['tags', 'category'])
            ->active()
            ->when(request('search'), fn ($q, $v) => $q->where(function ($sub) use ($v) {
                $sub->where('title', 'like', "%{$v}%")
                    ->orWhere('short_description', 'like', "%{$v}%")
                    ->orWhere('full_description', 'like', "%{$v}%");
            }))
            ->when(request('from_date'), fn ($q, $v) => $q->whereDate('start_date', '>=', $v))
            ->when(request('to_date'), fn ($q, $v) => $q->whereDate('start_date', '<=', $v))
            ->when(request()->has('featured'), fn ($q) => $q->where('is_featured', filter_var(request('featured'), FILTER_VALIDATE_BOOLEAN)))
            ->orderBy('start_date')
            ->paginate(request('per_page', 15));

        return $this->paginatedResponse(EventListResource::collection($events));
    }

    public function featured()
    {
        $events = Event::active()->where('is_featured', true)->with('tags')->orderBy('start_date')->paginate(request('per_page', 15));

        return $this->paginatedResponse(EventListResource::collection($events));
    }

    public function show(string $slug)
    {
        $event = Event::query()->where('slug', $slug)->firstOrFail();

        abort_if(! $event->is_active, 404);

        $event->load(['tags', 'category']);

        return $this->successResponse(new EventDetailResource($event));
    }
}
