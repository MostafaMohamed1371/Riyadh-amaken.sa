<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TagResource;
use App\Models\Tag;
use App\Support\ApiResponse;

class TagController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->successResponse(
            TagResource::collection(Tag::orderBy('name')->get())
        );
    }
}
