<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PlaceDetailResource;
use App\Http\Resources\V1\PlaceListResource;
use App\Models\Place;
use App\Support\ApiResponse;

class PlaceController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $places = Place::query()
            ->with(['category', 'tags'])
            ->active()
            ->when(request('category'), fn ($q, $v) => $q->whereHas('category', fn ($sub) => $sub->where('slug', $v)))
            ->when(request('search'), fn ($q, $v) => $q->where(function ($sub) use ($v) {
                $sub->where('name', 'like', "%{$v}%")
                    ->orWhere('short_description', 'like', "%{$v}%")
                    ->orWhere('full_description', 'like', "%{$v}%");
            }))
            ->when(request('tag'), fn ($q, $v) => $q->whereHas('tags', fn ($sub) => $sub->where('slug', $v)))
            ->when(request('city'), fn ($q, $v) => $q->where('city', $v))
            ->when(request()->has('featured'), fn ($q) => $q->where('is_featured', filter_var(request('featured'), FILTER_VALIDATE_BOOLEAN)))
            ->when(request('price_range'), fn ($q, $v) => $q->where('price_range', $v))
            ->latest()
            ->paginate(request('per_page', 15));

        return $this->paginatedResponse(PlaceListResource::collection($places));
    }

    public function featured()
    {
        $places = Place::active()->where('is_featured', true)->with(['category', 'tags'])->latest()->paginate(request('per_page', 15));

        return $this->paginatedResponse(PlaceListResource::collection($places));
    }

    public function show(string $slug)
    {
        $place = Place::query()->where('slug', $slug)->firstOrFail();

        abort_if(! $place->is_active, 404);

        $place->load(['category', 'tags']);

        return $this->successResponse(new PlaceDetailResource($place));
    }
}
