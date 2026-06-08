@extends('layouts.app')

@section('title', $promo->title . ' - Promora')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Poster (full width) --}}
        @if($promo->poster_image)
            <img src="{{ asset('storage/' . $promo->poster_image) }}"
                 alt="{{ $promo->title }}"
                 class="w-full h-64 sm:h-80 object-cover">
        @else
            <div class="w-full h-48 bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center">
                <svg class="w-16 h-16 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
        @endif

        <div class="p-6 space-y-4">

            {{-- Status & Category Badges --}}
            <div class="flex items-center flex-wrap gap-2">
                @if($promo->status === 'active')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                @elseif($promo->status === 'draft')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Draft</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Kadaluarsa</span>
                @endif

                @if($promo->is_hot_deal)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">🔥 Hot Deal</span>
                @endif

                @if($promo->category)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $promo->category->name }}</span>
                @endif
            </div>

            {{-- Title --}}
            <h1 class="text-2xl font-bold text-gray-800">{{ $promo->title }}</h1>

            {{-- Discount Badge + Pricing --}}
            @if($promo->discount_percentage || $promo->promo_price)
                <div class="flex items-center flex-wrap gap-3">
                    @if($promo->discount_percentage)
                        <span class="text-2xl font-bold text-orange-500">{{ $promo->discount_percentage }}% OFF</span>
                    @endif
                    @if($promo->promo_price)
                        <span class="text-xl font-semibold text-gray-800">Rp {{ number_format($promo->promo_price, 0, ',', '.') }}</span>
                    @endif
                    @if($promo->original_price)
                        <span class="text-sm text-gray-400 line-through">Rp {{ number_format($promo->original_price, 0, ',', '.') }}</span>
                    @endif
                </div>
            @endif

            {{-- Start/End Dates --}}
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                <div class="flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ $promo->start_date->format('d M Y') }} – {{ $promo->end_date->format('d M Y') }}</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>{{ number_format($promo->view_count) }} tayangan</span>
                </div>
            </div>

            {{-- Countdown (if hot deal) --}}
            @if($promo->is_hot_deal)
                <div class="bg-orange-50 border border-orange-100 rounded-lg p-3">
                    <x-countdown :end-date="$promo->end_date" />
                </div>
            @endif

            {{-- Description --}}
            @if($promo->description)
                <div class="prose prose-sm max-w-none text-gray-600">
                    <p>{{ $promo->description }}</p>
                </div>
            @endif

            {{-- Bookmark Button (for logged-in consumers) --}}
            @auth
                @if(auth()->user()->role === 'consumer')
                    @php
                        $isBookmarked = $promo->bookmarks()->where('user_id', auth()->id())->exists();
                        $bookmarkCount = $promo->bookmarks()->count();
                    @endphp
                    <div x-data="{
                            bookmarked: {{ $isBookmarked ? 'true' : 'false' }},
                            count: {{ $bookmarkCount }},
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
                        }" class="border-t border-gray-100 pt-4">
                        <button @click="toggle()"
                                :disabled="loading"
                                class="flex items-center space-x-2 px-4 py-2 rounded-lg font-medium text-sm min-h-[44px] transition-colors border"
                                :class="bookmarked
                                    ? 'bg-orange-50 border-orange-200 text-orange-600 hover:bg-orange-100'
                                    : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100'">
                            <svg class="w-5 h-5 transition-colors"
                                 :class="bookmarked ? 'fill-orange-500 stroke-orange-500' : 'fill-none stroke-current'"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            <span x-text="bookmarked ? 'Tersimpan (' + count + ')' : 'Simpan Promo (' + count + ')'"></span>
                        </button>
                    </div>
                @endif
            @endauth

            {{-- Seller Info Card with link to seller profile --}}
            @if($promo->seller)
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-3">Ditawarkan oleh</p>
                    <a href="{{ route('sellers.show', $promo->seller) }}"
                       class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 hover:border-orange-200 hover:bg-orange-50 transition-colors group">
                        @if($promo->seller->logo)
                            <img src="{{ asset('storage/' . $promo->seller->logo) }}"
                                 alt="{{ $promo->seller->business_name }}"
                                 class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-lg font-bold text-orange-500">
                                    {{ strtoupper(substr($promo->seller->business_name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 group-hover:text-orange-600 transition-colors">
                                {{ $promo->seller->business_name }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $promo->seller->business_category }}</p>
                            @if($promo->seller->address)
                                <p class="text-xs text-gray-400 truncate">{{ $promo->seller->address }}</p>
                            @endif
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-orange-500 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection
