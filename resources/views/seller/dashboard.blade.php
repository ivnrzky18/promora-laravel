@extends('layouts.seller')

@section('title', 'Dashboard Penjual - Promora')

@section('content')
<div class="-m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8 min-h-screen" style="background-color: #F3E1E1;">
    <div class="space-y-6">

        {{-- ================= HERO HEADER (Gradient) ================= --}}
        <div class="relative overflow-hidden rounded-2xl p-5 sm:p-8 shadow-lg" style="background-color: #DD3015;">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="absolute -bottom-16 -right-24 w-56 h-56 bg-white/10 rounded-full"></div>

            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Dashboard Penjual</h1>
                    <p class="text-white/90 mt-1 text-sm sm:text-base">
                        Selamat datang kembali,
                        <span class="font-bold text-[#FFB800]">{{ $sellerProfile->business_name }}</span>!
                    </p>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    {{-- Notification Bell --}}
                    <a href="{{ Route::has('seller.notifications') ? route('seller.notifications') : '#' }}"
                       class="w-11 h-11 flex items-center justify-center bg-white/15 hover:bg-white/25 rounded-xl text-white transition-colors backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </a>

                    {{-- Upload Promo --}}
                    <a href="{{ route('seller.promos.create') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white hover:bg-red-50 text-[#DD3015] text-sm font-bold rounded-xl shadow-md transition-colors min-h-[44px]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Upload Promo
                    </a>

                    {{-- Upload Event --}}
                    <a href="{{ route('seller.events.create') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#FFB800] hover:bg-amber-500 text-black text-sm font-bold rounded-xl shadow-md transition-colors min-h-[44px]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Upload Event
                    </a>
                </div>
            </div>
        </div>

        {{-- ================= PROFILE CARD ================= --}}
        <div class="bg-white rounded-xl shadow-md border border-red-100 p-4 sm:p-6">
            <div class="flex flex-wrap items-center gap-4">
                {{-- Logo --}}
                <div class="flex-shrink-0">
                    @if($sellerProfile->logo)
                        <img src="{{ \Illuminate\Support\Str::startsWith($sellerProfile->logo, 'http') ? $sellerProfile->logo : Storage::url($sellerProfile->logo) }}"
                             alt="{{ $sellerProfile->business_name }}"
                             class="w-16 h-16 rounded-full object-cover border-2 border-red-200 shadow-sm">
                    @else
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#DD3015] to-[#FFB800] flex items-center justify-center border-2 border-white shadow-md">
                            <span class="text-2xl font-bold text-white">
                                {{ strtoupper(substr($sellerProfile->business_name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h2 class="text-lg font-bold text-black truncate">{{ $sellerProfile->business_name }}</h2>

                        @if($sellerProfile->is_verified)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Terverifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Menunggu Verifikasi
                            </span>
                        @endif
                    </div>

                    <span class="inline-block bg-red-50 text-[#DD3015] border border-red-100 text-xs font-bold px-2.5 py-0.5 rounded-full mb-1">
                        {{ $sellerProfile->business_category }}
                    </span>

                    <p class="text-sm text-gray-600 truncate">
                        <svg class="w-3.5 h-3.5 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $sellerProfile->address }}
                    </p>
                </div>

                <a href="{{ route('seller.profile') }}"
                   class="flex-shrink-0 inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 text-black text-sm font-bold rounded-lg bg-white hover:bg-red-50 hover:border-[#DD3015] hover:text-[#DD3015] transition-colors min-h-[44px] shadow-sm">
                    Edit Profil
                </a>
            </div>
        </div>

        {{-- ================= STATISTICS CARDS ================= --}}
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-7 gap-4">

            {{-- Total Promo --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Promo</p>
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-black text-black">{{ $totalPromos }}</p>
            </div>

            {{-- Promo Aktif --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Promo Aktif</p>
                    <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-black text-green-600">{{ $activePromos }}</p>
            </div>

            {{-- Total Event --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Event</p>
                    <div class="w-8 h-8 bg-yellow-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-black text-black">{{ $totalEvents }}</p>
            </div>

            {{-- Event Aktif --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Event Aktif</p>
                    <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-black text-amber-600">{{ $activeEvents }}</p>
            </div>

            {{-- Total Views --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Views</p>
                    <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-black text-black">{{ number_format($totalViews) }}</p>
            </div>

            {{-- Pelanggan --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pelanggan</p>
                    <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#DD3015]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-black text-black">{{ $subscriberCount }}</p>
            </div>

            {{-- Rating --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Avg Rating</p>
                    <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#FFB800]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-black text-black">{{ number_format($averageRating, 1) }}</p>
            </div>
        </div>

        {{-- ================= QUICK ACTION BUTTONS ================= --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('seller.promos.create') }}"
               class="inline-flex items-center justify-center px-5 py-3 bg-[#DD3015] hover:bg-[#F30000] text-white text-sm font-bold rounded-xl transition-all duration-150 min-h-[44px] shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Upload Promo
            </a>

            <a href="{{ route('seller.events.create') }}"
               class="inline-flex items-center justify-center px-5 py-3 bg-[#FFB800] hover:bg-amber-500 text-black text-sm font-bold rounded-xl transition-all duration-150 min-h-[44px] shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Upload Event
            </a>
        </div>

        {{-- ================= PROMO LIST TABLE ================= --}}
        <div class="bg-white rounded-xl shadow-md border border-red-100 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 sm:px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-black">Daftar Promo Terbaru</h3>
                <a href="{{ route('seller.promos.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 text-black text-sm font-bold rounded-lg bg-white hover:bg-red-50 hover:text-[#DD3015] hover:border-[#DD3015] transition-colors min-h-[44px] w-full sm:w-auto shadow-sm">
                    Lihat Semua Promo
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            @if($promos->isEmpty())
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <p class="text-gray-500 text-sm">Belum ada promo. Mulai promosikan produk atau jasa Anda!</p>
                    <a href="{{ route('seller.promos.create') }}"
                       class="mt-4 inline-flex items-center px-5 py-2.5 bg-[#DD3015] hover:bg-[#F30000] text-white text-sm font-bold rounded-lg transition-colors min-h-[44px] shadow-sm">
                        Upload Promo Pertama
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-red-50/50 text-black border-b border-gray-100">
                                <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider">Promo</th>
                                <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider hidden sm:table-cell">Periode</th>
                                <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider hidden md:table-cell">Views</th>
                                <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($promos as $promo)
                            <tr class="hover:bg-red-50/20 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @if($promo->poster_image)
                                            <img src="{{ $promo->poster_url }}"
                                                 alt="{{ $promo->title }}"
                                                 class="w-10 h-10 rounded-lg object-cover flex-shrink-0 border border-gray-200">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0 border border-red-100">
                                                <svg class="w-5 h-5 text-[#DD3015]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-bold text-black truncate max-w-[160px] sm:max-w-xs">{{ $promo->title }}</p>
                                                @if($promo->is_premium ?? false)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                        ⭐ Premium
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 font-medium">{{ $promo->category->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    @if($promo->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                            Aktif
                                        </span>
                                    @elseif($promo->status === 'draft')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                            Draft
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                            Kedaluwarsa
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5 text-gray-600 hidden sm:table-cell">
                                    <span class="text-xs font-medium">
                                        {{ $promo->start_date->format('d M Y') }}
                                        <span class="text-[#DD3015] mx-1">&rarr;</span>
                                        {{ $promo->end_date->format('d M Y') }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 text-gray-600 hidden md:table-cell">
                                    <div class="flex items-center gap-1 text-xs font-medium">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ number_format($promo->view_count) }}
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('seller.promos.edit', $promo) }}"
                                           class="inline-flex items-center px-3 py-2 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100 transition-colors min-h-[44px]">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('seller.promos.destroy', $promo) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus promo ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-2 text-xs font-bold text-[#F30000] bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition-colors min-h-[44px]">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ================= EVENT LIST TABLE ================= --}}
        <div class="bg-white rounded-xl shadow-md border border-yellow-100 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 sm:px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-black">Daftar Event Terbaru</h3>
                <a href="{{ route('seller.events.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 text-black text-sm font-bold rounded-lg bg-white hover:bg-yellow-50 hover:text-amber-700 hover:border-amber-400 transition-colors min-h-[44px] w-full sm:w-auto shadow-sm">
                    Lihat Semua Event
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            @if($events->isEmpty())
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 text-yellow-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-500 text-sm">Belum ada event. Buat event untuk menarik lebih banyak pelanggan.</p>
                    <a href="{{ route('seller.events.create') }}"
                       class="mt-4 inline-flex items-center px-5 py-2.5 bg-[#FFB800] hover:bg-amber-500 text-black text-sm font-bold rounded-lg transition-colors min-h-[44px] shadow-sm">
                        Upload Event Pertama
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-yellow-50/60 text-black border-b border-gray-100">
                                <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider">Event</th>
                                <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider hidden sm:table-cell">Jadwal</th>
                                <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider hidden md:table-cell">Lokasi</th>
                                <th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($events as $event)
                            @php
                                $isEnded = $event->end_date ? $event->end_date->isPast() : $event->event_date->isPast();
                                $isUpcoming = $event->event_date->isFuture() || $event->event_date->isToday();
                            @endphp
                            <tr class="hover:bg-yellow-50/20 transition-colors">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @if($event->poster_image)
                                            <img src="{{ Storage::url($event->poster_image) }}"
                                                 alt="{{ $event->title }}"
                                                 class="w-10 h-10 rounded-lg object-cover flex-shrink-0 border border-gray-200">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0 border border-yellow-100">
                                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="min-w-0">
                                            <p class="font-bold text-black truncate max-w-[160px] sm:max-w-xs">{{ $event->title }}</p>
                                            <p class="text-xs text-gray-500 font-medium">
                                                {{ $event->event_date->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    @if(!$isEnded && $isUpcoming)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                            Akan Datang
                                        </span>
                                    @elseif($isEnded)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                            Berjalan
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5 text-gray-600 hidden sm:table-cell">
                                    <div class="text-xs font-medium">
                                        <div>{{ $event->event_date->format('d M Y, H:i') }}</div>
                                        @if($event->end_date)
                                            <div class="text-gray-400 mt-0.5">s/d {{ $event->end_date->format('d M Y, H:i') }}</div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-gray-600 hidden md:table-cell">
                                    <span class="text-xs font-medium">
                                        {{ $event->location ?: '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('seller.events.edit', $event) }}"
                                           class="inline-flex items-center px-3 py-2 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100 transition-colors min-h-[44px]">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('seller.events.destroy', $event) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-2 text-xs font-bold text-[#F30000] bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition-colors min-h-[44px]">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection