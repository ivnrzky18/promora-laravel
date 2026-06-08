<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SellerResource;
use App\Models\SellerProfile;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SellerController extends Controller
{
    /**
     * Return all verified sellers.
     */
    public function index(): AnonymousResourceCollection
    {
        return SellerResource::collection(
            SellerProfile::where('is_verified', true)->get()
        );
    }
}
