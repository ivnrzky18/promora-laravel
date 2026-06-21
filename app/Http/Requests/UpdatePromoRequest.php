<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'category_id'         => ['required', 'exists:categories,id'],
            'poster_image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'original_price'      => ['nullable', 'numeric', 'min:0'],
            'promo_price'         => ['nullable', 'numeric', 'min:0'],
            'start_date'          => ['required', 'date'],
            'end_date'            => ['required', 'date', 'after_or_equal:start_date'],
            'is_premium'          => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'poster_image.max' => 'Ukuran poster promo maksimal 5MB.',
        ];
    }
}