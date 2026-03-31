<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreEventRequest;
use App\Http\Requests\Api\V1\Admin\UpdateEventRequest;
use App\Http\Resources\V1\EventDetailResource;
use App\Http\Resources\V1\EventListResource;
use App\Models\Event;
use App\Support\ApiResponse;

class EventController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $events = Event::with('tags')->latest()->paginate(request('per_page', 15));

        return $this->paginatedResponse(EventListResource::collection($events));
    }

    public function store(StoreEventRequest $request)
    {
        $payload = $request->validated();
        $tagIds = $payload['tag_ids'] ?? [];
        unset($payload['tag_ids']);

        $event = Event::create($payload);
        $event->tags()->sync($tagIds);
        $event->load('tags');

        return $this->successResponse(new EventDetailResource($event), 'Event created.', 201);
    }

    public function show(Event $event)
    {
        $event->load('tags');

        return $this->successResponse(new EventDetailResource($event));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $payload = $request->validated();
        $tagIds = $payload['tag_ids'] ?? null;
        unset($payload['tag_ids']);

        $event->update($payload);
        if (is_array($tagIds)) {
            $event->tags()->sync($tagIds);
        }
        $event->load('tags');

        return $this->successResponse(new EventDetailResource($event), 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return $this->successResponse(null, 'Event deleted.');
    }
}
