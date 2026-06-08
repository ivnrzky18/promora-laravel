@extends('layouts.seller')

@section('title', 'Dashboard Penjual - Promora')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-500 mt-1">Selamat datang kembali, {{ auth()->user()->name }}!</p>
    </div>

    {{-- Profile Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Logo --}}
            <div class="flex-shrink-0">
                @if($sellerProfile->logo)
                    <img src="{{ asset('storage/' . $sellerProfile->logo) }}"
                         alt="{{ $sellerProfile->business_name }}"
                         class="w-16 h-16 rounded-full object-cover border-2 border-orange-100">
                @else
                    <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center border-2 border-orange-200">
                        <span class="text-2xl font-bold text-orange-500">
                            {{ strtoupper(substr($sellerProfile->business_name, 0, 1)) }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h2 class="text-lg font-bold text-gray-800 truncate">{{ $sellerProfile->business_name }}</h2>
                    {{-- Verification Badge --}}
                    @if($sellerProfile->is_verified)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            ✅ Terverifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            ⏳ Menunggu Verifikasi
                        </span>
                    @endif
                </div>
                {{-- Category Badge --}}
                <span class="inline-block bg-orange-50 text-orange-600 text-xs font-medium px-2.5 py-0.5 rounded-full mb-1">
                    {{ $sellerProfile->business_category }}
                </span>
                <p class="text-sm text-gray-400 truncate">
                    <svg class="w-3.5 h-3.5 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $sellerProfile->address }}
                </p>
            </div>

            {{-- Edit Profile Link --}}
            <a href="{{ route('seller.profile') }}"
               class="flex-shrink-0 text-sm text-orange-500 hover:text-orange-600 font-medium min-h-[44px] flex items-center">
                Edit Profil
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        {{-- Total Promo --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Promo</p>
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $totalPromos }}</p>
        </div>

        {{-- Total Views --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Views</p>
                <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalViews) }}</p>
        </div>

        {{-- Promo Aktif --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Promo Aktif</p>
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $activePromos }}</p>
        </div>

        {{-- Pelanggan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</p>
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $subscriberCount }}</p>
        </div>

        {{-- Rating --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Rating</p>
                <div class="w-8 h-8 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($averageRating, 1) }}</p>
        </div>
    </div>

    {{-- Quick Action Buttons --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('seller.promos.create') }}"
           class="inline-flex items-center justify-center px-5 py-3 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors min-h-[44px] shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Upload Promo
        </a>
        <a href="{{ route('seller.events.create') }}"
           class="inline-flex items-center justify-center px-5 py-3 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold rounded-xl transition-colors min-h-[44px] shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Upload Event
        </a>
    </div>

    {{-- Promo List Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 sm:px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-800">Daftar Promo</h3>
            <a href="{{ route('seller.promos.index') }}"
               class="inline-flex items-center justify-center px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors min-h-[44px] w-full sm:w-auto">
                Lihat Semua Promo
            </a>
        </div>

        @if($promos->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <p class="text-gray-500 text-sm">Belum ada promo. Buat promo pertama Anda!</p>
                <a href="{{ route('seller.promos.create') }}"
                   class="mt-4 inline-flex items-center px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors min-h-[44px]">
                    Upload Promo Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Promo</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Periode</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Views</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($promos as $promo)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Thumbnail + Title --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($promo->poster_image)
                                        <img src="{{ asset('storage/' . $promo->poster_image) }}"
                                             alt="{{ $promo->title }}"
                                             class="w-10 h-10 rounded-lg object-cover flex-shrink-0 border border-gray-100">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0 border border-gray-100">
                                            <svg class="w-5 h-5 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 truncate max-w-[160px] sm:max-w-xs">{{ $promo->title }}</p>
                                        <p class="text-xs text-gray-400">{{ $promo->category->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-4 py-3">
                                @if($promo->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Aktif
                                    </span>
                                @elseif($promo->status === 'draft')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        Draft
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                        Kadaluarsa
                                    </span>
                                @endif
                            </td>

                            {{-- Date Range --}}
                            <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">
                                <span class="text-xs">
                                    {{ $promo->start_date->format('d M Y') }}
                                    <span class="text-gray-300 mx-1">→</span>
                                    {{ $promo->end_date->format('d M Y') }}
                                </span>
                            </td>

                            {{-- View Count --}}
                            <td class="px-4 py-3 text-gray-500 hidden md:table-cell">
                                <div class="flex items-center gap-1 text-xs">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ number_format($promo->view_count) }}
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('seller.promos.edit', $promo) }}"
                                       class="inline-flex items-center px-3 py-2 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors min-h-[44px]">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('seller.promos.destroy', $promo) }}"
                                          onsubmit="return confirm('Hapus promo ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-2 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors min-h-[44px]">
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
@endsection
