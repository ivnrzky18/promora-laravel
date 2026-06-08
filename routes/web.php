<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\ConsumerAuthController;
use App\Http\Controllers\Auth\SellerAuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConsumerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\PublicSellerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
Route::get('/hot-deals', [PromoController::class, 'hotDeals'])->name('hot-deals');
Route::get('/api/calendar-events', [CalendarController::class, 'events'])->name('calendar.events');
Route::get('/explore', [SearchController::class, 'index'])->name('explore');
Route::get('/sellers/{sellerProfile}', [PublicSellerController::class, 'show'])->name('sellers.show');
Route::get('/promos/{promo}', [PromoController::class, 'show'])->name('promos.show');
Route::post('/reviews', [ReviewController::class, 'store'])->middleware('consumer')->name('reviews.store');

// ─── Consumer Auth ────────────────────────────────────────────────────────────
Route::prefix('consumer')->name('consumer.')->group(function () {
    Route::get('/register', [ConsumerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [ConsumerAuthController::class, 'register']);
    Route::get('/login', [ConsumerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ConsumerAuthController::class, 'login']);
    Route::post('/logout', [ConsumerAuthController::class, 'logout'])->name('logout')->middleware('consumer');
});

// Consumer protected routes
Route::prefix('consumer')->name('consumer.')->middleware('consumer')->group(function () {
    Route::get('/dashboard', [ConsumerController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookmarks', [ConsumerController::class, 'bookmarks'])->name('bookmarks');
    Route::get('/profile', [ConsumerController::class, 'profile'])->name('profile');
    Route::put('/profile', [ConsumerController::class, 'updateProfile'])->name('profile.update');
    Route::post('/bookmarks/{promo}/toggle', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/subscriptions/{seller}/toggle', [SubscriptionController::class, 'toggle'])->name('subscriptions.toggle');
});

// ─── Seller Auth ──────────────────────────────────────────────────────────────
Route::prefix('seller')->name('seller.')->group(function () {
    Route::get('/register', [SellerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [SellerAuthController::class, 'register']);
    Route::get('/login', [SellerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [SellerAuthController::class, 'login']);
    Route::post('/logout', [SellerAuthController::class, 'logout'])->name('logout')->middleware('seller');
});

// Seller protected routes
Route::prefix('seller')->name('seller.')->middleware('seller')->group(function () {
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
    Route::resource('promos', PromoController::class);
    Route::resource('events', EventController::class);
    Route::get('/profile', [SellerController::class, 'profile'])->name('profile');
    Route::put('/profile', [SellerController::class, 'updateProfile'])->name('profile.update');
});

// ─── Admin Auth ───────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout')->middleware('admin');
});

// Admin protected routes
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/sellers/{seller}/verify', [AdminController::class, 'verifySeller'])->name('sellers.verify');
    Route::delete('/sellers/{seller}', [AdminController::class, 'rejectSeller'])->name('sellers.reject');
    Route::get('/promos', [AdminController::class, 'promos'])->name('promos.index');
    Route::post('/promos/{promo}/approve', [AdminController::class, 'approvePromo'])->name('promos.approve');
    Route::delete('/promos/{promo}', [AdminController::class, 'rejectPromo'])->name('promos.reject');
    Route::resource('categories', CategoryController::class);
    Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
});
