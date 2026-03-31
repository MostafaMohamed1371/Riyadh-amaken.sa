# Riyadh Amaken Backend API

## 1) Project structure overview

Core API implementation lives in:

- `app/Http/Controllers/Api/V1` - Public API controllers
- `app/Http/Controllers/Api/V1/Admin` - Admin auth and CRUD controllers
- `app/Http/Requests/Api/V1/Admin` - Form Request validation classes
- `app/Http/Resources/V1` - Response resource transformers
- `app/Models` - Eloquent models and relationships
- *(optional)* `app/Http/Middleware/EnsureAdmin.php` - not applied on API routes (Sanctum only for management)
- `app/Support/ApiResponse.php` - Unified API response helper
- `database/migrations` - Schema and upgrades
- `database/seeders` and `database/factories` - Seed and fake data
- `routes/api.php` - public read routes under `api/` (no `/v1`); management under `api/manage/`

## 2) Migrations included

Created/updated schema for:

- `categories`
- `places`
- `events`
- `tags`
- `banners`
- `place_tag` (pivot)
- `event_tag` (pivot)
- `users.is_admin`

## 3) Models and relationships

- `Category` -> hasMany `Place`
- `Place` -> belongsTo `Category`, belongsToMany `Tag`
- `Event` -> belongsToMany `Tag`
- `Tag` -> belongsToMany `Place`, belongsToMany `Event`
- `Banner` standalone hero/slider entity
- `User` -> includes `is_admin` boolean cast

Slug route keys:

- `Place`: route key is `slug`
- `Event`: route key is `slug`

## 4) Validation (Form Requests)

Admin store/update requests enforce:

- unique slug checks
- FK existence checks (`category_id`, tag ids)
- URL validation (`website`, `instagram`, `booking_url`, `button_url`)
- numeric ranges (`rating` between 0 and 5)
- array validation for JSON-backed fields (`gallery`, `working_hours`)
- event date/time constraints

## 5) API Resources

- `CategoryResource`
- `PlaceListResource`
- `PlaceDetailResource`
- `EventListResource`
- `EventDetailResource`
- `BannerResource`
- `TagResource`

All controllers return:

```json
{
  "success": true,
  "message": "optional",
  "data": {}
}
```

Paginated endpoints include `meta` and `links`.

## 6) Controllers

Public controllers:

- `HomeController`
- `CategoryController`
- `PlaceController`
- `EventController`
- `TagController`
- `SearchController`
- `FilterController`

Admin controllers:

- `AuthController`
- `CategoryController`
- `PlaceController`
- `EventController`
- `BannerController`
- `TagController`

## 7) Routes

Base prefix for public reads: `/api` (e.g. `GET /api/home`).

Public:

- `GET /home`
- `GET /categories`
- `GET /categories/{slug}` (slug must start with a letter; numeric segments are reserved for legacy authenticated routes)
- `GET /places`
- `GET /places/featured`
- `GET /places/{slug}`
- `GET /events`
- `GET /events/featured`
- `GET /events/{slug}`
- `GET /tags`
- `GET /search?q=...&type=all|places|events`
- `GET /filters`

Auth (legacy):

- `POST /login`
- `POST /logout` (Sanctum)
- `GET /user` (Sanctum)

Management CRUD (Sanctum, under `/api/manage`):

- `/manage/places`
- `/manage/banners`
- `/manage/tags`

Legacy authenticated CRUD still uses `/api/categories`, `/api/events`, etc. (see `routes/api.php`).

## 8) Seeders

- `CategorySeeder`
- `TagSeeder`
- `BannerSeeder`
- `PlaceSeeder`
- `EventSeeder`
- `DatabaseSeeder` creates admin user and runs all seeders

Default admin user:

- Email: `admin@riyadhamaken.com`
- Password: `password`

## 9) Sanctum auth setup

- `laravel/sanctum` installed in dependencies
- Login issues token with `createToken`
- Protected routes use `auth:sanctum`
- Management routes under `/api` use `auth:sanctum` only (no separate admin middleware)

## 10) Final testing instructions

Run setup:

```bash
php artisan migrate
php artisan db:seed
php artisan serve
```

Quick checks:

```bash
curl http://127.0.0.1:8000/api/home
curl "http://127.0.0.1:8000/api/places?category=restaurants&featured=1&per_page=10"
curl "http://127.0.0.1:8000/api/events?from_date=2026-01-01&to_date=2026-12-31"
curl "http://127.0.0.1:8000/api/search?q=riyadh&type=all"
```

Login (Sanctum):

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@riyadhamaken.com","password":"password"}'
```

Use the returned `Bearer` token for `/api/manage/*` and other authenticated `/api/*` routes.

---

## Example API responses

`GET /api/home`

```json
{
  "success": true,
  "data": {
    "banners": [],
    "featured_categories": [],
    "featured_places": [],
    "featured_events": [],
    "popular_tags": []
  }
}
```

`GET /api/places`

```json
{
  "success": true,
  "data": [],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 0
  },
  "links": {
    "first": null,
    "last": null,
    "prev": null,
    "next": null
  }
}
```

`POST /api/login`

```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "token": "1|example",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@riyadhamaken.com",
      "is_admin": true
    }
  }
}
```
