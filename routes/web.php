<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FarmerController as AdminFarmerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\FarmerRegistrationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\FarmerPageController;
use App\Http\Controllers\Farmer\DashboardController as FarmerDashboardController;
use App\Http\Controllers\Farmer\FarmProfileController;
use App\Http\Controllers\Farmer\OrderController as FarmerOrderController;
use App\Http\Controllers\Farmer\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');

// Phase 3 — marketplace
Route::get('/shop', ShopController::class)->name('shop.index');
Route::get('/products/{product:slug}', ProductPageController::class)->name('shop.show');
Route::get('/farmers', [FarmerPageController::class, 'index'])->name('farmers.index');
Route::middleware('guest')->group(function () {
    Route::get('/farmers/join', [FarmerRegistrationController::class, 'create'])
        ->name('farmer.register');
    Route::post('/farmers/join', [FarmerRegistrationController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('farmer.register.store');
});
Route::get('/farmers/{farmerProfile:slug}', [FarmerPageController::class, 'show'])->name('farmers.show');

// Phase 4 — cart (guests + auth)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

// Phase 4 — checkout (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:5,1');
    Route::get('/checkout/confirmed', [CheckoutController::class, 'confirmed'])->name('checkout.confirmed');
});

/*
|--------------------------------------------------------------------------
| Signed-in areas
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardRedirectController::class)->name('dashboard');

    // Notifications (any auth role)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Breeze profile management (all roles)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customer
    Route::middleware('role:customer')->prefix('account')->name('account.')->group(function () {
        Route::get('/', AccountController::class)->name('index');
        Route::get('/orders', [AccountOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order:order_number}', [AccountOrderController::class, 'show'])->name('orders.show');
    });

    // Farmer
    Route::middleware('role:farmer')->prefix('farmer')->name('farmer.')->group(function () {
        Route::get('/', FarmerDashboardController::class)->name('dashboard');
        Route::get('/profile', [FarmProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [FarmProfileController::class, 'update'])->name('profile.update');
        Route::resource('products', ProductController::class)->except(['show']);
        Route::get('/orders', [FarmerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order:order_number}', [FarmerOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order:order_number}/status', [FarmerOrderController::class, 'updateStatus'])->name('orders.status');
    });

    // Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::resource('categories', CategoryController::class)->except(['show']);

        // User management
        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update']);
        Route::post('users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
        Route::post('users/{user}/reinstate', [AdminUserController::class, 'reinstate'])->name('users.reinstate');

        // Farmer management
        Route::get('farmers', [AdminFarmerController::class, 'index'])->name('farmers.index');
        Route::post('farmers/{farmerProfile}/verify', [AdminFarmerController::class, 'verify'])->name('farmers.verify');
        Route::post('farmers/{farmerProfile}/unverify', [AdminFarmerController::class, 'unverify'])->name('farmers.unverify');

        // Product moderation
        Route::get('products', [AdminProductController::class, 'index'])->name('products.index');
        Route::post('products/{product}/remove', [AdminProductController::class, 'remove'])->name('products.remove');
        Route::post('products/{product}/restore', [AdminProductController::class, 'restore'])->name('products.restore');

        // All orders
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    });
});

require __DIR__.'/auth.php';
