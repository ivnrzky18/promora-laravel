<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromoRequest;
use App\Http\Requests\UpdatePromoRequest;
use App\Models\Category;
use App\Models\Promo;
use App\Notifications\NewPromoNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PromoController extends Controller
{
    public function index(): View
    {
        $promos = auth()->user()->sellerProfile
            ->promos()
            ->with('category')
            ->latest()
            ->get();

        return view('seller.promos.index', compact('promos'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('seller.promos.create', compact('categories'));
    }

  public function store(StorePromoRequest $request): RedirectResponse
{
    try {
        $sellerProfile = auth()->user()->sellerProfile;

        if (!$sellerProfile) {
            return back()->with('error', 'Seller profile tidak ditemukan.');
        }

        $data = $request->validated();

        if ($request->hasFile('poster_image')) {
            $data['poster_image'] = $request->file('poster_image')->store('promos', 'public');
        }

        $data['seller_id'] = $sellerProfile->id;
        $data['status'] = 'active';
        $data['is_premium'] = $request->boolean('is_premium');

        Promo::create($data);

        return redirect()
            ->route('seller.promos.index')
            ->with('success', 'Promo berhasil dibuat.');
    } catch (\Throwable $e) {
        dd([
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);
    }
}

    public function hotDeals(): View
    {
        $hotDeals = Promo::hotDeals()
            ->with(['seller', 'category'])
            ->orderBy('end_date')
            ->get();

        return view('public.hot-deals', compact('hotDeals'));
    }

    public function show(Promo $promo): View
    {
        $promo->increment('view_count');

        return view('public.promos.show', compact('promo'));
    }

    public function edit(Promo $promo): View
    {
        abort_if($promo->seller_id !== auth()->user()->sellerProfile->id, 403);

        $categories = Category::orderBy('name')->get();

        return view('seller.promos.edit', compact('promo', 'categories'));
    }

    public function update(UpdatePromoRequest $request, Promo $promo): RedirectResponse
    {
        abort_if($promo->seller_id !== auth()->user()->sellerProfile->id, 403);

        $data = $request->validated();

        if ($request->hasFile('poster_image')) {
            if ($promo->poster_image) {
                Storage::disk('public')->delete($promo->poster_image);
            }

            $data['poster_image'] = $request->file('poster_image')->store('promos', 'public');
        }

        $data['is_premium'] = $request->boolean('is_premium');

        $promo->update($data);

        return redirect()
            ->route('seller.promos.index')
            ->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroy(Promo $promo): RedirectResponse
    {
        abort_if($promo->seller_id !== auth()->user()->sellerProfile->id, 403);

        if ($promo->poster_image) {
            Storage::disk('public')->delete($promo->poster_image);
        }

        $promo->delete();

        return redirect()
            ->route('seller.promos.index')
            ->with('success', 'Promo berhasil dihapus.');
    }
}