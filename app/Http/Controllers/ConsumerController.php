<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Promo;
use App\Models\SellerProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsumerController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = auth()->user();

        $mode = $request->get('mode', 'promo');

        // ===============================
        // STATS
        // ===============================
        $bookmarkCount = $user->bookmarks()->count();
        $subscriptionCount = $user->subscriptions()->count();

        $expiringPromoCount = Promo::query()
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now())
            ->whereDate('end_date', '<=', now()->copy()->addDays(3))
            ->count();

        $newSellerCount = SellerProfile::query()
            ->whereDate('created_at', '>=', now()->copy()->subDays(30))
            ->count();

        // ===============================
        // FILTER INPUT
        // ===============================
        $q          = $request->get('q');
        $categoryId = $request->get('category_id');
        $sort       = $request->get('sort', 'latest');

      // ===============================
// PROMO FEED
// ===============================
$promoQuery = Promo::with(['seller', 'category'])
    ->where('status', 'active')
    ->whereDate('end_date', '>=', now());

if ($q) {
    $promoQuery->where(function ($query) use ($q) {
        $query->where('title', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->orWhereHas('seller', function ($sellerQuery) use ($q) {
                $sellerQuery->where('business_name', 'like', "%{$q}%");
            });
    });
}

if ($categoryId) {
    $promoQuery->where('category_id', $categoryId);
}

/*
|--------------------------------------------------------------------------
| PRIORITAS PREMIUM DULU
|--------------------------------------------------------------------------
| 1. Promo premium selalu paling atas
| 2. Setelah itu baru sorting berdasarkan pilihan user
*/
$promoQuery->orderByDesc('is_premium');

if ($sort === 'ending_soon') {
    $promoQuery->orderBy('end_date', 'asc');
} elseif ($sort === 'most_viewed') {
    $promoQuery->orderByDesc('view_count');
} else {
    $promoQuery->latest(); // created_at desc
}

$promoFeed = $promoQuery->get();

        // ===============================
        // EVENT FEED  ← INI YANG PENTING
        // ===============================
        $eventQuery = Event::with('seller')
            ->where('status', 'active')
            ->whereDate('event_date', '>=', now()->startOfDay());

        if ($q) {
            $eventQuery->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhereHas('seller', function ($sellerQuery) use ($q) {
                        $sellerQuery->where('business_name', 'like', "%{$q}%");
                    });
            });
        }

        // kalau mode event, tampilkan semua event aktif + urut premium dulu
        $eventFeed = $eventQuery
            ->orderByDesc('is_premium')
            ->orderBy('event_date', 'asc')
            ->get();

        // ===============================
        // PREMIUM EVENTS
        // ===============================
        $premiumEvents = Event::with('seller')
            ->where('status', 'active')
            ->where('is_premium', true)
            ->whereDate('event_date', '>=', now()->startOfDay())
            ->orderBy('event_date', 'asc')
            ->get();

        // ===============================
        // UPCOMING EVENTS
        // ===============================
        $upcomingEvents = Event::with('seller')
            ->where('status', 'active')
            ->whereDate('event_date', '>=', now()->startOfDay())
            ->orderByDesc('is_premium')
            ->orderBy('event_date', 'asc')
            ->get();

        // ===============================
        // HOT DEALS
        // ===============================
        $hotDeals = Promo::with(['seller', 'category'])
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now())
            ->whereDate('end_date', '<=', now()->copy()->addDays(2))
            ->orderBy('end_date', 'asc')
            ->take(6)
            ->get();

        // ===============================
        // NOTIFICATIONS
        // ===============================
        $recentNotifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get();

        return view('consumer.dashboard', [
            'user'               => $user,
            'mode'               => $mode,
            'promoFeed'          => $promoFeed,
            'eventFeed'          => $eventFeed,
            'premiumEvents'      => $premiumEvents,
            'upcomingEvents'     => $upcomingEvents,
            'hotDeals'           => $hotDeals,
            'recentNotifications'=> $recentNotifications,
            'bookmarkCount'      => $bookmarkCount,
            'subscriptionCount'  => $subscriptionCount,
            'expiringPromoCount' => $expiringPromoCount,
            'newSellerCount'     => $newSellerCount,
        ]);
    }

    public function bookmarks(): View
    {
        $bookmarks = auth()->user()->bookmarks()
            ->with('promo.seller', 'promo.category')
            ->latest()
            ->get();

        return view('consumer.bookmarks', compact('bookmarks'));
    }

    public function profile(): View
    {
        $user = auth()->user();
        return view('consumer.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}