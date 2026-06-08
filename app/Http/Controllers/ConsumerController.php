<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Promo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConsumerController extends Controller
{
    /**
     * Consumer dashboard: feed from subscriptions + hot deals + stats.
     */
    public function dashboard(): View
    {
        $user = auth()->user();

        // Stats
        $bookmarkCount     = $user->bookmarks()->count();
        $subscriptionCount = $user->subscriptions()->count();

        // Subscribed seller IDs
        $subscribedSellerIds = $user->subscriptions()->pluck('seller_id');

        // Feed: promos from subscribed sellers, latest first
        if ($subscribedSellerIds->isEmpty()) {
            $promoFeed = collect();
        } else {
            $promoFeed = Promo::with(['seller', 'category'])
                ->where('status', 'active')
                ->whereIn('seller_id', $subscribedSellerIds)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Hot deals: active promos ending within 48 hours
        $hotDeals = Promo::with(['seller', 'category'])
            ->hotDeals()
            ->orderBy('end_date')
            ->take(4)
            ->get();

        return view('consumer.dashboard', compact(
            'user',
            'bookmarkCount',
            'subscriptionCount',
            'promoFeed',
            'hotDeals'
        ));
    }

    /**
     * Bookmarks page: all bookmarked promos including soft-deleted ones.
     */
    public function bookmarks(): View
    {
        $user = auth()->user();

        $bookmarks = $user->bookmarks()
            ->with(['promo' => function ($query) {
                $query->withTrashed()->with('seller');
            }])
            ->latest()
            ->get();

        return view('consumer.bookmarks', compact('bookmarks'));
    }

    /**
     * Consumer profile page.
     */
    public function profile(): View
    {
        return view('consumer.profile', ['user' => auth()->user()]);
    }

    /**
     * Update consumer profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'location' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
}
