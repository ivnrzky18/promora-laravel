<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Display the explore/search page with filters.
     */
    public function index(Request $request): View
    {
        $query = Promo::with(['seller', 'category'])
                      ->active()
                      ->whereHas('seller', fn ($q) => $q->where('is_verified', true));

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
                  ->orWhereHas('seller', fn ($sq) =>
                      $sq->where('business_name', 'LIKE', "%{$keyword}%")
                  );
            });
        }

        // Filter by location (text-based)
        if ($request->filled('location')) {
            $location = $request->location;
            $query->whereHas('seller', fn ($q) =>
                $q->where('address', 'LIKE', "%{$location}%")
            );
        }

        // Location-based search (Haversine) — requires lat+lng from browser geolocation
        if ($request->filled('lat') && $request->filled('lng')) {
            $lat = (float) $request->lat;
            $lng = (float) $request->lng;

            // Join seller_profiles to access latitude/longitude columns
            $query->join('seller_profiles', 'promos.seller_id', '=', 'seller_profiles.id')
                  ->selectRaw(
                      'promos.*, ( 6371 * acos( cos( radians(?) ) * cos( radians( seller_profiles.latitude ) )
                      * cos( radians( seller_profiles.longitude ) - radians(?) ) + sin( radians(?) )
                      * sin( radians( seller_profiles.latitude ) ) ) ) AS distance',
                      [$lat, $lng, $lat]
                  )
                  ->having('distance', '<', 50);
        }

        // Sort
        $sort = $request->get('sort', 'latest');

        if ($sort === 'nearest' && $request->filled('lat') && $request->filled('lng')) {
            $query->orderBy('distance', 'asc');
        } else {
            match ($sort) {
                'ending_soon' => $query->orderBy('end_date', 'asc'),
                'most_viewed' => $query->orderBy('view_count', 'desc'),
                default       => $query->orderBy('promos.created_at', 'desc'),
            };
        }

        $promos     = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('public.explore', compact('promos', 'categories'));
    }
}
