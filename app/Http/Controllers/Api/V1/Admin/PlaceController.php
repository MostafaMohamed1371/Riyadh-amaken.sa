<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StorePlaceRequest;
use App\Http\Requests\Api\V1\Admin\UpdatePlaceRequest;
use App\Http\Resources\V1\PlaceDetailResource;
use App\Http\Resources\V1\PlaceListResource;
use App\Models\Place;
use App\Support\ApiResponse;

class PlaceController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $places = Place::with(['category', 'tags'])->latest()->paginate(request('per_page', 15));

        return $this->paginatedResponse(PlaceListResource::collection($places));
    }

    public function store(StorePlaceRequest $request)
    {
        $payload = $request->validated();
        $tagIds = $payload['tag_ids'] ?? [];
        unset($payload['tag_ids']);

        $place = Place::create($payload);
        $place->tags()->sync($tagIds);
        $place->load(['category', 'tags']);

        return $this->successResponse(new PlaceDetailResource($place), 'Place created.', 201);
    }

    public function show(Place $place)
    {
        $place->load(['category', 'tags']);

        return $this->successResponse(new PlaceDetailResource($place));
    }

    public function update(UpdatePlaceRequest $request, Place $place)
    {
        $payload = $request->validated();
        $tagIds = $payload['tag_ids'] ?? null;
        unset($payload['tag_ids']);

        $place->update($payload);
        if (is_array($tagIds)) {
            $place->tags()->sync($tagIds);
        }
        $place->load(['category', 'tags']);

        return $this->successResponse(new PlaceDetailResource($place), 'Place updated.');
    }

    public function destroy(Place $place)
    {
        $place->delete();

        return $this->successResponse(null, 'Place deleted.');
    }
}
