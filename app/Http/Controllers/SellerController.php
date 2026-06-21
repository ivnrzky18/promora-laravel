<?php

namespace App\Http\Controllers;

use App\Models\SellerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class SellerController extends Controller
{
    /**
     * Show the seller dashboard with statistics.
     */
    public function dashboard(): View
    {
        $sellerProfile = auth()->user()->sellerProfile()
            ->with(['subscribers'])
            ->first();

        // =========================
        // PROMO STATS
        // =========================
        $totalPromos  = $sellerProfile->promos()->count();
        $promoViews   = $sellerProfile->promos()->sum('view_count');
        $activePromos = $sellerProfile->promos()
            ->where('status', 'active')
            ->count();

        // =========================
        // EVENT STATS
        // =========================
        $totalEvents = $sellerProfile->events()->count();

        $activeEvents = $sellerProfile->events()
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->count();

        // kalau tabel events kamu belum punya kolom view_count, pakai 0
        $eventViews = 0;
        if (\Schema::hasColumn('events', 'view_count')) {
            $eventViews = $sellerProfile->events()->sum('view_count');
        }

        // =========================
        // OTHER STATS
        // =========================
        $subscriberCount = $sellerProfile->subscribers()->count();
        $averageRating   = $sellerProfile->averageRating();
        $totalViews      = $promoViews + $eventViews;

        // =========================
        // PROMO LIST
        // =========================
        $promos = $sellerProfile->promos()
            ->with('category')
            ->latest()
            ->get();

        // =========================
        // EVENT LIST
        // =========================
        $events = $sellerProfile->events()
            ->latest()
            ->get();

        return view('seller.dashboard', compact(
            'sellerProfile',
            'totalPromos',
            'totalViews',
            'activePromos',
            'subscriberCount',
            'averageRating',
            'promos',

            // event data
            'totalEvents',
            'activeEvents',
            'events'
        ));
    }

    /**
     * Show the seller profile edit page.
     */
    public function profile(): View
    {
        $sellerProfile = auth()->user()->sellerProfile;

        return view('seller.profile', compact('sellerProfile'));
    }

    /**
     * Update the seller profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'business_name'     => 'required|string|max:255',
            'business_category' => 'required|string|max:100',
            'description'       => 'nullable|string',
            'address'           => 'required|string|max:500',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'logo'              => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $sellerProfile = auth()->user()->sellerProfile;

        $data = $request->only([
            'business_name',
            'business_category',
            'description',
            'address',
            'latitude',
            'longitude',
        ]);

        if ($request->hasFile('logo')) {
            if ($sellerProfile->logo) {
                Storage::disk('public')->delete($sellerProfile->logo);
            }

            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $sellerProfile->update($data);

        return redirect()
            ->route('seller.dashboard')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}