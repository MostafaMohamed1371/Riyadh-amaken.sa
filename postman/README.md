# Postman Collection - Riyadh Amaken API

## Import

1. Open Postman.
2. Click **Import**.
3. Import `Riyadh-Amaken-v1.postman_collection.json`.

## Variables

| Variable | Example | Purpose |
|----------|---------|---------|
| `base_url` | `http://127.0.0.1:8000/api` | Public read API (home, places, events, …) |
| `legacy_base_url` | `http://127.0.0.1:8000/api` | Auth + management CRUD (same host as `base_url`) |
| `auth_token` | *(from login)* | Bearer token for protected routes |
| `category_slug` | `restaurants` | Public category by slug |
| `place_slug` | `kingdom-centre` | Public place by slug |
| `event_slug` | `riyadh-festival` | Public event by slug |
| `resource_id` | `1` | Numeric id for GET/PUT/DELETE on management resources |

**Auth flow:** `POST {{legacy_base_url}}/login` → copy `data.token` → set `auth_token`.

**Protected routes** require:

- `Authorization: Bearer {{auth_token}}` (any authenticated user)

---

## Public API (`{{base_url}}` … `/api`)

| Method | Path | Description |
|--------|------|-------------|
| GET | `/home` | Home payload (sliders, categories, gallery) |
| GET | `/sliders` | Sliders list (`?per_page=`) — **no auth** |
| GET | `/sliders/{id}` | Single slider — **no auth** |
| GET | `/gallery` | Gallery list (`?per_page=`) — **no auth** |
| GET | `/gallery/{id}` | Single gallery item — **no auth** |
| GET | `/activities` | Activities list (`?per_page=`) — **no auth** |
| GET | `/activities/{id}` | Single activity — **no auth** |
| GET | `/categories` | Categories list |
| GET | `/categories/{slug}` | Category by slug (slug must start with a letter) |
| GET | `/places` | Places list (filters query params) |
| GET | `/places/featured` | Featured places |
| GET | `/places/{slug}` | Place by slug |
| GET | `/events` | Events list |
| GET | `/events/featured` | Featured events |
| GET | `/events/{slug}` | Event by slug |
| GET | `/tags` | Tags list |
| GET | `/search?q=` | Search |
| GET | `/filters` | Filter helpers |
| GET | `/schedule/suggestions` | Trip ideas: events + places for visit dates (optional `interests[]` = tag slugs) |
| GET | `/schedule` | Public: `data.events` only (saved **events**; each `event` includes `category` when `category_id` is set) |

### Schedule (authenticated)

| Method | Path | Notes |
|--------|------|--------|
| POST | `/schedule` | Body: `{ "type": "event"\|"place", "id": <numeric id> }` |
| PUT | `/schedule/reorder` | Body: `{ "order": [<schedule item ids in desired order>] }` (must list every id) |
| DELETE | `/schedule/{id}` | Remove one saved item |

---

## Auth (`{{legacy_base_url}}` … `/api`)

| Method | Path | Body |
|--------|------|------|
| POST | `/login` | `{ "email": "...", "password": "..." }` |
| POST | `/logout` | *(Bearer)* |
| GET | `/user` | *(Bearer)* |

---

## Management API (`{{legacy_base_url}}/manage` … `/api/manage`)

All require **Bearer** (Sanctum authenticated user).

### Places — `POST/GET/PUT/DELETE /api/manage/places`

| Method | Path | Notes |
|--------|------|--------|
| GET | `/manage/places` | List (paginated, `?per_page=15`) |
| POST | `/manage/places` | Create (JSON body) |
| GET | `/manage/places/{id}` | Show by **id** |
| PUT | `/manage/places/{id}` | Full update |
| DELETE | `/manage/places/{id}` | Delete |

**POST /api/manage/places example:**

```json
{
  "category_id": 1,
  "name": "Kingdom Centre Tower",
  "slug": "kingdom-centre-tower",
  "short_description": "Iconic landmark in Riyadh",
  "full_description": "Optional long text",
  "city": "Riyadh",
  "phone": "+966...",
  "website": "https://example.com",
  "instagram": "https://instagram.com/...",
  "image": "https://example.com/cover.jpg",
  "gallery": ["https://example.com/a.jpg"],
  "rating": 4.5,
  "price_range": "$$",
  "working_hours": { "sun-thu": "10:00-22:00" },
  "is_featured": true,
  "is_active": true,
  "tag_ids": [1, 2]
}
```

---

### Banners — `POST/GET/PUT/DELETE /api/manage/banners`

| Method | Path | Notes |
|--------|------|--------|
| GET | `/manage/banners` | List |
| POST | `/manage/banners` | Create |
| GET | `/manage/banners/{id}` | Show |
| PUT | `/manage/banners/{id}` | Update |
| DELETE | `/manage/banners/{id}` | Delete |

**POST /api/manage/banners example:**

```json
{
  "title": "Discover Riyadh",
  "subtitle": "Your city guide",
  "image": "https://example.com/banner.jpg",
  "button_text": "Explore",
  "button_url": "https://example.com",
  "is_active": true,
  "sort_order": 0
}
```

---

### Tags — `POST/GET/PUT/DELETE /api/manage/tags`

| Method | Path | Notes |
|--------|------|--------|
| GET | `/manage/tags` | List |
| POST | `/manage/tags` | Create |
| GET | `/manage/tags/{id}` | Show |
| PUT | `/manage/tags/{id}` | Update |
| DELETE | `/manage/tags/{id}` | Delete |

**POST /api/manage/tags example:**

```json
{
  "name": "Family",
  "slug": "family"
}
```

---

## Notes

- **Legacy** routes still exist under `/api` (e.g. `categories`, `events`, `activities`, …) for the old app. Public `GET /api/categories` and `GET /api/events` serve the new read API; authenticated legacy CRUD uses `POST/PUT/DELETE /api/categories/{id}` and `/api/events/{id}` (numeric id). Slug-based public detail URLs require a slug that starts with a letter so numeric ids remain available for legacy.
- **Management** routes for places, banners, and tags live under **`/api/manage/...`** so they do not collide with public `GET /api/places` and `GET /api/tags`.
- Log in with `POST /login` and use the returned token for management routes.
