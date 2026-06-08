<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'business_name'     => $this->business_name,
            'business_category' => $this->business_category,
            'description'       => $this->description,
            'address'           => $this->address,
            'logo'              => $this->logo ? asset('storage/' . $this->logo) : null,
            'average_rating'    => $this->averageRating(),
        ];
    }
}
