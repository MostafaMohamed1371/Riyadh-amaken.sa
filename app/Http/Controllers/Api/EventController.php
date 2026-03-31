<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 15), 100);
        $events = Event::with('category')->latest('start_date')->paginate($perPage);
        $events->getCollection()->transform(fn (Event $e) => $this->formatEvent($e));

        return response()->json($events);
    }

    public function show(Event $event): JsonResponse
    {
        $event->load('category');

        return response()->json(['data' => $this->formatEvent($event)]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('events', 'slug')],
            'description' => 'nullable|string|max:5000',
            'short_description' => 'nullable|string|max:255',
            'full_description' => 'nullable|string|max:5000',
            'date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'time' => 'nullable|date_format:H:i',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'ticket_price' => 'nullable|numeric|min:0',
            'booking_url' => 'nullable|url|max:255',
            'image' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'category_id' => 'nullable|integer|exists:categories,id',
        ], [
            'title.required' => 'The title field is required.',
            'date.date' => 'The date is not valid (use YYYY-MM-DD).',
            'time.date_format' => 'The time must be in HH:mm format.',
            'slug.unique' => 'This slug already exists.',
        ]);

        $payload = [
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? Str::slug($validated['title']),
            'short_description' => $validated['short_description'] ?? $validated['description'] ?? null,
            'full_description' => $validated['full_description'] ?? $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'start_date' => $validated['start_date'] ?? $validated['date'] ?? now()->toDateString(),
            'end_date' => $validated['end_date'] ?? null,
            'start_time' => $validated['start_time'] ?? $validated['time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'ticket_price' => $validated['ticket_price'] ?? null,
            'booking_url' => $validated['booking_url'] ?? null,
            'image' => $validated['image'] ?? null,
            'organizer' => $validated['organizer'] ?? null,
            'is_featured' => $validated['is_featured'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
            'category_id' => $validated['category_id'] ?? null,
        ];

        if (Event::query()->where('slug', $payload['slug'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This slug already exists.',
            ], 422);
        }

        $event = Event::create($payload);
        $event->load('category');

        return response()->json(['data' => $this->formatEvent($event)], 201);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('events', 'slug')->ignore($event->id)],
            'description' => 'nullable|string|max:5000',
            'short_description' => 'nullable|string|max:255',
            'full_description' => 'nullable|string|max:5000',
            'date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'time' => 'nullable|date_format:H:i',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'ticket_price' => 'nullable|numeric|min:0',
            'booking_url' => 'nullable|url|max:255',
            'image' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'category_id' => 'nullable|integer|exists:categories,id',
        ], [
            'title.required' => 'The title field is required.',
            'date.date' => 'The date is not valid (use YYYY-MM-DD).',
            'time.date_format' => 'The time must be in HH:mm format.',
            'slug.unique' => 'This slug already exists.',
        ]);

        $payload = [];

        if (array_key_exists('title', $validated)) {
            $payload['title'] = $validated['title'];
            if (! array_key_exists('slug', $validated)) {
                $payload['slug'] = Str::slug($validated['title']);
            }
        }
        if (array_key_exists('slug', $validated)) {
            $payload['slug'] = $validated['slug'];
        }
        if (array_key_exists('description', $validated)) {
            $payload['short_description'] = $validated['description'];
            $payload['full_description'] = $validated['description'];
        }
        if (array_key_exists('short_description', $validated)) {
            $payload['short_description'] = $validated['short_description'];
        }
        if (array_key_exists('full_description', $validated)) {
            $payload['full_description'] = $validated['full_description'];
        }
        if (array_key_exists('date', $validated)) {
            $payload['start_date'] = $validated['date'];
        }
        if (array_key_exists('start_date', $validated)) {
            $payload['start_date'] = $validated['start_date'];
        }
        if (array_key_exists('end_date', $validated)) {
            $payload['end_date'] = $validated['end_date'];
        }
        if (array_key_exists('time', $validated)) {
            $payload['start_time'] = $validated['time'];
        }
        if (array_key_exists('start_time', $validated)) {
            $payload['start_time'] = $validated['start_time'];
        }
        if (array_key_exists('end_time', $validated)) {
            $payload['end_time'] = $validated['end_time'];
        }
        foreach (['location', 'ticket_price', 'booking_url', 'image', 'organizer', 'is_featured', 'is_active', 'category_id'] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = $validated[$field];
            }
        }

        if (isset($payload['slug'])) {
            $taken = Event::query()->where('slug', $payload['slug'])->where('id', '!=', $event->id)->exists();
            if ($taken) {
                return response()->json([
                    'success' => false,
                    'message' => 'This slug already exists.',
                ], 422);
            }
        }

        if (empty($payload['start_date']) && empty($event->start_date)) {
            $payload['start_date'] = now()->toDateString();
        }

        $event->update($payload);
        $event->load('category');

        return response()->json(['data' => $this->formatEvent($event)]);
    }

    public function destroy(Event $event): JsonResponse
    {
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully.',
        ]);
    }

    private function formatEvent(Event $event): array
    {
        $event->loadMissing('category');

        $categoryPayload = null;
        if ($event->category) {
            $icon = $event->category->icon;
            $image = null;
            if ($icon) {
                $image = str_starts_with($icon, 'http://') || str_starts_with($icon, 'https://')
                    ? $icon
                    : asset('storage/'.$icon);
            }
            $categoryPayload = [
                'id' => $event->category->id,
                'title' => $event->category->name,
                'image' => $image,
            ];
        }

        return [
            'id' => $event->id,
            'category_id' => $event->category_id,
            'category' => $categoryPayload,
            'title' => $event->title,
            'slug' => $event->slug,
            'description' => $event->short_description ?? $event->full_description,
            'short_description' => $event->short_description,
            'full_description' => $event->full_description,
            'date' => $event->start_date?->format('Y-m-d'),
            'start_date' => $event->start_date?->format('Y-m-d'),
            'end_date' => $event->end_date?->format('Y-m-d'),
            'time' => $event->start_time?->format('H:i'),
            'start_time' => $event->start_time?->format('H:i'),
            'end_time' => $event->end_time?->format('H:i'),
            'location' => $event->location,
            'ticket_price' => $event->ticket_price,
            'booking_url' => $event->booking_url,
            'image' => $event->image,
            'organizer' => $event->organizer,
            'is_featured' => $event->is_featured,
            'is_active' => $event->is_active,
            'created_at' => $event->created_at?->toIso8601String(),
            'updated_at' => $event->updated_at?->toIso8601String(),
        ];
    }
}
