<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\NewPromoNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard with pending seller verifications.
     */
    public function dashboard(): View
    {
        $pendingSellers = SellerProfile::where('is_verified', false)
            ->with('user')
            ->latest()
            ->get();

        return view('admin.dashboard', compact('pendingSellers'));
    }

    /**
     * Verify a seller (set is_verified = true).
     */
    public function verifySeller(SellerProfile $seller): RedirectResponse
    {
        $seller->update(['is_verified' => true]);

        return redirect()->back()
            ->with('success', 'Seller berhasil diverifikasi.');
    }

    /**
     * Reject a seller by deleting the user (cascades to SellerProfile).
     */
    public function rejectSeller(SellerProfile $seller): RedirectResponse
    {
        $seller->user->delete();

        return redirect()->back()
            ->with('success', 'Seller berhasil ditolak.');
    }

    /**
     * Approve a promo (set status = active) and notify subscribers.
     */
    public function approvePromo(Promo $promo): RedirectResponse
    {
        $promo->update(['status' => 'active']);

        $subscribers = Subscription::where('seller_id', $promo->seller_id)
            ->with('user')
            ->get();

        foreach ($subscribers as $subscription) {
            $subscription->user->notify(new NewPromoNotification($promo));
        }

        return redirect()->back()
            ->with('success', 'Promo berhasil disetujui.');
    }

    /**
     * Reject a promo by force deleting it.
     */
    public function rejectPromo(Promo $promo): RedirectResponse
    {
        $promo->forceDelete();

        return redirect()->back()
            ->with('success', 'Promo berhasil ditolak.');
    }

    /**
     * Show platform statistics.
     */
    public function stats(): View
    {
        $totalConsumers = User::where('role', 'consumer')->count();
        $totalSellers   = User::where('role', 'seller')->count();
        $activePromos   = Promo::where('status', 'active')->count();
        $activeEvents   = Event::where('status', 'active')->count();

        return view('admin.stats', compact(
            'totalConsumers',
            'totalSellers',
            'activePromos',
            'activeEvents'
        ));
    }

    /**
     * Show draft promos pending moderation.
     */
    public function promos(): View
    {
        $promos = Promo::where('status', 'draft')
            ->with(['seller', 'category'])
            ->latest()
            ->get();

        return view('admin.promos.index', compact('promos'));
    }
}
