<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'description'         => $this->description,
            'discount_percentage' => $this->discount_percentage,
            'promo_price'         => $this->promo_price,
            'start_date'          => $this->start_date?->toDateString(),
            'end_date'            => $this->end_date?->toDateString(),
            'poster_image'        => $this->poster_image ? asset('storage/' . $this->poster_image) : null,
            'seller'              => ['name' => $this->seller?->business_name],
            'category'            => ['name' => $this->category?->name],
        ];
    }
}
