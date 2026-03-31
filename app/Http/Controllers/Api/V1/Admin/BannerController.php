<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StoreBannerRequest;
use App\Http\Requests\Api\V1\Admin\UpdateBannerRequest;
use App\Http\Resources\V1\BannerResource;
use App\Models\Banner;
use App\Support\ApiResponse;

class BannerController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $banners = Banner::orderBy('sort_order')->paginate(request('per_page', 15));

        return $this->paginatedResponse(BannerResource::collection($banners));
    }

    public function store(StoreBannerRequest $request)
    {
        $banner = Banner::create($request->validated());

        return $this->successResponse(new BannerResource($banner), 'Banner created.', 201);
    }

    public function show(Banner $banner)
    {
        return $this->successResponse(new BannerResource($banner));
    }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $banner->update($request->validated());

        return $this->successResponse(new BannerResource($banner), 'Banner updated.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();

        return $this->successResponse(null, 'Banner deleted.');
    }
}
