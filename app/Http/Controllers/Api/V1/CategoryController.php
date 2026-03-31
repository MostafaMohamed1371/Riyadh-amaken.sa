<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use App\Support\ApiResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $categories = Category::active()->withCount('places')->orderBy('sort_order')->paginate(request('per_page', 15));

        return $this->paginatedResponse(CategoryResource::collection($categories));
    }

    public function show(string $slug)
    {
        $category = Category::active()->withCount('places')->where('slug', $slug)->firstOrFail();

        return $this->successResponse(new CategoryResource($category));
    }
}
