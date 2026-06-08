<?php

namespace App\Http\Controllers;

use App\Models\SellerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class SellerController extends Controller
{
    /**
     * Show the seller dashboard with statistics.
     */
    public function dashboard(): View
    {
        $sellerProfile = auth()->user()->sellerProfile()->with('subscribers')->first();

        $totalPromos     = $sellerProfile->promos()->count();
        $totalViews      = $sellerProfile->promos()->sum('view_count');
        $activePromos    = $sellerProfile->promos()->where('status', 'active')->count();
        $subscriberCount = $sellerProfile->subscribers()->count();
        $averageRating   = $sellerProfile->averageRating();

        $promos = $sellerProfile->promos()
            ->with('category')
            ->latest()
            ->get();

        return view('seller.dashboard', compact(
            'sellerProfile',
            'totalPromos',
            'totalViews',
            'activePromos',
            'subscriberCount',
            'averageRating',
            'promos'
        ));
    }

    /**
     * Show the seller profile edit page.
     */
    public function profile(): View
    {
        $sellerProfile = auth()->user()->sellerProfile;

        return view('seller.profile', compact('sellerProfile'));
    }

    /**
     * Update the seller profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'business_name'     => 'required|string|max:255',
            'business_category' => 'required|string|max:100',
            'description'       => 'nullable|string',
            'address'           => 'required|string|max:500',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'logo'              => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $sellerProfile = auth()->user()->sellerProfile;

        $data = $request->only([
            'business_name',
            'business_category',
            'description',
            'address',
            'latitude',
            'longitude',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($sellerProfile->logo) {
                Storage::disk('public')->delete($sellerProfile->logo);
            }

            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $sellerProfile->update($data);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
}
