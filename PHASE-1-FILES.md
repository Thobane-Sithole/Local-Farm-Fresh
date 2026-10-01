# Phase 1 file map

New files
- app/Enums: UserRole, AccountStatus, OrderStatus, ProductUnit, PaymentMethod, Province
- app/Models: FarmerProfile, Category, Product, ProductImage, Address, Cart, CartItem, Order, OrderItem, OrderStatusEvent, Concerns/HasUniqueSlug
- app/Http/Middleware: EnsureUserHasRole, EnsureAccountIsActive
- app/Http/Controllers: HomeController, DashboardRedirectController, Auth/FarmerRegistrationController, Farmer/DashboardController, Admin/DashboardController, Account/AccountController
- app/Http/Requests/Auth/FarmerRegistrationRequest
- app/Policies: ProductPolicy, OrderPolicy
- app/Services/OrderNumberGenerator
- app/Console/Commands/CreateAdmin (php artisan lff:create-admin)
- database/migrations: 9 migrations (users fields, categories, farmer_profiles, products, product_images, addresses, carts, orders + items + status events + number sequences, notifications)
- database/factories: FarmerProfile, Category, Product, Order, Address
- database/seeders: CategorySeeder, MarketplaceSeeder
- resources/views: brand logo, ui components, site + dashboard layouts, farmer registration, dashboards, home, errors
- public/favicon.svg
- tests: farmer registration, role access, policies, order numbers, order status, seeder

Replaces Breeze / Laravel defaults
- app/Models/User.php, app/Providers/AppServiceProvider.php, bootstrap/app.php
- app/Http/Controllers/Auth/RegisteredUserController.php (adds phone)
- routes/web.php
- database/factories/UserFactory.php, database/seeders/DatabaseSeeder.php
- resources/views/auth/login.blade.php, auth/register.blade.php
- resources/views/layouts/guest.blade.php, layouts/app.blade.php
- resources/views/components: application-logo, primary/secondary/danger-button, text-input, input-label, input-error
- resources/css/app.css, tailwind.config.js, .env.example, tests/Feature/ExampleTest.php
