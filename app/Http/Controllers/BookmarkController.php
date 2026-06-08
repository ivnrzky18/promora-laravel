<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Promo;
use Illuminate\Http\JsonResponse;

class BookmarkController extends Controller
{
    /**
     * Toggle bookmark for the authenticated consumer.
     *
     * Middleware: consumer (applied via route definition)
     *
     * @param  Promo  $promo
     * @return JsonResponse  { bookmarked: bool, count: int }
     */
    public function toggle(Promo $promo): JsonResponse
    {
        $user = auth()->user();

        $bookmark = Bookmark::where('user_id', $user->id)
                            ->where('promo_id', $promo->id)
                            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $bookmarked = false;
        } else {
            Bookmark::create([
                'user_id'  => $user->id,
                'promo_id' => $promo->id,
            ]);
            $bookmarked = true;
        }

        $count = Bookmark::where('promo_id', $promo->id)->count();

        return response()->json([
            'bookmarked' => $bookmarked,
            'count'      => $count,
        ]);
    }
}
