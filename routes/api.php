<?php

use App\Http\Controllers\Api\ActivityController as LegacyActivityController;
use App\Http\Controllers\Api\AuthController as LegacyAuthController;
use App\Http\Controllers\Api\CategoryController as LegacyCategoryController;
use App\Http\Controllers\Api\EventController as LegacyEventController;
use App\Http\Controllers\Api\GalleryController as LegacyGalleryController;
use App\Http\Controllers\Api\SettingController as LegacySettingController;
use App\Http\Controllers\Api\SliderController as LegacySliderController;
use App\Http\Controllers\Api\UserController as LegacyUserController;
use App\Http\Controllers\Api\V1\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Api\V1\Admin\PlaceController as AdminPlaceController;
use App\Http\Controllers\Api\V1\Admin\TagController as AdminTagController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\FilterController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\PlaceController;
use App\Http\Controllers\Api\V1\ScheduleController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\TagController;
use Illuminate\Support\Facades\Route;

Route::post('register', [LegacyAuthController::class, 'register']);
Route::post('login', [LegacyAuthController::class, 'login']);
// Public read API (no /v1 prefix; slug routes use a leading letter so numeric IDs stay for legacy)
Route::get('home', [HomeController::class, 'index']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{slug}', [CategoryController::class, 'show'])->where('slug', '[a-zA-Z][a-zA-Z0-9\-]*');
Route::get('places/featured', [PlaceController::class, 'featured']);
Route::get('places', [PlaceController::class, 'index']);
Route::get('places/{slug}', [PlaceController::class, 'show'])->where('slug', '[a-zA-Z][a-zA-Z0-9\-]*');
Route::get('events/featured', [EventController::class, 'featured']);
Route::get('events', [EventController::class, 'index']);
Route::get('events/{slug}', [EventController::class, 'show'])->where('slug', '[a-zA-Z][a-zA-Z0-9\-]*');
Route::get('tags', [TagController::class, 'index']);
Route::get('search', [SearchController::class, 'index']);
Route::get('filters', [FilterController::class, 'index']);
Route::get('schedule/suggestions', [ScheduleController::class, 'suggestions']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LegacyAuthController::class, 'logout']);
    Route::get('user', [LegacyAuthController::class, 'user']);
    Route::get('schedule', [ScheduleController::class, 'index']);
    Route::post('schedule', [ScheduleController::class, 'store']);
    Route::put('schedule/reorder', [ScheduleController::class, 'reorder']);
    Route::delete('schedule/{scheduleItem}', [ScheduleController::class, 'destroy']);
    Route::apiResource('sliders', LegacySliderController::class);
    Route::apiResource('categories', LegacyCategoryController::class)->except(['index', 'show']);
    Route::get('categories/{category}', [LegacyCategoryController::class, 'show']);
    Route::apiResource('gallery', LegacyGalleryController::class);
    Route::apiResource('activities', LegacyActivityController::class);
    Route::apiResource('events', LegacyEventController::class)->except(['index', 'show']);
    Route::get('events/{event}', [LegacyEventController::class, 'show']);
    Route::get('settings', [LegacySettingController::class, 'index']);
    Route::put('settings', [LegacySettingController::class, 'update']);
    Route::apiResource('users', LegacyUserController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('places', AdminPlaceController::class);
    Route::apiResource('banners', AdminBannerController::class);
    Route::apiResource('tags', AdminTagController::class);
});
