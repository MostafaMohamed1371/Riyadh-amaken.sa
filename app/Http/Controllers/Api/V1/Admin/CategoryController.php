<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreCategoryRequest;
use App\Http\Requests\Api\V1\Admin\UpdateCategoryRequest;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use App\Support\ApiResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $categories = Category::orderBy('sort_order')->paginate(request('per_page', 15));

        return $this->paginatedResponse(CategoryResource::collection($categories));
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return $this->successResponse(new CategoryResource($category), 'Category created.', 201);
    }

    public function show(Category $category)
    {
        return $this->successResponse(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return $this->successResponse(new CategoryResource($category), 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return $this->successResponse(null, 'Category deleted.');
    }
}
