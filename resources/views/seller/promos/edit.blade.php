@extends('layouts.seller')

@section('title', 'Edit Promo - Promora')

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
                <h1 class="text-2xl font-bold text-black">Edit Promo</h1>
                <p class="text-gray-500 mt-0.5 text-sm">Perbarui informasi promo Anda dengan data terbaru</p>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('seller.promos.update', $promo) }}" enctype="multipart/form-data"
              x-data="{ imagePreview: null, hasExistingImage: {{ $promo->poster_image ? 'true' : 'false' }} }">
            @csrf
            @method('PUT')

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
                        <input type="text" id="title" name="title" value="{{ old('title', $promo->title) }}"
                               class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('title') border-[#F30000] ring-1 ring-[#F30000] @enderror">
                        @error('title')
                            <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-semibold text-black mb-1">Deskripsi</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('description') border-[#F30000] ring-1 ring-[#F30000] @enderror">{{ old('description', $promo->description) }}</textarea>
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
                                <option value="{{ $category->id }}" {{ old('category_id', $promo->category_id) == $category->id ? 'selected' : '' }}>
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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Current Poster --}}
                        @if($promo->poster_image)
                            <div x-show="hasExistingImage && !imagePreview" class="space-y-1">
                                <p class="text-xs font-semibold text-gray-500">Poster Saat Ini:</p>
                                <div class="relative rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                                    <img src="{{ asset('storage/' . $promo->poster_image) }}"
                                         alt="{{ $promo->title }}"
                                         class="w-full h-40 object-cover">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                        <span class="text-white text-xs font-bold px-2 py-1 bg-black/50 rounded">Aktif</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- New Image Preview --}}
                        <div x-show="imagePreview" class="space-y-1" x-cloak>
                            <p class="text-xs font-semibold text-[#DD3015]">Pratinjau Poster Baru:</p>
                            <div class="relative rounded-lg overflow-hidden border border-[#DD3015] shadow-sm group">
                                <img :src="imagePreview" alt="Preview" class="w-full h-40 object-cover">
                                <div class="absolute inset-0 bg-[#DD3015]/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <p class="text-white text-xs font-bold">Siap Diperbarui</p>
                                </div>
                            </div>
                        </div>

                        {{-- Uploader / Dropzone Area --}}
                        <div class="flex flex-col justify-end">
                            <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-[#DD3015] hover:bg-red-50/50 transition-all duration-200">
                                <div class="flex flex-col items-center justify-center pt-4 pb-4 px-2 text-center">
                                    <svg class="w-8 h-8 text-[#DD3015] mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-xs text-black font-bold">{{ $promo->poster_image ? 'Klik untuk mengganti poster' : 'Klik untuk upload gambar' }}</p>
                                    <p class="text-[11px] text-gray-500 mt-1">JPEG, PNG, WebP (Maks. 2MB)</p>
                                </div>
                                <input type="file" name="poster_image" accept="image/jpeg,image/png,image/webp" class="hidden"
                                       @change="imagePreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                            </label>

                            <button type="button" x-show="imagePreview" @click="imagePreview = null; $el.closest('.p-6').querySelector('input[type=file]').value = ''"
                                    class="mt-2 text-xs font-semibold text-[#F30000] hover:underline min-h-[36px] flex items-center justify-start" x-cloak>
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-16v1a3 3 0 003 3h10M9 7h6"/>
                                </svg>
                                Batalkan perubahan gambar
                            </button>
                        </div>
                    </div>
                    @error('poster_image')
                        <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pricing --}}
                <div class="p-6 space-y-4">
                    <div class="flex items-center space-x-2 pb-2 border-b border-gray-100">
                        <div class="w-2 h-5 bg-[#DD3015] rounded-full"></div>
                        <h2 class="text-base font-bold text-black">Harga & Diskon</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- Discount --}}
                        <div class="bg-red-50/40 p-3 rounded-lg border border-red-100">
                            <label for="discount_percentage" class="block text-sm font-semibold text-black mb-1">Diskon (%)</label>
                            <input type="number" id="discount_percentage" name="discount_percentage"
                                   value="{{ old('discount_percentage', $promo->discount_percentage) }}" min="0" max="100" step="0.01"
                                   class="w-full px-3 py-2.5 min-h-[44px] bg-white border border-gray-300 rounded-lg text-sm font-bold text-[#F30000] focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('discount_percentage') border-[#F30000] @enderror">
                            @error('discount_percentage')
                                <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Original Price --}}
                        <div>
                            <label for="original_price" class="block text-sm font-semibold text-black mb-1">Harga Asli (Rp)</label>
                            <input type="number" id="original_price" name="original_price"
                                   value="{{ old('original_price', $promo->original_price) }}" min="0" step="0.01"
                                   class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('original_price') border-[#F30000] @enderror">
                            @error('original_price')
                                <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Promo Price --}}
                        <div class="bg-amber-50/40 p-3 rounded-lg border border-amber-100">
                            <label for="promo_price" class="block text-sm font-semibold text-black mb-1">Harga Promo (Rp)</label>
                            <input type="number" id="promo_price" name="promo_price"
                                   value="{{ old('promo_price', $promo->promo_price) }}" min="0" step="0.01"
                                   class="w-full px-3 py-2.5 min-h-[44px] bg-white border border-gray-300 rounded-lg text-sm font-bold text-[#DD3015] focus:outline-none focus:ring-2 focus:ring-[#FFB800] focus:border-transparent @error('promo_price') border-[#F30000] @enderror">
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
                            <input type="date" id="start_date" name="start_date"
                                   value="{{ old('start_date', $promo->start_date->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('start_date') border-[#F30000] @enderror">
                            @error('start_date')
                                <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-semibold text-black mb-1">
                                Tanggal Berakhir <span class="text-[#F30000]">*</span>
                            </label>
                            <input type="date" id="end_date" name="end_date"
                                   value="{{ old('end_date', $promo->end_date->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('end_date') border-[#F30000] @enderror">
                            @error('end_date')
                                <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Submit Actions --}}
                <div class="p-6 bg-gray-50 flex items-center justify-end space-x-3 rounded-b-xl">
                    <a href="{{ route('seller.promos.index') }}"
                       class="px-5 py-2.5 text-sm font-bold text-black bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors min-h-[44px] flex items-center shadow-sm">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 text-sm font-bold text-white bg-[#DD3015] rounded-lg hover:bg-[#F30000] focus:ring-4 focus:ring-[#FFB800]/50 transition-all duration-150 min-h-[44px] shadow-md hover:shadow-lg">
                        Perbarui Promo
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection