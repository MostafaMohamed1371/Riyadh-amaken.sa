<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreTagRequest;
use App\Http\Requests\Api\V1\Admin\UpdateTagRequest;
use App\Http\Resources\V1\TagResource;
use App\Models\Tag;
use App\Support\ApiResponse;

class TagController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $tags = Tag::orderBy('name')->paginate(request('per_page', 15));

        return $this->paginatedResponse(TagResource::collection($tags));
    }

    public function store(StoreTagRequest $request)
    {
        $tag = Tag::create($request->validated());

        return $this->successResponse(new TagResource($tag), 'Tag created.', 201);
    }

    public function show(Tag $tag)
    {
        return $this->successResponse(new TagResource($tag));
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return $this->successResponse(new TagResource($tag), 'Tag updated.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return $this->successResponse(null, 'Tag deleted.');
    }
}
