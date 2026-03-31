<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Resources\V1\GalleryItemResource;
use App\Http\Resources\V1\SliderResource;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Slider;
use App\Support\ApiResponse;

class HomeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $categories = Category::active()
            ->withCount(['places', 'activities'])
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse([
            'sliders' => SliderResource::collection(Slider::query()->latest()->get()),
            'categories' => CategoryResource::collection($categories),
            'gallery' => GalleryItemResource::collection(Gallery::query()->latest()->get()),
        ]);
    }
}
