<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    /**
     * Store a new review for a seller.
     */
    public function store(StoreReviewRequest $request): RedirectResponse
    {
        // Application-level duplicate check
        $exists = Review::where('user_id', auth()->id())
                        ->where('seller_id', $request->seller_id)
                        ->exists();

        if ($exists) {
            return redirect()->back()->withErrors([
                'review' => 'Anda sudah memberikan ulasan untuk seller ini.',
            ]);
        }

        Review::create([
            'user_id'   => auth()->id(),
            'seller_id' => $request->seller_id,
            'rating'    => $request->rating,
            'comment'   => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Ulasan berhasil dikirim.');
    }
}
