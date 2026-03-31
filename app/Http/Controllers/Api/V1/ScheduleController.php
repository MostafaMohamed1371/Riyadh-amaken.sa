<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ReorderScheduleRequest;
use App\Http\Requests\Api\V1\ScheduleSuggestionsRequest;
use App\Http\Requests\Api\V1\StoreScheduleItemRequest;
use App\Http\Resources\V1\EventListResource;
use App\Http\Resources\V1\PlaceListResource;
use App\Http\Resources\V1\ScheduleItemResource;
use App\Models\Event;
use App\Models\Place;
use App\Models\ScheduleItem;
use App\Models\Tag;
use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ScheduleController extends Controller
{
    use ApiResponse;

    /**
     * Trip suggestions (public): events that overlap the visit window and places, optionally filtered by tag slugs (interests).
     */
    public function suggestions(ScheduleSuggestionsRequest $request)
    {
        $arrival = Carbon::parse($request->validated('arrival_date'))->startOfDay();
        $departure = Carbon::parse($request->validated('departure_date'))->endOfDay();
        $perPage = (int) ($request->validated('per_page') ?? 40);
        $interests = array_filter($request->validated('interests') ?? []);

        $tagIds = [];
        if ($interests !== []) {
            $tagIds = Tag::query()->whereIn('slug', $interests)->pluck('id')->all();
        }

        $eventsQuery = Event::query()
            ->active()
            ->with('tags')
            ->where(function ($q) use ($arrival, $departure) {
                $q->whereRaw('DATE(start_date) <= ?', [$departure->toDateString()])
                    ->whereRaw('COALESCE(DATE(end_date), DATE(start_date)) >= ?', [$arrival->toDateString()]);
            });

        if ($tagIds !== []) {
            $eventsQuery->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $tagIds));
        }

        $events = $eventsQuery->orderByDesc('is_featured')->orderBy('start_date')->limit($perPage)->get();

        $placesQuery = Place::query()->active()->with(['category', 'tags']);

        if ($tagIds !== []) {
            $placesQuery->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $tagIds));
        } else {
            $placesQuery->orderByDesc('is_featured');
        }

        $places = $placesQuery->latest()->limit($perPage)->get();

        return $this->successResponse([
            'arrival_date' => $arrival->toDateString(),
            'departure_date' => $departure->toDateString(),
            'interests' => $interests,
            'events' => EventListResource::collection($events),
            'places' => PlaceListResource::collection($places),
            'meta' => [
                'per_page' => $perPage,
                'events_returned' => $events->count(),
                'places_returned' => $places->count(),
            ],
        ]);
    }

    /**
     * Authenticated user's saved itinerary ("My Schedule").
     */
    public function index(Request $request)
    {
        $items = $request->user()->scheduleItems()->get();
        $items->loadMissing('scheduleable');
        $items->each(function (ScheduleItem $item) {
            if ($item->scheduleable instanceof Event) {
                $item->scheduleable->load('tags');
            } elseif ($item->scheduleable instanceof Place) {
                $item->scheduleable->load(['category', 'tags']);
            }
        });

        return $this->successResponse(ScheduleItemResource::collection($items));
    }

    public function store(StoreScheduleItemRequest $request)
    {
        $user = $request->user();
        $type = $request->validated('type');
        $id = (int) $request->validated('id');

        $model = $type === 'event'
            ? Event::query()->active()->whereKey($id)->firstOrFail()
            : Place::query()->active()->whereKey($id)->firstOrFail();

        $class = $model::class;

        $exists = ScheduleItem::query()
            ->where('user_id', $user->id)
            ->where('scheduleable_type', $class)
            ->where('scheduleable_id', $model->id)
            ->exists();

        if ($exists) {
            return $this->successResponse(null, 'Already in your schedule.', 200);
        }

        $nextOrder = (int) (ScheduleItem::query()->where('user_id', $user->id)->max('sort_order') ?? -1) + 1;

        $item = ScheduleItem::query()->create([
            'user_id' => $user->id,
            'scheduleable_type' => $class,
            'scheduleable_id' => $model->id,
            'sort_order' => $nextOrder,
        ]);

        $item->loadMissing('scheduleable');
        if ($item->scheduleable instanceof Event) {
            $item->scheduleable->load('tags');
        } else {
            $item->scheduleable->load(['category', 'tags']);
        }

        return $this->successResponse(new ScheduleItemResource($item), 'Added to your schedule.', 201);
    }

    public function destroy(Request $request, ScheduleItem $scheduleItem)
    {
        abort_unless($scheduleItem->user_id === $request->user()->id, 403);

        $scheduleItem->delete();

        return $this->successResponse(null, 'Removed from your schedule.');
    }

    public function reorder(ReorderScheduleRequest $request)
    {
        $user = $request->user();
        $ids = $request->validated('order');

        $existingIds = $user->scheduleItems()->pluck('id')->sort()->values()->all();
        $sortedRequestIds = collect($ids)->sort()->values()->all();
        if ($existingIds !== $sortedRequestIds) {
            throw ValidationException::withMessages([
                'order' => ['Order must include every schedule item id exactly once.'],
            ]);
        }

        DB::transaction(function () use ($ids, $user) {
            foreach ($ids as $position => $id) {
                ScheduleItem::query()
                    ->where('user_id', $user->id)
                    ->whereKey($id)
                    ->update(['sort_order' => $position]);
            }
        });

        return $this->successResponse(null, 'Order updated.');
    }
}
