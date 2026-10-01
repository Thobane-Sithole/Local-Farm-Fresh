# Phase 2 file map

## New files

### Services
- `app/Services/CloudinaryService.php` — upload / delete images via `cloudinary-labs/cloudinary-laravel`
- `app/Services/ProductService.php` — create / update / delete products, owns Cloudinary image lifecycle

### Controllers
- `app/Http/Controllers/Farmer/FarmProfileController.php` — edit / update own farm profile
- `app/Http/Controllers/Farmer/ProductController.php` — full CRUD for farmer's own products
- `app/Http/Controllers/Admin/CategoryController.php` — full CRUD for categories

### Form Requests
- `app/Http/Requests/Farmer/UpdateFarmProfileRequest.php`
- `app/Http/Requests/Farmer/StoreProductRequest.php`
- `app/Http/Requests/Farmer/UpdateProductRequest.php`
- `app/Http/Requests/Admin/StoreCategoryRequest.php`
- `app/Http/Requests/Admin/UpdateCategoryRequest.php`

### Views — Farmer
- `resources/views/farmer/profile/edit.blade.php`
- `resources/views/farmer/products/index.blade.php`
- `resources/views/farmer/products/create.blade.php`
- `resources/views/farmer/products/edit.blade.php`
- `resources/views/farmer/products/_form.blade.php` — shared create/edit fields partial

### Views — Admin
- `resources/views/admin/categories/index.blade.php`
- `resources/views/admin/categories/create.blade.php`
- `resources/views/admin/categories/edit.blade.php`

### Tests
- `tests/Feature/Farmer/FarmProfileTest.php`
- `tests/Feature/Farmer/ProductManagementTest.php`
- `tests/Feature/Admin/CategoryManagementTest.php`

## Modified files
- `routes/web.php` — added farmer profile, farmer products resource, and admin categories resource routes

## Dependencies to add (composer)
```
composer require cloudinary-labs/cloudinary-laravel
```

Set `CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME` in `.env`.

## Routes added

| Method | URI | Name |
|--------|-----|------|
| GET | /farmer/profile | farmer.profile.edit |
| PUT | /farmer/profile | farmer.profile.update |
| GET | /farmer/products | farmer.products.index |
| GET | /farmer/products/create | farmer.products.create |
| POST | /farmer/products | farmer.products.store |
| GET | /farmer/products/{product}/edit | farmer.products.edit |
| PUT | /farmer/products/{product} | farmer.products.update |
| DELETE | /farmer/products/{product} | farmer.products.destroy |
| GET | /admin/categories | admin.categories.index |
| GET | /admin/categories/create | admin.categories.create |
| POST | /admin/categories | admin.categories.store |
| GET | /admin/categories/{category}/edit | admin.categories.edit |
| PUT | /admin/categories/{category} | admin.categories.update |
| DELETE | /admin/categories/{category} | admin.categories.destroy |
