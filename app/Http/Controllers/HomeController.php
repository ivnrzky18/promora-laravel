<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Promo;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the landing page with latest promos, hot deals, and categories.
     */
    public function index(): View
    {
        $latestPromos = Promo::active()
            ->with(['seller', 'category'])
            ->latest()
            ->take(6)
            ->get();

        $hotDeals = Promo::hotDeals()
            ->with(['seller', 'category'])
            ->orderBy('end_date')
            ->take(4)
            ->get();

        $categories = Category::withCount(['promos' => fn ($q) => $q->where('status', 'active')])
            ->orderBy('name')
            ->get();

        return view('public.home', compact('latestPromos', 'hotDeals', 'categories'));
    }
}
