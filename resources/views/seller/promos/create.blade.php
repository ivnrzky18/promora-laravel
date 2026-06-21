@extends('layouts.seller')

@section('title', 'Tambah Promo - Promora')

@section('content')
<div class="min-h-screen p-4 sm:p-6" style="background-color: #F3E1E1;">
    <div class="max-w-3xl mx-auto space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center space-x-3 bg-white p-4 rounded-xl shadow-sm border border-red-100">
            <a href="{{ route('seller.promos.index') }}"
               class="text-gray-400 hover:text-[#DD3015] transition-colors min-h-[44px] flex items-center p-2 rounded-lg hover:bg-red-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-black">Tambah Promo</h1>
                <p class="text-gray-500 mt-0.5 text-sm">Buat promo baru yang menarik untuk bisnis Anda</p>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('seller.promos.store') }}" enctype="multipart/form-data"
              x-data="{ imagePreview: null, isPremium: {{ old('is_premium') ? 'true' : 'false' }} }">
            @csrf

            <div class="bg-white rounded-xl shadow-md border border-red-100 overflow-hidden divide-y divide-gray-100">

                {{-- Basic Info --}}
                <div class="p-6 space-y-4">
                    <div class="flex items-center space-x-2 pb-2 border-b border-gray-100">
                        <div class="w-2 h-5 bg-[#DD3015] rounded-full"></div>
                        <h2 class="text-base font-bold text-black">Informasi Dasar</h2>
                    </div>

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-semibold text-black mb-1">
                            Judul Promo <span class="text-[#F30000]">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}"
                               class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('title') border-[#F30000] ring-1 ring-[#F30000] @enderror"
                               placeholder="Contoh: Diskon 50% Semua Menu">
                        @error('title')
                            <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-semibold text-black mb-1">Deskripsi</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('description') border-[#F30000] ring-1 ring-[#F30000] @enderror"
                                  placeholder="Jelaskan detail promo Anda secara rinci...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-black mb-1">
                            Kategori <span class="text-[#F30000]">*</span>
                        </label>
                        <select id="category_id" name="category_id"
                                class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('category_id') border-[#F30000] ring-1 ring-[#F30000] @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Poster Image --}}
                <div class="p-6 space-y-4">
                    <div class="flex items-center space-x-2 pb-2 border-b border-gray-100">
                        <div class="w-2 h-5 bg-[#DD3015] rounded-full"></div>
                        <h2 class="text-base font-bold text-black">Poster Promo</h2>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-black mb-2">Gambar Poster</label>

                        <div x-show="imagePreview" class="mb-3 relative max-w-sm rounded-lg overflow-hidden group">
                            <img :src="imagePreview" alt="Preview" class="w-full h-48 object-cover border border-gray-200 rounded-lg">
                            <div class="absolute inset-0 bg-[#DD3015]/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <p class="text-white text-sm font-medium">Gambar Terpilih</p>
                            </div>
                        </div>

                        <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-[#DD3015] hover:bg-red-50/50 transition-all duration-200"
                               x-show="!imagePreview">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 text-[#DD3015] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm text-black font-medium">Klik untuk upload gambar</p>
                                <p class="text-xs text-gray-500 mt-1">JPEG, PNG, WebP — Maks. 2MB</p>
                            </div>
                            <input type="file" name="poster_image" accept="image/jpeg,image/png,image/webp" class="hidden"
                                   @change="imagePreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                        </label>

                        @error('poster_image')
                            <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Premium Listing --}}
                <div class="p-6 space-y-4">
                    <div class="flex items-center space-x-2 pb-2 border-b border-yellow-100">
                        <div class="w-2 h-5 bg-[#FFB800] rounded-full"></div>
                        <h2 class="text-base font-bold text-black">Premium Listing</h2>
                    </div>

                    <label class="flex items-start gap-3 p-4 border border-yellow-300 bg-yellow-50 rounded-2xl cursor-pointer hover:bg-yellow-100/60 transition-colors">
                        <input type="checkbox"
                               name="is_premium"
                               value="1"
                               x-model="isPremium"
                               class="mt-1 w-5 h-5 rounded border-gray-300 text-[#FFB800] focus:ring-[#FFB800]">

                        <div class="flex-1">
                            <p class="font-bold text-black flex items-center gap-2 flex-wrap">
                                Aktifkan Premium Listing
                                <span class="px-2 py-1 text-xs bg-yellow-400 text-white rounded-full">
                                    ⭐ Premium
                                </span>
                            </p>

                            <p class="text-sm text-gray-600 mt-1">
                                Promo akan diprioritaskan tampil di halaman consumer
                            </p>
                        </div>
                    </label>
                </div>

                {{-- Pricing --}}
                <div class="p-6 space-y-4">
                    <div class="flex items-center space-x-2 pb-2 border-b border-gray-100">
                        <div class="w-2 h-5 bg-[#DD3015] rounded-full"></div>
                        <h2 class="text-base font-bold text-black">Harga & Diskon</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="discount_percentage" class="block text-sm font-semibold text-black mb-1">Diskon (%)</label>
                            <input type="number" id="discount_percentage" name="discount_percentage"
                                   value="{{ old('discount_percentage') }}" min="0" max="100" step="0.01"
                                   class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm">
                            @error('discount_percentage')
                                <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="original_price" class="block text-sm font-semibold text-black mb-1">Harga Asli (Rp)</label>
                            <input type="number" id="original_price" name="original_price"
                                   value="{{ old('original_price') }}" min="0" step="0.01"
                                   class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm">
                            @error('original_price')
                                <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="promo_price" class="block text-sm font-semibold text-black mb-1">Harga Promo (Rp)</label>
                            <input type="number" id="promo_price" name="promo_price"
                                   value="{{ old('promo_price') }}" min="0" step="0.01"
                                   class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm">
                            @error('promo_price')
                                <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Dates --}}
                <div class="p-6 space-y-4">
                    <div class="flex items-center space-x-2 pb-2 border-b border-gray-100">
                        <div class="w-2 h-5 bg-[#DD3015] rounded-full"></div>
                        <h2 class="text-base font-bold text-black">Periode Promo</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-semibold text-black mb-1">
                                Tanggal Mulai <span class="text-[#F30000]">*</span>
                            </label>
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}"
                                   class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm">
                            @error('start_date')
                                <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-semibold text-black mb-1">
                                Tanggal Berakhir <span class="text-[#F30000]">*</span>
                            </label>
                            <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}"
                                   class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm">
                            @error('end_date')
                                <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="p-6 bg-gray-50 flex items-center justify-end space-x-3 rounded-b-xl">
                    <a href="{{ route('seller.promos.index') }}"
                       class="px-5 py-2.5 text-sm font-bold text-black bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px] flex items-center shadow-sm">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 text-sm font-bold text-white bg-[#DD3015] rounded-lg hover:bg-[#F30000] transition-all duration-150 min-h-[44px] shadow-md hover:shadow-lg">
                        Simpan Promo
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection