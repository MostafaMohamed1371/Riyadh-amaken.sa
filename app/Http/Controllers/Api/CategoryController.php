<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 15), 100);
        $categories = Category::latest()->paginate($perPage);
        $categories->getCollection()->transform(fn (Category $c) => $this->formatCategory($c));

        return response()->json($categories);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json(['data' => $this->formatCategory($category)]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')],
            'description' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'image.max' => 'The image may not be greater than 2MB.',
            'slug.unique' => 'This slug already exists.',
        ]);

        $validated['name'] = $validated['name'] ?? $validated['title'] ?? null;
        if (! $validated['name']) {
            return response()->json([
                'success' => false,
                'message' => 'The name or title field is required.',
            ], 422);
        }

        $validated['slug'] = isset($validated['slug'])
            ? $validated['slug']
            : Str::slug($validated['name']);

        if (Category::query()->where('slug', $validated['slug'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This slug already exists.',
            ], 422);
        }
        if ($request->hasFile('image')) {
            $validated['icon'] = $request->file('image')->store('categories', 'public');
        }
        unset($validated['title'], $validated['image']);

        $category = Category::create($validated);

        return response()->json(['data' => $this->formatCategory($category)], 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'title' => 'sometimes|required|string|max:255',
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($category->id)],
            'description' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'image.max' => 'The image may not be greater than 2MB.',
            'slug.unique' => 'This slug already exists.',
        ]);

        if (! isset($validated['name']) && isset($validated['title'])) {
            $validated['name'] = $validated['title'];
        }
        if (isset($validated['name']) && ! isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        if (isset($validated['slug'])) {
            $taken = Category::query()
                ->where('slug', $validated['slug'])
                ->where('id', '!=', $category->id)
                ->exists();
            if ($taken) {
                return response()->json([
                    'success' => false,
                    'message' => 'This slug already exists.',
                ], 422);
            }
        }

        if ($request->hasFile('image')) {
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            $validated['icon'] = $request->file('image')->store('categories', 'public');
        }
        unset($validated['title'], $validated['image']);

        $category->update($validated);

        return response()->json(['data' => $this->formatCategory($category)]);
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->icon) {
            Storage::disk('public')->delete($category->icon);
        }
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }

    private function formatCategory(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'title' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'icon' => $category->icon ? asset('storage/'.$category->icon) : null,
            'image' => $category->icon ? asset('storage/'.$category->icon) : null,
            'is_active' => $category->is_active,
            'sort_order' => $category->sort_order,
            'created_at' => $category->created_at?->toIso8601String(),
            'updated_at' => $category->updated_at?->toIso8601String(),
        ];
    }
}
