<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PromoResource;
use App\Models\Promo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PromoController extends Controller
{
    /**
     * Return all active promos with seller and category.
     */
    public function index(): AnonymousResourceCollection
    {
        return PromoResource::collection(
            Promo::active()->with(['seller', 'category'])->get()
        );
    }

    /**
     * Return a single active promo by ID.
     */
    public function show(int $id): PromoResource|JsonResponse
    {
        $promo = Promo::active()->find($id);

        if (!$promo) {
            return response()->json(['message' => 'Promo tidak ditemukan'], 404);
        }

        return new PromoResource($promo);
    }
}
