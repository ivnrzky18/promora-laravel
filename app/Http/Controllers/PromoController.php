<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Models\Category;
use App\Models\Promo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PromoController extends Controller
{
    /**
     * Display a listing of the seller's promos.
     */
    public function index(): View
    {
        $promos = auth()->user()->sellerProfile
            ->promos()
            ->with('category')
            ->latest()
            ->get();

        return view('seller.promos.index', compact('promos'));
    }

    /**
     * Show the form for creating a new promo.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('seller.promos.create', compact('categories'));
    }

    /**
     * Store a newly created promo in storage.
     */
    public function store(StorePromoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('poster_image')) {
            $data['poster_image'] = $request->file('poster_image')->store('promos', 'public');
        }

        $data['seller_id'] = auth()->user()->sellerProfile->id;
        $data['status']    = 'draft';

        Promo::create($data);

        return redirect()->route('seller.promos.index')
            ->with('success', 'Promo berhasil dibuat.');
    }

    /**
     * Display the Hot Deals page — promos ending within 48 hours, ordered by end_date.
     */
    public function hotDeals(): View
    {
        $hotDeals = Promo::hotDeals()
            ->with(['seller', 'category'])
            ->orderBy('end_date')
            ->get();

        return view('public.hot-deals', compact('hotDeals'));
    }

    /**
     * Display the specified promo (public view, increments view count).
     */
    public function show(Promo $promo): View
    {
        $promo->increment('view_count');

        return view('public.promos.show', compact('promo'));
    }

    /**
     * Show the form for editing the specified promo.
     */
    public function edit(Promo $promo): View
    {
        abort_if($promo->seller_id !== auth()->user()->sellerProfile->id, 403);

        $categories = Category::orderBy('name')->get();

        return view('seller.promos.edit', compact('promo', 'categories'));
    }

    /**
     * Update the specified promo in storage.
     */
    public function update(UpdatePromoRequest $request, Promo $promo): RedirectResponse
    {
        abort_if($promo->seller_id !== auth()->user()->sellerProfile->id, 403);

        $data = $request->validated();

        if ($request->hasFile('poster_image')) {
            // Delete old poster if exists
            if ($promo->poster_image) {
                Storage::disk('public')->delete($promo->poster_image);
            }

            $data['poster_image'] = $request->file('poster_image')->store('promos', 'public');
        }

        $promo->update($data);

        return redirect()->route('seller.promos.index')
            ->with('success', 'Promo berhasil diperbarui.');
    }

    /**
     * Remove the specified promo from storage (soft delete).
     */
    public function destroy(Promo $promo): RedirectResponse
    {
        abort_if($promo->seller_id !== auth()->user()->sellerProfile->id, 403);

        $promo->delete();

        return redirect()->route('seller.promos.index')
            ->with('success', 'Promo berhasil dihapus.');
    }
}
