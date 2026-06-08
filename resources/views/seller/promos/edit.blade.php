@extends('layouts.seller')

@section('title', 'Edit Promo - Promora')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center space-x-3">
        <a href="{{ route('seller.promos.index') }}"
           class="text-gray-400 hover:text-gray-600 transition-colors min-h-[44px] flex items-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Promo</h1>
            <p class="text-gray-500 mt-0.5 text-sm">Perbarui informasi promo Anda</p>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('seller.promos.update', $promo) }}" enctype="multipart/form-data"
          x-data="{ imagePreview: null, hasExistingImage: {{ $promo->poster_image ? 'true' : 'false' }} }">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-100">

            {{-- Basic Info --}}
            <div class="p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-700">Informasi Dasar</h2>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Judul Promo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $promo->title) }}"
                           class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('title') border-red-400 @enderror">
                    @error('title')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('description') border-red-400 @enderror">{{ old('description', $promo->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category --}}
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id"
                            class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('category_id') border-red-400 @enderror">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $promo->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Poster Image --}}
            <div class="p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-700">Poster Promo</h2>

                {{-- Current Poster --}}
                @if($promo->poster_image)
                    <div x-show="hasExistingImage && !imagePreview">
                        <p class="text-xs text-gray-500 mb-2">Poster saat ini:</p>
                        <img src="{{ asset('storage/' . $promo->poster_image) }}"
                             alt="{{ $promo->title }}"
                             class="w-full max-w-sm h-48 object-cover rounded-lg border border-gray-200">
                    </div>
                @endif

                {{-- New Image Preview --}}
                <div x-show="imagePreview">
                    <p class="text-xs text-gray-500 mb-2">Poster baru:</p>
                    <img :src="imagePreview" alt="Preview" class="w-full max-w-sm h-48 object-cover rounded-lg border border-gray-200">
                </div>

                <div>
                    <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-orange-400 hover:bg-orange-50 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-4 pb-4">
                            <svg class="w-7 h-7 text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500">{{ $promo->poster_image ? 'Ganti poster' : 'Upload poster' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">JPEG, PNG, WebP — Maks. 2MB</p>
                        </div>
                        <input type="file" name="poster_image" accept="image/jpeg,image/png,image/webp" class="hidden"
                               @change="imagePreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                    </label>

                    @error('poster_image')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Pricing --}}
            <div class="p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-700">Harga & Diskon</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-1">Diskon (%)</label>
                        <input type="number" id="discount_percentage" name="discount_percentage"
                               value="{{ old('discount_percentage', $promo->discount_percentage) }}" min="0" max="100" step="0.01"
                               class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('discount_percentage') border-red-400 @enderror">
                        @error('discount_percentage')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="original_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Asli (Rp)</label>
                        <input type="number" id="original_price" name="original_price"
                               value="{{ old('original_price', $promo->original_price) }}" min="0" step="0.01"
                               class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('original_price') border-red-400 @enderror">
                        @error('original_price')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="promo_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Promo (Rp)</label>
                        <input type="number" id="promo_price" name="promo_price"
                               value="{{ old('promo_price', $promo->promo_price) }}" min="0" step="0.01"
                               class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('promo_price') border-red-400 @enderror">
                        @error('promo_price')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Dates --}}
            <div class="p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-700">Periode Promo</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="start_date" name="start_date"
                               value="{{ old('start_date', $promo->start_date->format('Y-m-d')) }}"
                               class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('start_date') border-red-400 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Berakhir <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="end_date" name="end_date"
                               value="{{ old('end_date', $promo->end_date->format('Y-m-d')) }}"
                               class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('end_date') border-red-400 @enderror">
                        @error('end_date')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="p-6 flex items-center justify-end space-x-3">
                <a href="{{ route('seller.promos.index') }}"
                   class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors min-h-[44px] flex items-center">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition-colors min-h-[44px]">
                    Perbarui Promo
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
