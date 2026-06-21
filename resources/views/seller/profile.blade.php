@extends('layouts.seller')

@section('title', 'Profil Bisnis - Promora')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 py-6 px-4 sm:px-0">

    {{-- Page Header --}}
    <div class="border-b border-[#DD3015]/10 pb-4">
        <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Profil Bisnis</h1>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-1">Kelola informasi bisnis Anda yang tampil ke publik</p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data"
          x-data="{ logoPreview: null }">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y divide-gray-100 overflow-hidden">

            {{-- Logo --}}
            <div class="p-6 space-y-4">
                <h2 class="text-sm font-black text-gray-800 uppercase tracking-wider">Logo Bisnis</h2>

                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
                    {{-- Current / Preview Logo --}}
                    <div class="flex-shrink-0">
                        <div x-show="!logoPreview" class="relative group">
                            @if($sellerProfile->logo)
                                <img src="{{ asset('storage/' . $sellerProfile->logo) }}"
                                     alt="Logo Bisnis"
                                     class="w-24 h-24 rounded-full object-cover border-4 border-[#DD3015]/10 shadow-sm"
                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($sellerProfile->business_name) }}&background=F3E1E1&color=DD3015&size=128&bold=true';">
                            @else
                                <div class="w-24 h-24 rounded-full bg-[#F3E1E1] flex items-center justify-center border-4 border-[#F3E1E1]/50 shadow-inner">
                                    <span class="text-4xl font-black text-[#DD3015]">
                                        {{ strtoupper(substr($sellerProfile->business_name ?? 'P', 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div x-show="logoPreview">
                            <img :src="logoPreview" alt="Preview Logo"
                                 class="w-24 h-24 rounded-full object-cover border-4 border-[#DD3015] shadow-md">
                        </div>
                    </div>

                    {{-- Upload Control --}}
                    <div class="flex-1 text-center sm:text-left space-y-2">
                        <label class="inline-flex items-center px-4 py-2.5 border-2 border-[#DD3015] text-xs font-black text-[#DD3015] uppercase tracking-widest bg-[#F3E1E1] hover:bg-[#F3E1E1]/80 rounded-xl cursor-pointer transition-all duration-200 min-h-[44px]">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Ganti Logo Bisnis
                            <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" class="hidden"
                                   @change="logoPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                        </label>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Format: JPEG, PNG, WebP — Maksimal Ukuran 2MB</p>
                        @error('logo')
                            <p class="mt-1 text-xs font-bold text-red-600 uppercase tracking-wide">⚠️ {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Business Info --}}
            <div class="p-6 space-y-5">
                <h2 class="text-sm font-black text-gray-800 uppercase tracking-wider">Informasi Bisnis</h2>

                {{-- Business Name --}}
                <div class="space-y-1">
                    <label for="business_name" class="block text-xs font-black text-gray-700 uppercase tracking-wide">
                        Nama Bisnis <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="business_name" name="business_name"
                           value="{{ old('business_name', $sellerProfile->business_name) }}"
                           class="w-full px-4 py-3 min-h-[44px] bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:bg-white transition-all @error('business_name') border-red-400 focus:ring-red-500 @enderror"
                           placeholder="Masukkan nama resmi bisnis Anda">
                    @error('business_name')
                        <p class="mt-1 text-xs font-bold text-red-600 uppercase tracking-wide">⚠️ {{ $message }}</p>
                    @enderror
                </div>

                {{-- Business Category --}}
                <div class="space-y-1">
                    <label for="business_category" class="block text-xs font-black text-gray-700 uppercase tracking-wide">
                        Kategori Bisnis <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="business_category" name="business_category"
                                class="w-full px-4 py-3 min-h-[44px] bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:bg-white transition-all appearance-none @error('business_category') border-red-400 focus:ring-red-500 @enderror">
                            @foreach(['Kuliner', 'Fashion', 'Jasa', 'Kesehatan', 'Pendidikan', 'Hiburan'] as $cat)
                                <option value="{{ $cat }}" {{ old('business_category', $sellerProfile->business_category) === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    @error('business_category')
                        <p class="mt-1 text-xs font-bold text-red-600 uppercase tracking-wide">⚠️ {{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="space-y-1">
                    <label for="description" class="block text-xs font-black text-gray-700 uppercase tracking-wide">Deskripsi Bisnis</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-4 py-3 min-h-[44px] bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:bg-white transition-all @error('description') border-red-400 focus:ring-red-500 @enderror"
                              placeholder="Ceritakan tentang bisnis Anda...">{{ old('description', $sellerProfile->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs font-bold text-red-600 uppercase tracking-wide">⚠️ {{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Location --}}
            <div class="p-6 space-y-5">
                <h2 class="text-sm font-black text-gray-800 uppercase tracking-wider">Lokasi & Pemetaan</h2>

                {{-- Address --}}
                <div class="space-y-1">
                    <label for="address" class="block text-xs font-black text-gray-700 uppercase tracking-wide">
                        Alamat Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea id="address" name="address" rows="2"
                              class="w-full px-4 py-3 min-h-[44px] bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:bg-white transition-all @error('address') border-red-400 focus:ring-red-500 @enderror"
                              placeholder="Tulis nama jalan, nomor toko, RT/RW...">{{ old('address', $sellerProfile->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-xs font-bold text-red-600 uppercase tracking-wide">⚠️ {{ $message }}</p>
                    @enderror
                </div>

                {{-- Coordinates --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="latitude" class="block text-xs font-black text-gray-700 uppercase tracking-wide">Latitude</label>
                        <input type="number" id="latitude" name="latitude" step="any"
                               value="{{ old('latitude', $sellerProfile->latitude) }}"
                               class="w-full px-4 py-3 min-h-[44px] bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:bg-white transition-all @error('latitude') border-red-400 focus:ring-red-500 @enderror"
                               placeholder="Contoh: -6.2088">
                        @error('latitude')
                            <p class="mt-1 text-xs font-bold text-red-600 uppercase tracking-wide">⚠️ {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="longitude" class="block text-xs font-black text-gray-700 uppercase tracking-wide">Longitude</label>
                        <input type="number" id="longitude" name="longitude" step="any"
                               value="{{ old('longitude', $sellerProfile->longitude) }}"
                               class="w-full px-4 py-3 min-h-[44px] bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:bg-white transition-all @error('longitude') border-red-400 focus:ring-red-500 @enderror"
                               placeholder="Contoh: 106.8456">
                        @error('longitude')
                            <p class="mt-1 text-xs font-bold text-red-600 uppercase tracking-wide">⚠️ {{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="bg-red-50/50 border border-[#DD3015]/10 rounded-xl p-3.5 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-[#DD3015] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-[11px] text-[#DD3015] font-bold uppercase tracking-wide leading-relaxed">Informasi: Titik koordinat di atas sangat penting agar konsumen dapat melacak jarak lokasi gerai fisik Anda secara presisi melalui peta digital.</p>
                </div>
            </div>

            {{-- Submit Action --}}
            <div class="p-6 bg-gray-50 flex items-center justify-end space-x-3">
                {{-- TOMBOL KEMBALI KE DASHBOARD --}}
                <a href="{{ route('seller.dashboard') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 text-xs font-black text-gray-700 hover:text-black uppercase tracking-widest bg-white hover:bg-gray-100 border border-gray-300 active:scale-95 rounded-xl transition-all min-h-[44px] shadow-sm">
                    Kembali
                </a>

                {{-- TOMBOL SIMPAN --}}
                <button type="submit"
                        class="px-6 py-3 text-xs font-black text-white bg-[#DD3015] hover:bg-[#DD3015]/90 active:scale-95 rounded-xl transition-all uppercase tracking-widest min-h-[44px] shadow-sm shadow-red-900/10">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection