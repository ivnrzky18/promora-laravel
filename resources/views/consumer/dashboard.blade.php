@extends('layouts.consumer')

@section('title', 'Dashboard - Promora')

@section('content')

{{-- Welcome Greeting --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Halo, {{ $user->name }}! 👋
    </h1>
    <p class="text-gray-500 mt-1 text-sm">Temukan promo terbaik dari seller favoritmu.</p>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center space-x-4">
        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $bookmarkCount }}</p>
            <p class="text-xs text-gray-500 font-medium">Bookmark Tersimpan</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center space-x-4">
        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $subscriptionCount }}</p>
            <p class="text-xs text-gray-500 font-medium">Subscription Aktif</p>
        </div>
    </div>
</div>

{{-- Feed Promo Section --}}
<section class="mb-10">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-800">Feed Promo</h2>
        <a href="{{ url('/explore') }}" class="text-sm text-orange-500 hover:text-orange-600 font-medium min-h-[44px] flex items-center px-1">
            Lihat Semua →
        </a>
    </div>

    @if($promoFeed->isEmpty() && $subscriptionCount == 0)
        {{-- Empty state: user has no subscriptions yet --}}
        <div class="bg-white rounded-xl border border-dashed border-gray-200 p-10 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-gray-600 font-semibold mb-1">Mulai ikuti UMKM favoritmu!</p>
            <p class="text-gray-400 text-sm mb-4">Subscribe ke seller untuk melihat promo terbaru mereka di sini.</p>
            <a href="{{ url('/explore') }}"
               class="inline-flex items-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors min-h-[44px]">
                Jelajahi Seller
            </a>
        </div>
    @elseif($promoFeed->isEmpty())
        {{-- Has subscriptions but no active promos yet --}}
        <div class="bg-white rounded-xl border border-dashed border-gray-200 p-10 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-500 font-medium mb-1">Belum ada promo dari seller yang kamu ikuti</p>
            <p class="text-gray-400 text-sm mb-4">Seller yang kamu ikuti belum memiliki promo aktif saat ini.</p>
            <a href="{{ url('/explore') }}"
               class="inline-flex items-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors min-h-[44px]">
                Jelajahi Seller
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($promoFeed as $promo)
                <x-promo-card :promo="$promo" />
            @endforeach
        </div>
    @endif
</section>

{{-- Hot Deals Section --}}
<section>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-800">🔥 Hot Deals</h2>
        <a href="{{ url('/hot-deals') }}" class="text-sm text-orange-500 hover:text-orange-600 font-medium min-h-[44px] flex items-center px-1">
            Lihat Semua →
        </a>
    </div>

    @if($hotDeals->isEmpty())
        <div class="bg-white rounded-xl border border-dashed border-gray-200 p-8 text-center">
            <p class="text-gray-400 text-sm">Tidak ada hot deals saat ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($hotDeals as $promo)
                <div class="relative">
                    <x-promo-card :promo="$promo" />
                    {{-- Countdown overlay at bottom of card --}}
                    <div class="px-4 pb-3 -mt-2">
                        <x-countdown :end_date="$promo->end_date" />
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

@endsection
