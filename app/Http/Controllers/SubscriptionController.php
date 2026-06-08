<?php

namespace App\Http\Controllers;

use App\Models\SellerProfile;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    /**
     * Toggle subscription to a seller.
     * Creates a subscription if none exists, deletes it otherwise.
     */
    public function toggle(SellerProfile $seller): JsonResponse
    {
        $user = auth()->user();

        $subscription = Subscription::where('user_id', $user->id)
                                     ->where('seller_id', $seller->id)
                                     ->first();

        if ($subscription) {
            $subscription->delete();
            $subscribed = false;
        } else {
            Subscription::create(['user_id' => $user->id, 'seller_id' => $seller->id]);
            $subscribed = true;
        }

        return response()->json(['subscribed' => $subscribed]);
    }
}
