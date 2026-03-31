<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Resources\V1\TagResource;
use App\Models\Category;
use App\Models\Place;
use App\Models\Tag;
use App\Support\ApiResponse;

class FilterController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->successResponse([
            'categories' => CategoryResource::collection(Category::active()->orderBy('sort_order')->get()),
            'tags' => TagResource::collection(Tag::orderBy('name')->get()),
            'price_ranges' => Place::query()->whereNotNull('price_range')->distinct()->pluck('price_range')->values(),
        ]);
    }
}
