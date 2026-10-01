# Local-Farm-Fresh

**Fresh. Local. Direct.** A marketplace where small-scale South African farmers
sell produce directly to their community. Payment is cash on delivery.

Laravel 12 monolith: Blade, Tailwind CSS, Alpine.js, PostgreSQL, Cloudinary, deployed on Render.

---

## Build status

| Phase | Scope | Status |
|------:|-------|--------|
| 1 | Setup, authentication, database, roles | **Done** |
| 2 | Farmer profiles, categories, product management, Cloudinary | **Done** |
| 3 | Public marketplace, search, filters, product pages, SEO | |
| 4 | Cart, checkout, orders (OrderService), sample orders | |
| 5 | Notifications (in-app + email), farmer order management | |
| 6 | Admin dashboard tools | |
| 7 | UI refinement, accessibility, performance | |
| 8 | Testing, security hardening, production prep | |
| 9 | Render deployment | |

---

## Local setup

Requires PHP 8.3+, Composer, Node 20+, PostgreSQL 15+.

```bash
# 1. Laravel + Breeze (Blade stack, PHPUnit)
composer create-project laravel/laravel local-farm-fresh "^12.0"
cd local-farm-fresh
composer require laravel/breeze --dev
php artisan breeze:install blade      # choose PHPUnit when asked; no dark mode

# 2. Copy this repository's files over the fresh install (overwrite when asked)

# 3. Environment
cp .env.example .env
php artisan key:generate
createdb local_farm_fresh             # or create it in pgAdmin

# 4. Cloudinary package (Phase 2+)
composer require cloudinary-labs/cloudinary-laravel
# Add CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME to .env

# 5. Database, assets, run
php artisan migrate:fresh --seed
npm install && npm run build
php artisan serve
```

### Seeded accounts (development only, password `password`)

| Role | Email |
|------|-------|
| Admin | admin@localfarmfresh.test |
| Farmer | thandeka@farm.test (and 9 other farmers, see `MarketplaceSeeder`) |
| Customer | customer@localfarmfresh.test |

Seeding creates 10 categories, 10 farmers across all regions, 36 products and
20 customers with delivery addresses. In production only categories are seeded;
create the first admin with `php artisan lff:create-admin`.

### Tests

```bash
php artisan test
```

---

## Architecture decisions

**One order per farmer.** A cart holding produce from three farmers becomes three
orders sharing a `checkout_group`. Each farmer confirms, packs, delivers and
collects cash for their own goods, so each needs an independent order and status.

**Order snapshots.** Orders copy the delivery address, and order items copy the
product name, unit and price, so history stays correct when products or
addresses change later.

**Order numbers** (`LFF-20260930-0001`) come from a per-day counter updated with one
atomic `INSERT ... ON CONFLICT ... RETURNING`, so concurrent checkouts can't collide.

**No database IDs in URLs.** Products and farms use slugs
(`/products/fresh-tomatoes`); orders use their order number.

**Roles** live in a `role` column (`customer`, `farmer`, `admin`) backed by the
`UserRole` enum. `role` and `status` are not mass-assignable, so they can never be
set from form input. Route groups use the `role:` middleware; record-level access
uses policies; admins pass every policy via `Gate::before`.

**Suspension** takes effect on the user's next request: `EnsureAccountIsActive`
signs them out, and suspended farmers' products disappear from `Product::visible()`.

**Two kinds of "unavailable".** `is_available` is the farmer's own switch;
`removed_at` is admin moderation, which a farmer cannot undo.

**Future-ready without building it now.** `PaymentMethod` is an enum with one case,
farm profiles have nullable lat/lng for later location features, and business logic
goes into service classes so a REST API or mobile app can reuse it.

### Design tokens

| Token | Hex | Use |
|-------|-----|-----|
| `brand-600` | #23823F | Buttons and green text (4.84:1 with white, WCAG AA) |
| `brand-500` | #2E9B50 | Accents, icons, focus rings, large type |
| `brand-300` | #43B85F | Decorative only |
| `brand-800` / `brand-900` | #174C32 / #0F3D27 | Dark panels, sidebar, footer |
| `brand-100` / `brand-50` | #E6F5E8 / #F3FAF4 | Tints |
| `ink` / `muted` / `line` / `canvas` | #18352A / #68766D / #DCE7DF / #F8FAF7 | Text, borders, background |

White on the brief's `#2E9B50` is 3.55:1, below AA for button-size text, which is
why action colour is one step deeper.

---

## Render (full guide in Phase 9)

Planned configuration:

- **Build:** `composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache`
- **Pre-deploy:** `php artisan migrate --force && php artisan db:seed --class=CategorySeeder --force`
- **Start:** served by a PHP runtime on Render (detailed in Phase 9)
- **Env:** `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, plus the DB, mail and `CLOUDINARY_URL` values from `.env.example`
- Images live in Cloudinary, never on Render's disk.
