@extends('layouts.seller')

@section('title', 'Profil Bisnis - Promora')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Profil Bisnis</h1>
        <p class="text-gray-500 mt-1">Kelola informasi bisnis Anda yang tampil ke publik</p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data"
          x-data="{ logoPreview: null }">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-100">

            {{-- Logo --}}
            <div class="p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-700">Logo Bisnis</h2>

                <div class="flex items-start space-x-4">
                    {{-- Current / Preview Logo --}}
                    <div class="flex-shrink-0">
                        <div x-show="!logoPreview">
                            @if($sellerProfile->logo)
                                <img src="{{ asset('storage/' . $sellerProfile->logo) }}"
                                     alt="{{ $sellerProfile->business_name }}"
                                     class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                            @else
                                <div class="w-20 h-20 rounded-full bg-orange-100 flex items-center justify-center border-2 border-orange-200">
                                    <span class="text-3xl font-bold text-orange-500">
                                        {{ strtoupper(substr($sellerProfile->business_name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div x-show="logoPreview">
                            <img :src="logoPreview" alt="Preview"
                                 class="w-20 h-20 rounded-full object-cover border-2 border-orange-200">
                        </div>
                    </div>

                    {{-- Upload --}}
                    <div class="flex-1">
                        <label class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer transition-colors min-h-[44px]">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Ganti Logo
                            <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" class="hidden"
                                   @change="logoPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                        </label>
                        <p class="text-xs text-gray-400 mt-1.5">JPEG, PNG, WebP — Maks. 2MB</p>
                        @error('logo')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Business Info --}}
            <div class="p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-700">Informasi Bisnis</h2>

                {{-- Business Name --}}
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Bisnis <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="business_name" name="business_name"
                           value="{{ old('business_name', $sellerProfile->business_name) }}"
                           class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('business_name') border-red-400 @enderror">
                    @error('business_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Business Category --}}
                <div>
                    <label for="business_category" class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori Bisnis <span class="text-red-500">*</span>
                    </label>
                    <select id="business_category" name="business_category"
                            class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('business_category') border-red-400 @enderror">
                        @foreach(['Kuliner', 'Fashion', 'Jasa', 'Kesehatan', 'Pendidikan', 'Hiburan'] as $cat)
                            <option value="{{ $cat }}" {{ old('business_category', $sellerProfile->business_category) === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                    @error('business_category')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Bisnis</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('description') border-red-400 @enderror"
                              placeholder="Ceritakan tentang bisnis Anda...">{{ old('description', $sellerProfile->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Location --}}
            <div class="p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-700">Lokasi</h2>

                {{-- Address --}}
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat <span class="text-red-500">*</span>
                    </label>
                    <textarea id="address" name="address" rows="2"
                              class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('address') border-red-400 @enderror"
                              placeholder="Alamat lengkap bisnis Anda">{{ old('address', $sellerProfile->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Coordinates --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="number" id="latitude" name="latitude" step="any"
                               value="{{ old('latitude', $sellerProfile->latitude) }}"
                               class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('latitude') border-red-400 @enderror"
                               placeholder="-6.2088">
                        @error('latitude')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="number" id="longitude" name="longitude" step="any"
                               value="{{ old('longitude', $sellerProfile->longitude) }}"
                               class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('longitude') border-red-400 @enderror"
                               placeholder="106.8456">
                        @error('longitude')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <p class="text-xs text-gray-400">Koordinat digunakan untuk fitur pencarian berdasarkan lokasi.</p>
            </div>

            {{-- Submit --}}
            <div class="p-6 flex items-center justify-end space-x-3">
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition-colors min-h-[44px]">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
