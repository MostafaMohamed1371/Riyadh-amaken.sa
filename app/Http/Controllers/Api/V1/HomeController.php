<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BannerResource;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Resources\V1\EventListResource;
use App\Http\Resources\V1\PlaceListResource;
use App\Http\Resources\V1\TagResource;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Event;
use App\Models\Place;
use App\Models\Tag;
use App\Support\ApiResponse;

class HomeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->successResponse([
            'banners' => BannerResource::collection(Banner::active()->orderBy('sort_order')->get()),
            'featured_categories' => CategoryResource::collection(Category::active()->orderBy('sort_order')->take(6)->get()),
            'featured_places' => PlaceListResource::collection(Place::active()->where('is_featured', true)->with(['category', 'tags'])->latest()->take(8)->get()),
            'featured_events' => EventListResource::collection(Event::active()->where('is_featured', true)->with('tags')->orderBy('start_date')->take(8)->get()),
            'popular_tags' => TagResource::collection(Tag::withCount(['places', 'events'])->orderByDesc('places_count')->orderByDesc('events_count')->take(10)->get()),
        ]);
    }
}
