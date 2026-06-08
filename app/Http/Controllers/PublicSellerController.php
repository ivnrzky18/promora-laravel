<?php

namespace App\Http\Controllers;

use App\Models\SellerProfile;
use App\Models\Subscription;
use Illuminate\View\View;

class PublicSellerController extends Controller
{
    /**
     * Display the public profile page for a seller.
     */
    public function show(SellerProfile $sellerProfile): View
    {
        $promos        = $sellerProfile->promos()->active()->with('category')->latest()->get();
        $reviews       = $sellerProfile->reviews()->with('user')->latest()->get();
        $averageRating = $sellerProfile->averageRating();

        $isSubscribed = false;
        if (auth()->check()) {
            $isSubscribed = Subscription::where('user_id', auth()->id())
                                        ->where('seller_id', $sellerProfile->id)
                                        ->exists();
        }

        return view('public.sellers.show', compact('sellerProfile', 'promos', 'reviews', 'averageRating', 'isSubscribed'));
    }
}
