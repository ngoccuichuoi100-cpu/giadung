<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\SalesScheduleController as AdminSalesScheduleController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PayOSController;
use App\Http\Controllers\SearchHistoryController;
use App\Http\Controllers\AiChatController;
use Illuminate\Support\Facades\Route;

// --- GUEST / CUSTOMER ACCESSIBLE ROUTES ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home-preview', [HomeController::class, 'preview'])->name('home.preview');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{id}/variant-info', [ProductController::class, 'getVariantInfo'])->name('products.variant-info');
Route::get('/products/{id}/quick-view', [ProductController::class, 'getQuickView'])->name('products.quick-view');

// Live Search & Filter APIs
Route::get('/api/products/live-search', [ProductController::class, 'liveSearch'])->name('api.products.live-search');

// Search History APIs
Route::get('/api/search-history', [SearchHistoryController::class, 'index'])->name('api.search-history.index');
Route::post('/api/search-history', [SearchHistoryController::class, 'store'])->name('api.search-history.store');
Route::delete('/api/search-history/{id}', [SearchHistoryController::class, 'destroy'])->name('api.search-history.destroy');
Route::delete('/api/search-history', [SearchHistoryController::class, 'clear'])->name('api.search-history.clear');

// AI Smart Chatbot APIs
Route::post('/api/chat-ai/send', [AiChatController::class, 'sendMessage'])->name('chat-ai.send');
Route::get('/api/chat-ai/suggestions', [AiChatController::class, 'getSuggestions'])->name('chat-ai.suggestions');

// About Us & News
Route::get('/about-us', [HomeController::class, 'about'])->name('about');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// Theme toggle (for guest and user)
Route::post('/settings/toggle-theme', [SettingController::class, 'toggleTheme'])->name('settings.toggle-theme');
Route::post('/settings/report', [SettingController::class, 'fileReport'])->name('settings.report');

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/profile', [AuthController::class, 'updateProfile'])->name('register.profile');
Route::get('/register/welcome', [AuthController::class, 'showWelcome'])->name('register.welcome');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('login.google.callback');

// Cart & Checkout (Guest accessible, checking inside controllers)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place-order');

// PayOS Payment Routes
Route::get('/payment/payos/{orderId}', [PayOSController::class, 'createPayment'])->name('payos.checkout');
Route::get('/payment/payos-return', [PayOSController::class, 'handleReturn'])->name('payos.return');
Route::get('/payment/payos-cancel', [PayOSController::class, 'handleCancel'])->name('payos.cancel');
Route::post('/payos/webhook', [PayOSController::class, 'webhook'])->name('payos.webhook');
Route::post('/api/payos/webhook', [PayOSController::class, 'webhook']);
Route::get('/api/orders/{id}/payos-status', [PayOSController::class, 'checkStatus'])->name('orders.payos-status');



// --- PROTECTED CUSTOMER ROUTES (Requires active & logged in user) ---
Route::middleware(['auth', 'active'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    // Settings
    Route::post('/settings/preferences', [SettingController::class, 'updatePreferences'])->name('settings.preferences');

    // Orders history
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/resend-receipt', [OrderController::class, 'resendReceipt'])->name('orders.resend-receipt');

    // Location APIs for Cascading Selects
    Route::get('/api/locations/provinces', [LocationController::class, 'provinces']);
    Route::get('/api/locations/provinces/{province_code}/districts', [LocationController::class, 'districts']);
    Route::get('/api/locations/districts/{district_code}/wards', [LocationController::class, 'wards']);
    Route::get('/api/locations/delivery-estimate/{province_code}', [LocationController::class, 'deliveryEstimate']);

    // Coupon Apply API
    Route::post('/api/coupons/apply', [CouponController::class, 'apply']);

    // Wishlist
    Route::post('/products/{id}/wishlist', [ProductController::class, 'toggleWishlist'])->name('products.wishlist');

    // Reviews & votes
    Route::post('/products/{id}/reviews', [ProductController::class, 'storeReview'])->name('products.reviews.store');
    Route::post('/products/reviews/{id}/vote', [ProductController::class, 'voteReview'])->name('products.reviews.vote');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Games
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::post('/games/checkin', [GameController::class, 'checkin'])->name('games.checkin');
    Route::post('/games/spin', [GameController::class, 'spin'])->name('games.spin');
    Route::post('/games/even-odd', [GameController::class, 'playEvenOdd'])->name('games.even-odd');
    Route::post('/games/redeem', [GameController::class, 'redeemReward'])->name('games.redeem');

    // Friends
    Route::get('/friends', [FriendshipController::class, 'index'])->name('friends.index');
    Route::post('/friends/send', [FriendshipController::class, 'sendRequest'])->name('friends.send');
    Route::post('/friends/{id}/accept', [FriendshipController::class, 'acceptRequest'])->name('friends.accept');
    Route::post('/friends/{id}/decline', [FriendshipController::class, 'declineRequest'])->name('friends.decline');
    Route::post('/friends/{id}/remove', [FriendshipController::class, 'removeFriend'])->name('friends.remove');
    Route::post('/friends/block', [FriendshipController::class, 'blockUser'])->name('friends.block');
    Route::post('/friends/{id}/unblock', [FriendshipController::class, 'unblockUser'])->name('friends.unblock');
});


// --- PROTECTED ADMIN ROUTES (Requires active, logged in admin) ---
Route::middleware(['auth', 'active', 'admin'])->prefix(config('app.admin_path', 'admin'))->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Categories Management (Resource routes)
    Route::resource('categories', AdminCategoryController::class);

    // Products Management (Resource routes)
    Route::resource('products', AdminProductController::class);
    Route::delete('products/images/{id}', [AdminProductController::class, 'deleteGalleryImage'])->name('products.images.delete');

    // Orders Management
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{id}/send-receipt', [AdminOrderController::class, 'sendReceipt'])->name('orders.send-receipt');

    // Customers Management
    Route::get('customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{id}', [AdminCustomerController::class, 'show'])->name('customers.show');
    Route::patch('customers/{id}/toggle', [AdminCustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    Route::delete('customers/{id}', [AdminCustomerController::class, 'destroy'])->name('customers.destroy');

    // Vouchers / Promotions Management
    Route::get('vouchers', [AdminVoucherController::class, 'index'])->name('vouchers.index');
    Route::get('vouchers/create', [AdminVoucherController::class, 'create'])->name('vouchers.create');
    Route::post('vouchers', [AdminVoucherController::class, 'store'])->name('vouchers.store');
    Route::get('vouchers/{id}/edit', [AdminVoucherController::class, 'edit'])->name('vouchers.edit');
    Route::put('vouchers/{id}', [AdminVoucherController::class, 'update'])->name('vouchers.update');
    Route::delete('vouchers/{id}', [AdminVoucherController::class, 'destroy'])->name('vouchers.destroy');
    Route::patch('vouchers/{id}/toggle', [AdminVoucherController::class, 'toggleStatus'])->name('vouchers.toggle-status');
    Route::post('vouchers/{id}/give', [AdminVoucherController::class, 'giveToCustomers'])->name('vouchers.give');

    // News Management (Admin)
    Route::get('news', [AdminNewsController::class, 'index'])->name('news.index');

    // Employee Management (Admin only)
    Route::resource('employees', AdminEmployeeController::class);

    // Sales Days Assignment Schedules
    Route::get('sales-schedules', [AdminSalesScheduleController::class, 'index'])->name('sales-schedules.index');
    Route::post('sales-schedules', [AdminSalesScheduleController::class, 'store'])->name('sales-schedules.store');
    Route::put('sales-schedules/{id}', [AdminSalesScheduleController::class, 'update'])->name('sales-schedules.update');
    Route::delete('sales-schedules/{id}', [AdminSalesScheduleController::class, 'destroy'])->name('sales-schedules.destroy');
});
