<?php

namespace App\Http\Controllers;

use App\Models\SellerProfile;
use App\Models\Subscription;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;

class PublicSellerController extends Controller
{
    /**
     * Display the public profile page for a seller.
     */
    public function show(SellerProfile $sellerProfile): View
    {
        // =========================
        // PROMO SELLER
        // =========================
        $promos = $sellerProfile->promos()
            ->active()
            ->with('category')
            ->latest()
            ->get();

        // =========================
        // REVIEW SELLER
        // =========================
        $reviews       = $sellerProfile->reviews()->with('user')->latest()->get();
        $averageRating = $sellerProfile->averageRating();

        // =========================
        // EVENT SELLER
        // =========================
        $eventQuery = $sellerProfile->events()
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });

        // kalau ada kolom status di tabel events, aktifkan filter active
        if (Schema::hasColumn('events', 'status')) {
            $eventQuery->where('status', 'active');
        }

        // urutkan event terdekat
        $events = (clone $eventQuery)
            ->orderBy('event_date', 'asc')
            ->get();

        // event premium seller
        if (Schema::hasColumn('events', 'is_premium')) {
            $premiumEvents = $sellerProfile->events()
                ->where('is_premium', true)
                ->where(function ($q) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
                });

            if (Schema::hasColumn('events', 'status')) {
                $premiumEvents->where('status', 'active');
            }

            $premiumEvents = $premiumEvents
                ->orderBy('event_date', 'asc')
                ->get();
        } else {
            $premiumEvents = collect();
        }

        // =========================
        // STATUS SUBSCRIBE
        // =========================
        $isSubscribed = false;
        if (auth()->check()) {
            $isSubscribed = Subscription::where('user_id', auth()->id())
                ->where('seller_id', $sellerProfile->id)
                ->exists();
        }

        return view('public.sellers.show', compact(
            'sellerProfile',
            'promos',
            'reviews',
            'averageRating',
            'isSubscribed',

            // event
            'events',
            'premiumEvents'
        ));
    }
}