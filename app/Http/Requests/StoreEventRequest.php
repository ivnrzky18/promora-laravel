<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'location'      => ['nullable', 'string', 'max:500'],
            'event_date'    => ['required', 'date'],
            'end_date'      => ['nullable', 'date', 'after_or_equal:event_date'],
            'poster_image'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB
            'is_premium'    => ['nullable', 'boolean'],
            'premium_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'Judul event wajib diisi.',
            'event_date.required'   => 'Tanggal event wajib diisi.',
            'poster_image.image'    => 'Poster event harus berupa gambar.',
            'poster_image.mimes'    => 'Poster event harus berformat JPG, JPEG, PNG, atau WEBP.',
            'poster_image.max'      => 'Ukuran poster event maksimal 5MB.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ];
    }
}