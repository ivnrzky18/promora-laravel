@extends('layouts.app')

@section('title', 'Hot Deals 🔥 - Promora')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-2">
            <span class="text-4xl" aria-hidden="true">🔥</span>
            <h1 class="text-3xl font-bold text-gray-800">Hot Deals</h1>
        </div>
        <p class="text-gray-500 text-sm mt-1">
            Promo-promo terbaik yang akan segera berakhir — jangan sampai ketinggalan!
        </p>
        <div class="mt-3 inline-flex items-center px-3 py-1.5 rounded-full bg-orange-100 text-orange-700 text-xs font-semibold">
            ⏰ Berakhir dalam 48 jam ke depan
        </div>
    </div>

    {{-- Hot Deals Grid --}}
    @if($hotDeals->count() > 0)

        {{-- Results Count --}}
        <p class="text-sm text-gray-500 mb-5">
            Menampilkan <span class="font-semibold text-gray-700">{{ $hotDeals->count() }}</span> hot deal aktif
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($hotDeals as $promo)
                @php
                    $isBookmarked = auth()->check()
                        ? $promo->bookmarks()->where('user_id', auth()->id())->exists()
                        : false;
                @endphp

                <div class="bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col">

                    {{-- Poster Image --}}
                    <div class="relative aspect-video bg-gray-100 overflow-hidden">
                        @if($promo->poster_image)
                            <img src="{{ asset('storage/' . $promo->poster_image) }}"
                                 alt="{{ $promo->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-orange-100 to-orange-200">
                                <svg class="w-12 h-12 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Discount Badge --}}
                        @if($promo->discount_percentage)
                            <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ number_format($promo->discount_percentage, 0) }}% OFF
                            </div>
                        @endif

                        {{-- "Berakhir Segera" Badge (orange) --}}
                        <div class="absolute top-2 right-2 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                            🔥 Berakhir Segera
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-4 flex flex-col flex-1">

                        {{-- Promo Title --}}
                        <h2 class="font-semibold text-gray-800 text-sm leading-snug line-clamp-2 mb-2">
                            {{ $promo->title }}
                        </h2>

                        {{-- Discount Percentage --}}
                        @if($promo->discount_percentage)
                            <p class="text-lg font-bold text-red-500 mb-1">
                                Diskon {{ number_format($promo->discount_percentage, 0) }}%
                            </p>
                        @endif

                        {{-- Seller Name --}}
                        <p class="text-xs text-gray-500 mb-3 truncate flex items-center">
                            <svg class="w-3 h-3 mr-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ $promo->seller->business_name ?? 'Seller' }}
                        </p>

                        {{-- Spacer --}}
                        <div class="flex-1"></div>

                        {{-- Countdown Timer --}}
                        <div class="mb-3">
                            <x-countdown :endDate="$promo->end_date" />
                        </div>

                        {{-- Footer: Bookmark + View Detail --}}
                        <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                            @auth
                                <div x-data="{
                                        bookmarked: {{ $isBookmarked ? 'true' : 'false' }},
                                        count: {{ $promo->bookmarks_count ?? $promo->bookmarks()->count() }},
                                        loading: false,
                                        toggle() {
                                            if (this.loading) return;
                                            this.loading = true;
                                            fetch('{{ route('consumer.bookmarks.toggle', $promo) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                    'Accept': 'application/json',
                                                    'Content-Type': 'application/json'
                                                }
                                            })
                                            .then(r => r.json())
                                            .then(data => {
                                                this.bookmarked = data.bookmarked;
                                                this.count = data.count;
                                            })
                                            .catch(() => {})
                                            .finally(() => { this.loading = false; });
                                        }
                                    }" class="flex items-center">
                                    <button @click="toggle()"
                                            :disabled="loading"
                                            :aria-label="bookmarked ? 'Hapus bookmark' : 'Tambah bookmark'"
                                            class="flex items-center space-x-1 text-xs hover:text-orange-500 transition-colors min-h-[44px] px-2 py-1 rounded-lg hover:bg-orange-50"
                                            :class="bookmarked ? 'text-orange-500' : 'text-gray-400'">
                                        <svg class="w-4 h-4 transition-colors"
                                             :class="bookmarked ? 'fill-orange-500 stroke-orange-500' : 'fill-none stroke-current'"
                                             stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                        </svg>
                                        <span x-text="count" class="font-medium"></span>
                                    </button>
                                </div>
                            @else
                                <a href="{{ route('consumer.login') }}"
                                   aria-label="Masuk untuk bookmark promo ini"
                                   class="flex items-center space-x-1 text-xs text-gray-400 hover:text-orange-500 transition-colors min-h-[44px] px-2 py-1 rounded-lg hover:bg-orange-50">
                                    <svg class="w-4 h-4 fill-none stroke-current" stroke="currentColor"
                                         viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                    <span>{{ $promo->bookmarks_count ?? $promo->bookmarks()->count() }}</span>
                                </a>
                            @endauth

                            <a href="{{ route('promos.show', $promo) }}"
                               class="text-xs text-orange-500 hover:text-orange-600 font-medium min-h-[44px] flex items-center px-2 transition-colors">
                                Lihat Detail →
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="text-6xl mb-4" aria-hidden="true">🔥</div>
            <h2 class="text-xl font-semibold text-gray-600 mb-2">Belum ada Hot Deals saat ini</h2>
            <p class="text-gray-400 text-sm mb-6">
                Hot Deals adalah promo yang akan berakhir dalam 48 jam ke depan.<br>
                Pantau terus halaman ini untuk penawaran terbaik!
            </p>
            <a href="{{ route('explore') }}"
               class="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white font-medium px-6 py-3 rounded-lg text-sm transition-colors min-h-[44px]">
                Jelajahi Semua Promo
            </a>
        </div>
    @endif

</div>
@endsection
