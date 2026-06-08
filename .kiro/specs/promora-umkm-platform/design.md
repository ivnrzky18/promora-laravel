# Design Document - Promora UMKM Platform

## Overview

Promora adalah platform web full-stack berbasis Laravel 11 yang berfungsi sebagai pusat informasi terpusat bagi UMKM Indonesia. Platform ini menghubungkan Consumer dengan Seller lokal melalui fitur pencarian, kalender promo, notifikasi in-app, dan sistem ulasan.

**Tech Stack:**
- Backend: Laravel 11 (PHP 8.2+)
- Frontend: Laravel Blade + Tailwind CSS + Alpine.js
- Database: MySQL 8.0 (via Laragon, phpMyAdmin di localhost/phpmyadmin)
- Auth: Laravel Breeze dengan single `users` table + role-based middleware
- Notifications: Laravel Notifications (in-app, database driver)
- File Storage: Laravel Storage dengan public disk + `storage:link`
- Calendar: FullCalendar.js via CDN
- Scheduler: Laravel Task Scheduler (UpdateHotDeals, ExpirePromos)
- Testing: PHPUnit + PestPHP + property-based testing (eris/eris)

**Environment Configuration (`.env`):**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=promora
DB_USERNAME=root
DB_PASSWORD=
```

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        Browser (Client)                          │
│  Blade Templates + Tailwind CSS + Alpine.js + FullCalendar.js   │
└──────────────────────────┬──────────────────────────────────────┘
                           │ HTTP / AJAX (JSON)
┌──────────────────────────▼──────────────────────────────────────┐
│                     Laravel 11 Application                       │
│                                                                  │
│  ┌─────────────┐  ┌──────────────┐  ┌────────────────────────┐  │
│  │  Web Routes │  │  API Routes  │  │  Console (Scheduler)   │  │
│  │  (Blade)    │  │  (JSON)      │  │  UpdateHotDeals        │  │
│  └──────┬──────┘  └──────┬───────┘  │  ExpirePromos          │  │
│         │                │          └────────────────────────┘  │
│  ┌──────▼────────────────▼──────────────────────────────────┐   │
│  │                    Controllers                            │   │
│  │  Auth | Consumer | Seller | Admin | Promo | Event        │   │
│  │  Category | Notification | Search | Bookmark | Review    │   │
│  └──────────────────────────┬────────────────────────────── ┘   │
│                             │                                    │
│  ┌──────────────────────────▼────────────────────────────────┐  │
│  │                    Eloquent Models                         │  │
│  │  User | SellerProfile | Category | Promo | Event          │  │
│  │  Subscription | Bookmark | Review | Notification          │  │
│  └──────────────────────────┬────────────────────────────────┘  │
│                             │                                    │
│  ┌──────────────────────────▼────────────────────────────────┐  │
│  │              Laravel Storage (public disk)                 │  │
│  │              Laravel Notifications (database)              │  │
│  └───────────────────────────────────────────────────────────┘  │
└──────────────────────────┬──────────────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────────────┐
│                      MySQL 8.0 Database                          │
└─────────────────────────────────────────────────────────────────┘
```

### Authentication Architecture

Platform menggunakan **single `users` table** dengan kolom `role` (enum: `consumer`, `seller`, `admin`) dan **role-based middleware** — bukan multiple guards dengan multiple tables. Pendekatan ini lebih sederhana dan sesuai dengan Laravel Breeze standar.

```
config/auth.php
  guards:
    web → session driver → users provider (App\Models\User)

Middleware:
  EnsureConsumer  → checks auth()->check() && auth()->user()->role === 'consumer'
  EnsureSeller    → checks auth()->check() && auth()->user()->role === 'seller'
  EnsureAdmin     → checks auth()->check() && auth()->user()->role === 'admin'
```

**Rationale:** Multiple guards memerlukan multiple user tables atau provider yang berbeda. Karena semua role menggunakan tabel `users` yang sama, role-based middleware adalah pendekatan yang lebih idiomatis di Laravel untuk kasus ini.

### Request Lifecycle

```
Browser Request
  → public/index.php
  → bootstrap/app.php (middleware stack)
  → routes/web.php atau routes/api.php
  → Middleware (auth, role check)
  → Controller method
  → Service / Model layer
  → Response (Blade view atau JSON)
```

## Components and Interfaces

### Middleware

```php
// app/Http/Middleware/EnsureConsumer.php
class EnsureConsumer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'consumer') {
            return redirect()->route('consumer.login');
        }
        return $next($request);
    }
}

// app/Http/Middleware/EnsureSeller.php
// app/Http/Middleware/EnsureAdmin.php
// — pola yang sama dengan role berbeda
```

Middleware didaftarkan di `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'consumer' => EnsureConsumer::class,
        'seller'   => EnsureSeller::class,
        'admin'    => EnsureAdmin::class,
    ]);
})
```

### Controllers

#### Auth Controllers
```
app/Http/Controllers/Auth/
  ConsumerAuthController.php  — register, login, logout consumer
  SellerAuthController.php    — register, login, logout seller
  AdminAuthController.php     — login, logout admin
```

#### Feature Controllers
```
app/Http/Controllers/
  ConsumerController.php      — dashboard, profile, saved promos
  SellerController.php        — dashboard, profile management
  PromoController.php         — CRUD promo (ResourceController)
  EventController.php         — CRUD event (ResourceController)
  CategoryController.php      — CRUD category (ResourceController, admin only)
  NotificationController.php  — list, mark as read
  AdminController.php         — verification, moderation, stats
  SearchController.php        — explore/search with filters
  BookmarkController.php      — toggle bookmark (AJAX)
  SubscriptionController.php  — toggle subscription (AJAX)
  ReviewController.php        — store review
  PublicSellerController.php  — public seller profile page

app/Http/Controllers/Api/
  PromoController.php         — GET /api/promos, /api/promos/{id}
  SellerController.php        — GET /api/sellers
```

#### Key Controller Method Signatures

```php
// PromoController.php
class PromoController extends Controller
{
    public function index(): View                          // seller's promo list
    public function create(): View                        // create form
    public function store(StorePromoRequest $request): RedirectResponse
    public function show(Promo $promo): View              // public promo detail
    public function edit(Promo $promo): View              // edit form
    public function update(UpdatePromoRequest $request, Promo $promo): RedirectResponse
    public function destroy(Promo $promo): RedirectResponse  // soft delete
}

// BookmarkController.php
class BookmarkController extends Controller
{
    public function toggle(Request $request, Promo $promo): JsonResponse
    // Returns: { bookmarked: bool, count: int }
}

// SubscriptionController.php
class SubscriptionController extends Controller
{
    public function toggle(Request $request, SellerProfile $seller): JsonResponse
    // Returns: { subscribed: bool }
}

// SearchController.php
class SearchController extends Controller
{
    public function index(Request $request): View
    // Query params: q, category_id, location, sort, lat, lng
}
```

### Form Requests

```
app/Http/Requests/
  Auth/
    RegisterConsumerRequest.php
    RegisterSellerRequest.php
    LoginRequest.php
  StorePromoRequest.php
  UpdatePromoRequest.php
  StoreEventRequest.php
  StoreReviewRequest.php
  StoreCategoryRequest.php
```

Semua pesan validasi menggunakan Bahasa Indonesia, didefinisikan di `lang/id/validation.php`.

### Routes Structure

```php
// routes/web.php

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [SearchController::class, 'index'])->name('explore');
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
Route::get('/hot-deals', [PromoController::class, 'hotDeals'])->name('hot-deals');
Route::get('/sellers/{sellerProfile}', [PublicSellerController::class, 'show'])->name('sellers.show');
Route::get('/promos/{promo}', [PromoController::class, 'show'])->name('promos.show');

// Consumer auth
Route::prefix('consumer')->name('consumer.')->group(function () {
    Route::get('/register', [ConsumerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [ConsumerAuthController::class, 'register']);
    Route::get('/login', [ConsumerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ConsumerAuthController::class, 'login']);
    Route::post('/logout', [ConsumerAuthController::class, 'logout'])->name('logout');
});

// Consumer protected routes
Route::prefix('consumer')->name('consumer.')->middleware('consumer')->group(function () {
    Route::get('/dashboard', [ConsumerController::class, 'dashboard'])->name('dashboard');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/bookmarks', [ConsumerController::class, 'bookmarks'])->name('bookmarks');
    Route::get('/profile', [ConsumerController::class, 'profile'])->name('profile');
    Route::put('/profile', [ConsumerController::class, 'updateProfile'])->name('profile.update');
    Route::post('/bookmarks/{promo}/toggle', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::post('/subscriptions/{sellerProfile}/toggle', [SubscriptionController::class, 'toggle'])->name('subscriptions.toggle');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// Seller auth
Route::prefix('seller')->name('seller.')->group(function () {
    Route::get('/register', [SellerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [SellerAuthController::class, 'register']);
    Route::get('/login', [SellerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [SellerAuthController::class, 'login']);
    Route::post('/logout', [SellerAuthController::class, 'logout'])->name('logout');
});

// Seller protected routes
Route::prefix('seller')->name('seller.')->middleware('seller')->group(function () {
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
    Route::resource('promos', PromoController::class);
    Route::resource('events', EventController::class);
    Route::get('/profile', [SellerController::class, 'profile'])->name('profile');
    Route::put('/profile', [SellerController::class, 'updateProfile'])->name('profile.update');
});

// Admin auth
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Admin protected routes
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/sellers/{sellerProfile}/verify', [AdminController::class, 'verifySeller'])->name('sellers.verify');
    Route::delete('/sellers/{sellerProfile}', [AdminController::class, 'rejectSeller'])->name('sellers.reject');
    Route::post('/promos/{promo}/approve', [AdminController::class, 'approvePromo'])->name('promos.approve');
    Route::delete('/promos/{promo}', [AdminController::class, 'rejectPromo'])->name('promos.reject');
    Route::resource('categories', CategoryController::class);
    Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
});

// routes/api.php
Route::prefix('v1')->group(function () {
    Route::get('/promos', [Api\PromoController::class, 'index']);
    Route::get('/promos/{promo}', [Api\PromoController::class, 'show']);
    Route::get('/sellers', [Api\SellerController::class, 'index']);
});
```

### Blade View Structure

```
resources/views/
  layouts/
    app.blade.php           — main layout (nav, footer)
    consumer.blade.php      — consumer layout dengan sidebar
    seller.blade.php        — seller layout dengan sidebar
    admin.blade.php         — admin layout
  components/
    promo-card.blade.php    — reusable promo card component
    seller-card.blade.php   — reusable seller card component
    countdown.blade.php     — countdown timer component
    star-rating.blade.php   — star rating input/display
    notification-bell.blade.php
  auth/
    consumer/
      register.blade.php
      login.blade.php
    seller/
      register.blade.php
      login.blade.php
    admin/
      login.blade.php
  consumer/
    dashboard.blade.php
    bookmarks.blade.php
    notifications.blade.php
    profile.blade.php
  seller/
    dashboard.blade.php
    promos/
      index.blade.php
      create.blade.php
      edit.blade.php
    events/
      index.blade.php
      create.blade.php
      edit.blade.php
    profile.blade.php
  admin/
    dashboard.blade.php
    sellers/
      index.blade.php       — pending verification list
    promos/
      index.blade.php       — pending approval list
    categories/
      index.blade.php
      create.blade.php
      edit.blade.php
    stats.blade.php
  public/
    explore.blade.php       — search & filter page
    calendar.blade.php      — FullCalendar page
    hot-deals.blade.php
    sellers/
      show.blade.php        — public seller profile
    promos/
      show.blade.php        — public promo detail
```

## Data Models

### Database Schema

#### Table: `users`
```sql
CREATE TABLE users (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    email           VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password        VARCHAR(255) NOT NULL,
    role            ENUM('consumer', 'seller', 'admin') NOT NULL DEFAULT 'consumer',
    phone           VARCHAR(20) NULL,
    avatar          VARCHAR(255) NULL,
    location        VARCHAR(255) NULL,          -- kota/kecamatan consumer
    remember_token  VARCHAR(100) NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL
);
```

#### Table: `seller_profiles`
```sql
CREATE TABLE seller_profiles (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             BIGINT UNSIGNED NOT NULL,
    business_name       VARCHAR(255) NOT NULL,
    business_category   VARCHAR(100) NOT NULL,  -- Kuliner, Fashion, Jasa, dll
    description         TEXT NULL,
    address             VARCHAR(500) NOT NULL,
    latitude            DECIMAL(10, 8) NULL,
    longitude           DECIMAL(11, 8) NULL,
    logo                VARCHAR(255) NULL,
    is_verified         BOOLEAN NOT NULL DEFAULT FALSE,
    deleted_at          TIMESTAMP NULL,
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Table: `categories`
```sql
CREATE TABLE categories (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    icon        VARCHAR(255) NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL
);
-- Seed data: Kuliner, Fashion, Jasa, Kesehatan, Pendidikan, Hiburan
```

#### Table: `promos`
```sql
CREATE TABLE promos (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seller_id           BIGINT UNSIGNED NOT NULL,   -- FK ke seller_profiles.id
    category_id         BIGINT UNSIGNED NOT NULL,
    title               VARCHAR(255) NOT NULL,
    description         TEXT NULL,
    poster_image        VARCHAR(255) NULL,
    discount_percentage DECIMAL(5, 2) NULL,
    original_price      DECIMAL(15, 2) NULL,
    promo_price         DECIMAL(15, 2) NULL,
    start_date          DATE NOT NULL,
    end_date            DATE NOT NULL,
    is_hot_deal         BOOLEAN NOT NULL DEFAULT FALSE,
    view_count          INT UNSIGNED NOT NULL DEFAULT 0,
    status              ENUM('draft', 'active', 'expired') NOT NULL DEFAULT 'draft',
    deleted_at          TIMESTAMP NULL,
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL,
    FOREIGN KEY (seller_id) REFERENCES seller_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

#### Table: `events`
```sql
CREATE TABLE events (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seller_id       BIGINT UNSIGNED NOT NULL,
    title           VARCHAR(255) NOT NULL,
    description     TEXT NULL,
    location        VARCHAR(500) NULL,
    event_date      DATETIME NOT NULL,
    end_date        DATETIME NULL,
    poster_image    VARCHAR(255) NULL,
    status          ENUM('draft', 'active', 'cancelled') NOT NULL DEFAULT 'draft',
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    FOREIGN KEY (seller_id) REFERENCES seller_profiles(id) ON DELETE CASCADE
);
```

#### Table: `subscriptions`
```sql
CREATE TABLE subscriptions (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    seller_id   BIGINT UNSIGNED NOT NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL,
    UNIQUE KEY unique_subscription (user_id, seller_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES seller_profiles(id) ON DELETE CASCADE
);
```

#### Table: `bookmarks`
```sql
CREATE TABLE bookmarks (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    promo_id    BIGINT UNSIGNED NOT NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL,
    UNIQUE KEY unique_bookmark (user_id, promo_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (promo_id) REFERENCES promos(id) ON DELETE CASCADE
);
```

#### Table: `reviews`
```sql
CREATE TABLE reviews (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    seller_id   BIGINT UNSIGNED NOT NULL,
    promo_id    BIGINT UNSIGNED NULL,
    rating      TINYINT UNSIGNED NOT NULL,   -- 1–5
    comment     TEXT NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL,
    UNIQUE KEY unique_review (user_id, seller_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES seller_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (promo_id) REFERENCES promos(id) ON DELETE SET NULL
);
```

#### Table: `notifications`
```sql
-- Menggunakan tabel notifikasi bawaan Laravel (database driver)
-- php artisan notifications:table
CREATE TABLE notifications (
    id          CHAR(36) PRIMARY KEY,          -- UUID
    type        VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id   BIGINT UNSIGNED NOT NULL,
    data        JSON NOT NULL,
    read_at     TIMESTAMP NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL,
    INDEX notifications_notifiable_type_notifiable_id_index (notifiable_type, notifiable_id)
);
```

### Eloquent Models

```php
// app/Models/User.php
class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'avatar', 'location'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    public function sellerProfile(): HasOne { ... }
    public function bookmarks(): HasMany { ... }
    public function subscriptions(): HasMany { ... }
    public function reviews(): HasMany { ... }
    public function notifications(): MorphMany { ... }  // via Notifiable trait

    public function isConsumer(): bool { return $this->role === 'consumer'; }
    public function isSeller(): bool   { return $this->role === 'seller'; }
    public function isAdmin(): bool    { return $this->role === 'admin'; }
}

// app/Models/SellerProfile.php
class SellerProfile extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'business_name', 'business_category', 'description',
                           'address', 'latitude', 'longitude', 'logo', 'is_verified'];

    public function user(): BelongsTo { ... }
    public function promos(): HasMany { ... }
    public function events(): HasMany { ... }
    public function subscribers(): HasMany { ... }  // Subscription model
    public function reviews(): HasMany { ... }

    public function averageRating(): float
    {
        return $this->reviews()->avg('rating') ?? 0.0;
    }
}

// app/Models/Promo.php
class Promo extends Model
{
    use SoftDeletes;
    protected $fillable = ['seller_id', 'category_id', 'title', 'description', 'poster_image',
                           'discount_percentage', 'original_price', 'promo_price',
                           'start_date', 'end_date', 'is_hot_deal', 'view_count', 'status'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'is_hot_deal' => 'boolean'];

    public function seller(): BelongsTo { ... }
    public function category(): BelongsTo { ... }
    public function bookmarks(): HasMany { ... }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeHotDeals(Builder $query): Builder
    {
        return $query->where('status', 'active')
                     ->where('end_date', '<=', now()->addHours(48))
                     ->where('end_date', '>=', now());
    }
}

// app/Models/Event.php
class Event extends Model
{
    protected $fillable = ['seller_id', 'title', 'description', 'location',
                           'event_date', 'end_date', 'poster_image', 'status'];
    protected $casts = ['event_date' => 'datetime', 'end_date' => 'datetime'];

    public function seller(): BelongsTo { ... }
}

// app/Models/Subscription.php
class Subscription extends Model
{
    protected $fillable = ['user_id', 'seller_id'];

    public function user(): BelongsTo { ... }
    public function seller(): BelongsTo { ... }
}

// app/Models/Bookmark.php
class Bookmark extends Model
{
    protected $fillable = ['user_id', 'promo_id'];

    public function user(): BelongsTo { ... }
    public function promo(): BelongsTo { ... }
}

// app/Models/Review.php
class Review extends Model
{
    protected $fillable = ['user_id', 'seller_id', 'promo_id', 'rating', 'comment'];
    protected $casts = ['rating' => 'integer'];

    public function user(): BelongsTo { ... }
    public function seller(): BelongsTo { ... }
    public function promo(): BelongsTo { ... }
}
```

### Entity Relationship Diagram

```
users ──────────────── seller_profiles
  │  1                    1 │
  │  ├── bookmarks ─────────┤ promos
  │  ├── subscriptions ─────┤
  │  └── reviews ───────────┘
  │
  └── notifications (polymorphic via Notifiable)

seller_profiles ──── promos ──── categories
               └──── events

promos ──── bookmarks ──── users
       └─── reviews ──── users
```

## Key Algorithms

### 1. Haversine Distance Calculation

Digunakan untuk menghitung jarak antara Consumer (koordinat browser) dan Seller (koordinat tersimpan di DB).

```php
// app/Services/DistanceService.php
class DistanceService
{
    /**
     * Menghitung jarak antara dua titik koordinat menggunakan formula Haversine.
     * Mengembalikan jarak dalam kilometer.
     *
     * @param float $lat1  Latitude titik pertama (Consumer)
     * @param float $lng1  Longitude titik pertama (Consumer)
     * @param float $lat2  Latitude titik kedua (Seller)
     * @param float $lng2  Longitude titik kedua (Seller)
     * @return float Jarak dalam kilometer
     */
    public function calculate(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371.0; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
```

Untuk query database dengan sorting berdasarkan jarak, digunakan raw SQL expression:

```php
// Di SearchController::index()
$query->selectRaw(
    '*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) )
    * cos( radians( longitude ) - radians(?) ) + sin( radians(?) )
    * sin( radians( latitude ) ) ) ) AS distance',
    [$consumerLat, $consumerLng, $consumerLat]
)->having('distance', '<', 50)  // radius 50km
 ->orderBy('distance');
```

### 2. Hot Deal Logic

```php
// app/Console/Commands/UpdateHotDeals.php
class UpdateHotDeals extends Command
{
    protected $signature   = 'promos:update-hot-deals';
    protected $description = 'Mark promos ending within 48 hours as hot deals';

    public function handle(): void
    {
        $threshold = now()->addHours(48);

        // Set is_hot_deal = true untuk promo yang memenuhi kriteria
        Promo::where('status', 'active')
             ->where('end_date', '<=', $threshold)
             ->where('end_date', '>=', now())
             ->update(['is_hot_deal' => true]);

        // Reset is_hot_deal = false untuk promo yang tidak lagi memenuhi kriteria
        Promo::where('is_hot_deal', true)
             ->where(function ($q) use ($threshold) {
                 $q->where('status', '!=', 'active')
                   ->orWhere('end_date', '>', $threshold)
                   ->orWhere('end_date', '<', now());
             })
             ->update(['is_hot_deal' => false]);
    }
}

// app/Console/Commands/ExpirePromos.php
class ExpirePromos extends Command
{
    protected $signature   = 'promos:expire';
    protected $description = 'Set status=expired for promos past their end_date';

    public function handle(): void
    {
        Promo::where('status', 'active')
             ->where('end_date', '<', now()->toDateString())
             ->update(['status' => 'expired', 'is_hot_deal' => false]);
    }
}
```

Scheduler didaftarkan di `routes/console.php`:
```php
Schedule::command('promos:update-hot-deals')->hourly();
Schedule::command('promos:expire')->hourly();
```

### 3. Notification Dispatch on Promo Publish

```php
// Di AdminController::approvePromo() atau PromoController::store()
// Ketika status promo berubah menjadi 'active':

$promo->update(['status' => 'active']);

$subscribers = Subscription::where('seller_id', $promo->seller_id)
    ->with('user')
    ->get();

foreach ($subscribers as $subscription) {
    $subscription->user->notify(new NewPromoNotification($promo));
}

// app/Notifications/NewPromoNotification.php
class NewPromoNotification extends Notification
{
    public function __construct(private Promo $promo) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'    => 'Promo Baru dari ' . $this->promo->seller->business_name,
            'message'  => $this->promo->title,
            'promo_id' => $this->promo->id,
            'type'     => 'new_promo',
        ];
    }
}
```

### 4. Bookmark Toggle (AJAX)

```php
// BookmarkController::toggle()
public function toggle(Request $request, Promo $promo): JsonResponse
{
    $user = auth()->user();

    $bookmark = Bookmark::where('user_id', $user->id)
                        ->where('promo_id', $promo->id)
                        ->first();

    if ($bookmark) {
        $bookmark->delete();
        $bookmarked = false;
    } else {
        Bookmark::create(['user_id' => $user->id, 'promo_id' => $promo->id]);
        $bookmarked = true;
    }

    $count = Bookmark::where('promo_id', $promo->id)->count();

    return response()->json(['bookmarked' => $bookmarked, 'count' => $count]);
}
```

Alpine.js di Blade template:
```html
<div x-data="{ bookmarked: {{ $isBookmarked ? 'true' : 'false' }}, count: {{ $promo->bookmarks_count }} }">
    <button @click="
        fetch('{{ route('consumer.bookmarks.toggle', $promo) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => { bookmarked = data.bookmarked; count = data.count; })
    ">
        <span x-text="count"></span>
        <svg :class="bookmarked ? 'fill-orange-500' : 'fill-none'" ...></svg>
    </button>
</div>
```

### 5. Search & Filter Algorithm

```php
// SearchController::index()
public function index(Request $request): View
{
    $query = Promo::with(['seller', 'category'])
                  ->active()
                  ->whereHas('seller', fn($q) => $q->where('is_verified', true));

    // Filter by category
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Filter by keyword
    if ($request->filled('q')) {
        $keyword = $request->q;
        $query->where(function ($q) use ($keyword) {
            $q->where('title', 'LIKE', "%{$keyword}%")
              ->orWhere('description', 'LIKE', "%{$keyword}%")
              ->orWhereHas('seller', fn($sq) =>
                  $sq->where('business_name', 'LIKE', "%{$keyword}%")
              );
        });
    }

    // Filter by location (text-based)
    if ($request->filled('location')) {
        $location = $request->location;
        $query->whereHas('seller', fn($q) =>
            $q->where('address', 'LIKE', "%{$location}%")
        );
    }

    // Sort
    match ($request->get('sort', 'latest')) {
        'ending_soon' => $query->orderBy('end_date', 'asc'),
        'most_viewed' => $query->orderBy('view_count', 'desc'),
        default       => $query->orderBy('created_at', 'desc'),
    };

    $promos = $query->paginate(12)->withQueryString();

    return view('public.explore', compact('promos'));
}
```

### 6. File Upload & Storage

```php
// Di StorePromoRequest atau PromoController::store()
if ($request->hasFile('poster_image')) {
    // Hapus file lama jika ada (untuk update)
    if ($promo->poster_image) {
        Storage::disk('public')->delete($promo->poster_image);
    }

    $path = $request->file('poster_image')
                    ->store('promos', 'public');
    // $path = 'promos/randomhash.jpg'
    // URL publik: asset('storage/' . $path)
}
```

Validasi di FormRequest:
```php
'poster_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
```

### 7. FullCalendar.js Integration

Kalender mengambil data via AJAX dari endpoint JSON:

```php
// routes/web.php
Route::get('/api/calendar-events', [CalendarController::class, 'events'])->name('calendar.events');

// CalendarController::events()
public function events(Request $request): JsonResponse
{
    $promos = Promo::active()
        ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
        ->get()
        ->map(fn($p) => [
            'id'    => 'promo-' . $p->id,
            'title' => $p->title,
            'start' => $p->start_date->toDateString(),
            'end'   => $p->end_date->addDay()->toDateString(), // FullCalendar end is exclusive
            'color' => '#f97316',  // orange
            'url'   => route('promos.show', $p),
        ]);

    $events = Event::where('status', 'active')
        ->when($request->category_id, fn($q) =>
            $q->whereHas('seller', fn($sq) =>
                $sq->where('business_category', Category::find($request->category_id)?->name)
            )
        )
        ->get()
        ->map(fn($e) => [
            'id'    => 'event-' . $e->id,
            'title' => $e->title,
            'start' => $e->event_date->toIso8601String(),
            'color' => '#3b82f6',  // blue
        ]);

    return response()->json($promos->merge($events)->values());
}
```

Blade template kalender:
```html
<div id="calendar"></div>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        locale: 'id',
        events: {
            url: '{{ route("calendar.events") }}',
            extraParams: function() {
                return { category_id: document.getElementById('category-filter').value };
            }
        },
        eventClick: function(info) {
            // Tampilkan modal dengan detail event
        }
    });
    calendar.render();
});
</script>
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

**Property Reflection (deduplication):**
- Requirements 2.5 dan 11.1–11.2 keduanya menguji bookmark toggle round-trip → digabung menjadi Property 5
- Requirements 8.3 dan 9.3 keduanya menguji location text filter → dicakup oleh Property 10
- Requirements 13.1 dan 13.2 keduanya menguji file validation → digabung menjadi Property 14

---

### Property 1: Registrasi Consumer selalu menghasilkan role = consumer

*For any* data registrasi Consumer yang valid (nama, email unik, kata sandi, lokasi), membuat akun melalui endpoint registrasi Consumer SHALL menghasilkan User dengan `role = 'consumer'`.

**Validates: Requirements 1.3**

---

### Property 2: Registrasi Seller selalu menghasilkan role = seller dan SellerProfile

*For any* data registrasi Seller yang valid (nama, email unik, kata sandi, nama bisnis, kategori, alamat), membuat akun melalui endpoint registrasi Seller SHALL menghasilkan User dengan `role = 'seller'` DAN satu entri SellerProfile yang terhubung ke User tersebut.

**Validates: Requirements 1.4**

---

### Property 3: Email duplikat selalu ditolak saat registrasi

*For any* email yang sudah terdaftar di database, mengirim formulir registrasi (Consumer atau Seller) dengan email tersebut SHALL ditolak dan tidak membuat akun baru.

**Validates: Requirements 1.5**

---

### Property 4: Kredensial tidak valid tidak membuat sesi

*For any* pasangan email/kata sandi yang tidak cocok dengan akun yang ada, percobaan login SHALL tidak membuat sesi autentikasi baru.

**Validates: Requirements 1.9**

---

### Property 5: Bookmark toggle adalah operasi round-trip

*For any* Promo dan Consumer yang sudah login, melakukan toggle bookmark dua kali berturut-turut SHALL mengembalikan status bookmark ke kondisi awal (bookmarked → unbookmarked → bookmarked, atau sebaliknya).

**Validates: Requirements 2.5, 11.1, 11.2**

---

### Property 6: Feed Consumer Dashboard diurutkan berdasarkan terbaru

*For any* Consumer dengan sejumlah Subscription aktif, feed Promo pada Consumer Dashboard SHALL diurutkan berdasarkan `created_at` secara descending — setiap Promo pada posisi i memiliki `created_at` >= Promo pada posisi i+1.

**Validates: Requirements 2.3**

---

### Property 7: Hot Deals section berisi tepat promo yang memenuhi kriteria

*For any* kumpulan Promo dengan berbagai status dan `end_date`, seksi Hot Deals SHALL menampilkan tepat promo-promo yang memiliki `status = 'active'` DAN `end_date` berada dalam rentang [now, now + 48 jam].

**Validates: Requirements 2.4, 6.1**

---

### Property 8: Promo baru selalu dibuat dengan status = draft

*For any* data Promo yang valid yang dikirim oleh Seller melalui formulir unggah, entri Promo yang dibuat SHALL memiliki `status = 'draft'`.

**Validates: Requirements 3.3**

---

### Property 9: Scheduler UpdateHotDeals mempertahankan invariant is_hot_deal

*For any* kumpulan Promo dengan berbagai status dan `end_date`, setelah menjalankan perintah `promos:update-hot-deals`, setiap Promo SHALL memiliki `is_hot_deal = true` jika dan hanya jika `status = 'active'` DAN `end_date <= now() + 48 jam` DAN `end_date >= now()`.

**Validates: Requirements 6.4**

---

### Property 10: Scheduler ExpirePromos mempertahankan invariant status expired

*For any* kumpulan Promo aktif dengan berbagai `end_date`, setelah menjalankan perintah `promos:expire`, setiap Promo dengan `end_date < today` SHALL memiliki `status = 'expired'`, dan Promo dengan `end_date >= today` SHALL tetap memiliki `status = 'active'`.

**Validates: Requirements 6.5**

---

### Property 11: Subscribe/unsubscribe adalah operasi round-trip

*For any* pasangan Consumer dan Seller, melakukan subscribe kemudian unsubscribe SHALL menghasilkan tidak adanya entri Subscription antara Consumer dan Seller tersebut.

**Validates: Requirements 7.2, 7.3**

---

### Property 12: Jumlah notifikasi yang dikirim sama dengan jumlah subscriber

*For any* Seller dengan N Subscription aktif, ketika Seller tersebut mempublikasikan Promo baru (status berubah menjadi `active`), SHALL terbuat tepat N entri Notification — satu untuk setiap Consumer yang berlangganan.

**Validates: Requirements 7.4**

---

### Property 13: Unread notification count sama dengan jumlah notifikasi dengan read_at = null

*For any* User dengan sejumlah Notification (campuran yang sudah dan belum dibaca), jumlah notifikasi yang belum dibaca yang ditampilkan SHALL sama dengan `count(notifications WHERE read_at IS NULL AND notifiable_id = user.id)`.

**Validates: Requirements 7.7**

---

### Property 14: Category filter hanya mengembalikan promo dari kategori yang dipilih

*For any* filter `category_id` yang diterapkan pada halaman Explore, semua Promo yang dikembalikan SHALL memiliki `category_id` yang sama dengan filter yang dipilih.

**Validates: Requirements 8.2**

---

### Property 15: Keyword search mengembalikan hanya hasil yang mengandung keyword

*For any* kata kunci pencarian, semua Promo yang dikembalikan SHALL mengandung kata kunci tersebut pada setidaknya satu dari kolom: `promos.title`, `promos.description`, atau `seller_profiles.business_name`.

**Validates: Requirements 8.4**

---

### Property 16: Haversine distance memenuhi sifat-sifat metrik dasar

*For any* pasangan koordinat (lat1, lng1) dan (lat2, lng2), fungsi `DistanceService::calculate()` SHALL memenuhi:
- `distance(A, A) = 0` (identitas)
- `distance(A, B) = distance(B, A)` (simetri)
- `distance(A, B) >= 0` (non-negatif)

**Validates: Requirements 9.4**

---

### Property 17: Sort by distance menghasilkan urutan ascending

*For any* kumpulan Seller dengan koordinat dan titik referensi Consumer, hasil pencarian yang diurutkan berdasarkan jarak SHALL menghasilkan urutan ascending — jarak Seller pada posisi i <= jarak Seller pada posisi i+1.

**Validates: Requirements 9.5**

---

### Property 18: Review duplikat dari Consumer yang sama ditolak

*For any* pasangan Consumer dan Seller, jika Consumer sudah memiliki Review untuk Seller tersebut, pengiriman Review kedua SHALL ditolak dan jumlah Review Seller tidak bertambah.

**Validates: Requirements 10.3**

---

### Property 19: Average rating adalah rata-rata aritmetika dari semua review

*For any* Seller dengan sejumlah Review, nilai `averageRating()` SHALL sama dengan `sum(rating) / count(reviews)` dengan presisi dua desimal.

**Validates: Requirements 10.4**

---

### Property 20: Respons JSON bookmark toggle berisi status dan count yang akurat

*For any* operasi toggle bookmark pada Promo, respons JSON SHALL berisi `bookmarked` yang mencerminkan status bookmark terkini Consumer tersebut, dan `count` yang sama dengan jumlah total Bookmark untuk Promo tersebut di database.

**Validates: Requirements 11.3**

---

### Property 21: Validasi file upload menerima format valid dan menolak format tidak valid

*For any* file yang diunggah, validasi SHALL menerima file jika dan hanya jika format adalah JPEG, PNG, atau WebP DAN ukuran file <= 2MB (2048 KB).

**Validates: Requirements 13.1, 13.2**

---

### Property 22: API endpoint mengembalikan semua field yang dipersyaratkan

*For any* Promo aktif, respons dari `GET /api/promos` dan `GET /api/promos/{id}` SHALL mengandung semua field yang dipersyaratkan: `id`, `title`, `description`, `discount_percentage`, `promo_price`, `start_date`, `end_date`, `poster_image`, `seller.name`, dan `category.name`.

**Validates: Requirements 14.1, 14.3**

## Error Handling

### HTTP Error Responses

```php
// app/Exceptions/Handler.php — custom error pages
// 404: resources/views/errors/404.blade.php
// 403: resources/views/errors/403.blade.php
// 500: resources/views/errors/500.blade.php
```

### Validation Errors

Semua FormRequest mengembalikan error dalam format yang konsisten:
- Web routes: redirect back dengan `$errors` bag dan input lama
- AJAX/API routes: JSON response dengan HTTP 422 dan array `errors`

```php
// Contoh pesan validasi Bahasa Indonesia di lang/id/validation.php
'required' => 'Kolom :attribute wajib diisi.',
'email'    => 'Kolom :attribute harus berupa alamat email yang valid.',
'unique'   => ':attribute sudah digunakan.',
'max'      => [
    'file'   => 'Ukuran file :attribute tidak boleh lebih dari :max kilobyte.',
    'string' => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
],
'mimes'    => 'Format file :attribute harus berupa: :values.',
```

### API Error Responses

```php
// API/PromoController::show() — 404 handling
public function show(int $id): JsonResponse
{
    $promo = Promo::active()->find($id);

    if (!$promo) {
        return response()->json([
            'message' => 'Promo tidak ditemukan',
        ], 404);
    }

    return response()->json(new PromoResource($promo));
}
```

### File Upload Errors

```php
// StorePromoRequest
public function rules(): array
{
    return [
        'poster_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
    ];
}

public function messages(): array
{
    return [
        'poster_image.mimes' => 'Format file tidak didukung atau ukuran melebihi 2MB.',
        'poster_image.max'   => 'Format file tidak didukung atau ukuran melebihi 2MB.',
    ];
}
```

### Soft Delete Handling

Promo yang di-soft-delete tetap dapat diakses melalui relasi Bookmark dengan `withTrashed()`:

```php
// ConsumerController::bookmarks()
$bookmarks = auth()->user()
    ->bookmarks()
    ->with(['promo' => fn($q) => $q->withTrashed()])
    ->get();

// Di Blade template:
@if($bookmark->promo->trashed() || $bookmark->promo->status === 'expired')
    <span class="badge">Tidak Tersedia</span>
@endif
```

### Authorization Errors

```php
// Seller hanya bisa mengedit promo miliknya sendiri
public function edit(Promo $promo): View
{
    abort_if($promo->seller_id !== auth()->user()->sellerProfile->id, 403);
    return view('seller.promos.edit', compact('promo'));
}
```

## Testing Strategy

### Dual Testing Approach

Platform menggunakan dua lapisan pengujian yang saling melengkapi:

1. **Unit/Feature Tests** — menguji contoh spesifik, edge case, dan kondisi error
2. **Property-Based Tests** — menguji properti universal di seluruh input yang di-generate

### Test Framework

- **PHPUnit** (sudah termasuk di Laravel) untuk unit dan feature tests
- **PestPHP** (`pestphp/pest`) sebagai test runner dengan sintaks yang lebih ekspresif
- **Pest Plugin Faker** untuk data generation
- **eris/eris** atau **giorgiosironi/eris** sebagai library property-based testing untuk PHP

```bash
composer require --dev pestphp/pest pestphp/pest-plugin-laravel
composer require --dev giorgiosironi/eris
```

### Unit Tests

Fokus pada contoh spesifik dan edge case:

```
tests/Unit/
  DistanceServiceTest.php       — Haversine calculation examples
  PromoScopeTest.php            — active(), hotDeals() scopes
  AverageRatingTest.php         — averageRating() calculation
  FileValidationTest.php        — file format/size validation

tests/Feature/
  Auth/
    ConsumerRegistrationTest.php
    SellerRegistrationTest.php
    LoginTest.php
  Consumer/
    DashboardTest.php
    BookmarkTest.php
    SubscriptionTest.php
    NotificationTest.php
  Seller/
    DashboardTest.php
    PromoManagementTest.php
    EventManagementTest.php
  Admin/
    VerificationTest.php
    ModerationTest.php
    CategoryTest.php
  Public/
    ExploreTest.php
    CalendarTest.php
    HotDealsTest.php
    SellerProfileTest.php
  Api/
    PromoApiTest.php
    SellerApiTest.php
  Scheduler/
    UpdateHotDealsTest.php
    ExpirePromosTest.php
```

### Property-Based Tests

Setiap property dari bagian Correctness Properties diimplementasikan sebagai satu property-based test dengan minimum 100 iterasi.

```
tests/Property/
  RegistrationPropertyTest.php    — Properties 1, 2, 3, 4
  BookmarkPropertyTest.php        — Properties 5, 20
  FeedOrderingPropertyTest.php    — Property 6
  HotDealPropertyTest.php         — Properties 7, 9, 10
  PromoCreationPropertyTest.php   — Property 8
  SubscriptionPropertyTest.php    — Properties 11, 12
  NotificationPropertyTest.php    — Property 13
  SearchPropertyTest.php          — Properties 14, 15
  DistancePropertyTest.php        — Properties 16, 17
  ReviewPropertyTest.php          — Properties 18, 19
  FileUploadPropertyTest.php      — Property 21
  ApiPropertyTest.php             — Property 22
```

Contoh implementasi property test:

```php
// tests/Property/BookmarkPropertyTest.php
use Eris\Generator;
use Eris\TestTrait;

class BookmarkPropertyTest extends TestCase
{
    use TestTrait;

    /**
     * Feature: promora-umkm-platform, Property 5: Bookmark toggle adalah operasi round-trip
     */
    public function test_bookmark_toggle_is_round_trip(): void
    {
        $this->forAll(
            Generator\choose(1, 100),  // promo count
        )
        ->withMaxSize(100)
        ->then(function (int $promoCount) {
            $consumer = User::factory()->consumer()->create();
            $promo    = Promo::factory()->active()->create();

            $initialState = Bookmark::where('user_id', $consumer->id)
                                    ->where('promo_id', $promo->id)
                                    ->exists();

            // Toggle dua kali
            $this->actingAs($consumer)
                 ->postJson(route('consumer.bookmarks.toggle', $promo));
            $this->actingAs($consumer)
                 ->postJson(route('consumer.bookmarks.toggle', $promo));

            $finalState = Bookmark::where('user_id', $consumer->id)
                                  ->where('promo_id', $promo->id)
                                  ->exists();

            $this->assertEquals($initialState, $finalState);
        });
    }
}
```

```php
// tests/Property/DistancePropertyTest.php
/**
 * Feature: promora-umkm-platform, Property 16: Haversine distance memenuhi sifat-sifat metrik dasar
 */
public function test_haversine_distance_metric_properties(): void
{
    $service = new DistanceService();

    $this->forAll(
        Generator\float(-90, 90),   // lat1
        Generator\float(-180, 180), // lng1
        Generator\float(-90, 90),   // lat2
        Generator\float(-180, 180), // lng2
    )
    ->withMaxSize(100)
    ->then(function (float $lat1, float $lng1, float $lat2, float $lng2) use ($service) {
        // Identitas: distance(A, A) = 0
        $this->assertEquals(0.0, $service->calculate($lat1, $lng1, $lat1, $lng1), '', 0.0001);

        // Non-negatif
        $dist = $service->calculate($lat1, $lng1, $lat2, $lng2);
        $this->assertGreaterThanOrEqual(0.0, $dist);

        // Simetri: distance(A, B) = distance(B, A)
        $distReverse = $service->calculate($lat2, $lng2, $lat1, $lng1);
        $this->assertEquals($dist, $distReverse, '', 0.0001);
    });
}
```

### Test Configuration

```xml
<!-- phpunit.xml -->
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
</php>
```

Property tests dikonfigurasi untuk minimum 100 iterasi:
```php
// Di setiap property test class
protected function setUp(): void
{
    parent::setUp();
    $this->minimumEvaluationRatio(1.0);
    $this->withMaxSize(100);  // 100 iterasi minimum
}
```

### Testing Balance

- Unit tests menangani: contoh spesifik, integrasi antar komponen, edge case, dan kondisi error
- Property tests menangani: properti universal yang harus berlaku untuk semua input
- Hindari menulis terlalu banyak unit test untuk kasus yang sudah dicakup property tests
- Integration tests (dengan database nyata) untuk: scheduler commands, file upload, API endpoints
