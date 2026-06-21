@extends('layouts.app')

@section('title', 'Hot Deals 🔥 - Promora')

@section('content')
<div class="min-h-screen bg-[#F3E1E1] py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Page Header --}}
        <div class="mb-10 bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-[#DD3015]/10 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-[#F30000]/5 rounded-full blur-2xl"></div>
            
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative z-10">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="text-4xl filter drop-shadow" aria-hidden="true">🔥</span>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-[#DD3015] tracking-tight">
                            Hot Deals
                        </h1>
                    </div>
                    <p class="text-gray-600 text-sm md:text-base max-w-xl">
                        Promo-promo terbaik yang akan segera berakhir — jangan sampai ketinggalan kesempatan emas ini!
                    </p>
                </div>
                <div>
                    <div class="inline-flex items-center px-4 py-2 rounded-xl bg-[#FFB800]/10 border border-[#FFB800]/30 text-[#black] font-bold text-xs uppercase tracking-wider animate-pulse">
                        <span class="mr-2 text-base">⏰</span> Berakhir dalam 48 jam ke depan
                    </div>
                </div>
            </div>
        </div>

        {{-- Hot Deals Grid --}}
        @if($hotDeals->count() > 0)

            {{-- Results Count --}}
            <div class="flex items-center space-x-2 mb-6 pl-2">
                <span class="w-2 h-2 rounded-full bg-[#F30000]"></span>
                <p class="text-sm font-medium text-gray-700">
                    Menampilkan <span class="font-bold text-[#DD3015]">{{ $hotDeals->count() }}</span> hot deal aktif yang sedang berjalan
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($hotDeals as $promo)
                    @php
                        $isBookmarked = auth()->check()
                            ? $promo->bookmarks()->where('user_id', auth()->id())->exists()
                            : false;
                    @endphp

                    <div class="group bg-white rounded-2xl shadow-md hover:shadow-xl border border-[#DD3015]/10 overflow-hidden transition-all duration-300 transform hover:-translate-y-1 flex flex-col">

                        {{-- Poster Image Container --}}
                        <div class="relative aspect-[16/10] bg-gray-900 overflow-hidden">
                            @if($promo->poster_image)
                                <img src="{{ asset('storage/' . $promo->poster_image) }}"
                                     alt="{{ $promo->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-[#DD3015]/10 to-[#F30000]/20">
                                    <svg class="w-14 h-14 text-[#DD3015]/40 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span class="text-xs font-semibold text-[#DD3015]/60">Promora Deals</span>
                                </div>
                            @endif

                            {{-- OVERLAY STATE (Aplikasi Overlay Warna Utama DD3015 60% saat Hover) --}}
                            <div class="absolute inset-0 bg-[#DD3015]/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center z-10">
                                <a href="{{ route('promos.show', $promo) }}" 
                                   class="bg-white text-[#DD3015] font-bold text-sm px-5 py-2.5 rounded-xl shadow-lg hover:bg-black hover:text-white transition-colors duration-200 transform scale-90 group-hover:scale-100 transition-transform">
                                    Lihat Detail Promo
                                </a>
                            </div>

                            {{-- Discount Badge (Menggunakan Warna Sekunder F30000 - Merah Cerah) --}}
                            @if($promo->discount_percentage)
                                <div class="absolute top-3 left-3 z-20 bg-[#F30000] text-white text-xs font-black px-3 py-1.5 rounded-lg shadow-md tracking-wider">
                                    {{ number_format($promo->discount_percentage, 0) }}% OFF
                                </div>
                            @endif

                            {{-- "Berakhir Segera" Badge (Menggunakan Kuning Emas #FFB800 & Teks Hitam kontras) --}}
                            <div class="absolute top-3 right-3 z-20 bg-[#FFB800] text-black text-[10px] font-extrabold px-2.5 py-1.5 rounded-lg shadow-md uppercase tracking-wider">
                                🔥 Hot Deal
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-5 flex flex-col flex-1 bg-white">

                            {{-- Seller Name --}}
                            <p class="text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-[#DD3015]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ $promo->seller->business_name ?? 'OFFICIAL SELLER' }}
                            </p>

                            {{-- Promo Title --}}
                            <h2 class="font-bold text-gray-900 text-base leading-snug line-clamp-2 mb-3 group-hover:text-[#DD3015] transition-colors">
                                {{ $promo->title }}
                            </h2>

                            {{-- Discount Text --}}
                            @if($promo->discount_percentage)
                                <div class="mb-4 bg-[#F30000]/5 px-3 py-1.5 rounded-lg inline-self-start">
                                    <p class="text-sm font-medium text-gray-600">
                                        Hemat hingga <span class="text-lg font-black text-[#F30000]">{{ number_format($promo->discount_percentage, 0) }}%</span>
                                    </p>
                                </div>
                            @endif

                            {{-- Spacer --}}
                            <div class="flex-1"></div>

                            {{-- Countdown Timer Box --}}
                            <div class="mb-4 p-3 bg-gray-50 border border-gray-100 rounded-xl">
                                <p class="text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1">Sisa Waktu Penawaran:</p>
                                <x-countdown :endDate="$promo->end_date" />
                            </div>

                            {{-- Footer Actions --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-2">
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
                                                class="flex items-center space-x-1.5 text-xs font-semibold transition-all min-h-[40px] px-3 py-1.5 rounded-xl border"
                                                :class="bookmarked ? 'bg-[#DD3015]/10 text-[#DD3015] border-[#DD3015]/20' : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-gray-100'">
                                            <svg class="w-4 h-4 transition-colors duration-200"
                                                 :class="bookmarked ? 'fill-[#DD3015] stroke-[#DD3015]' : 'fill-none stroke-current'"
                                                 stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                            </svg>
                                            <span x-text="count"></span>
                                        </button>
                                    </div>
                                @else
                                    <a href="{{ route('consumer.login') }}"
                                       aria-label="Masuk untuk bookmark promo ini"
                                       class="flex items-center space-x-1.5 text-xs font-semibold bg-gray-50 text-gray-500 border border-gray-200 hover:bg-gray-100 transition-all min-h-[40px] px-3 py-1.5 rounded-xl">
                                        <svg class="w-4 h-4 fill-none stroke-current" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                        </svg>
                                        <span>{{ $promo->bookmarks_count ?? $promo->bookmarks()->count() }}</span>
                                    </a>
                                @endauth

                                <a href="{{ route('promos.show', $promo) }}"
                                   class="text-xs font-bold text-[#DD3015] hover:text-[#F30000] bg-[#DD3015]/5 hover:bg-[#DD3015]/10 min-h-[40px] flex items-center px-4 rounded-xl transition-all duration-200 group-hover:translate-x-1">
                                    Lihat Detail <span class="ml-1 transition-transform group-hover:translate-x-0.5">→</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            {{-- Empty State Terbuka & Bersih --}}
            <div class="text-center py-24 bg-white rounded-2xl shadow-sm border border-[#DD3015]/10 px-4">
                <div class="w-20 h-20 bg-[#F3E1E1] rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-4xl filter drop-shadow" aria-hidden="true">🔥</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Belum ada Hot Deals saat ini</h2>
                <p class="text-gray-500 text-sm max-w-md mx-auto mb-8 leading-relaxed">
                    Hot Deals adalah promo eksklusif yang akan berakhir dalam 48 jam ke depan.<br>
                    Pantau terus halaman ini untuk mendapatkan penawaran terbaik dari Promora!
                </p>
                <a href="{{ route('explore') }}"
                   class="inline-flex items-center bg-[#DD3015] hover:bg-black text-white font-bold px-8 py-3.5 rounded-xl text-sm transition-all duration-300 shadow-lg hover:shadow-xl min-h-[44px]">
                    Jelajahi Semua Promo
                </a>
            </div>
        @endif

    </div>
</div>
@endsection