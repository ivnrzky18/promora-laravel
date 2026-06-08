<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'seller';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'poster_image'        => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'original_price'      => 'nullable|numeric|min:0',
            'promo_price'         => 'nullable|numeric|min:0',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after_or_equal:start_date',
            'category_id'         => 'required|exists:categories,id',
        ];
    }
}
