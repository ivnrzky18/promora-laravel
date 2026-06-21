@extends('layouts.app')

@section('title', $promo->title . ' - Promora')

@section('content')
<div class="min-h-screen bg-[#F3E1E1] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white rounded-[2rem] shadow-xl shadow-red-900/5 border border-[#DD3015]/5 overflow-hidden relative">
            {{-- Aksen Dekoratif Atas --}}
            <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-[#DD3015] via-black to-[#DD3015]"></div>

            {{-- Poster (full width) --}}
            @if($promo->poster_image)
                <img src="{{ asset('storage/' . $promo->poster_image) }}"
                     alt="{{ $promo->title }}"
                     class="w-full h-64 sm:h-96 object-cover pt-2">
            @else
                <div class="w-full h-56 bg-gradient-to-br from-[#F3E1E1] to-white flex items-center justify-center pt-2 border-b border-gray-100">
                    <svg class="w-16 h-16 text-[#DD3015]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            @endif

            <div class="p-6 sm:p-8 space-y-6">

                {{-- Status & Category Badges --}}
                <div class="flex items-center flex-wrap gap-2">
                    @if($promo->status === 'active')
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-[10px] font-black tracking-wider uppercase bg-green-100 text-green-800">Aktif</span>
                    @elseif($promo->status === 'draft')
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-[10px] font-black tracking-wider uppercase bg-yellow-100 text-yellow-800">Draft</span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-[10px] font-black tracking-wider uppercase bg-red-100 text-[#DD3015]">Kadaluarsa</span>
                    @endif

                    @if($promo->is_hot_deal)
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-[10px] font-black tracking-wider uppercase bg-[#DD3015] text-white animate-pulse">🔥 Hot Deal</span>
                    @endif

                    @if($promo->category)
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-[10px] font-black tracking-wider uppercase bg-black text-white">{{ $promo->category->name }}</span>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="text-2xl sm:text-3xl font-black text-black uppercase tracking-tight leading-tight">
                    {{ $promo->title }}
                </h1>

                {{-- Discount Badge + Pricing --}}
                @if($promo->discount_percentage || $promo->promo_price)
                    <div class="flex items-baseline flex-wrap gap-3 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        @if($promo->discount_percentage)
                            <span class="text-3xl font-black text-[#DD3015] tracking-tight">{{ $promo->discount_percentage }}% OFF</span>
                        @endif
                        @if($promo->promo_price)
                            <span class="text-2xl font-black text-black tracking-tight">Rp {{ number_format($promo->promo_price, 0, ',', '.') }}</span>
                        @endif
                        @if($promo->original_price)
                            <span class="text-sm text-gray-400 font-bold line-through">Rp {{ number_format($promo->original_price, 0, ',', '.') }}</span>
                        @endif
                    </div>
                @endif

                {{-- Start/End Dates & Views --}}
                <div class="flex flex-wrap items-center gap-4 text-xs font-bold text-gray-500 border-b border-gray-100 pb-5">
                    <div class="flex items-center space-x-1.5 bg-white px-3 py-1.5 rounded-xl border border-gray-100">
                        <svg class="w-4 h-4 text-[#DD3015]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $promo->start_date->format('d M Y') }} – {{ $promo->end_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center space-x-1.5 bg-white px-3 py-1.5 rounded-xl border border-gray-100">
                        <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>{{ number_format($promo->view_count) }} TAYANGAN</span>
                    </div>
                </div>

                {{-- Countdown (if hot deal) --}}
                @if($promo->is_hot_deal)
                    <div class="bg-red-50 border border-[#DD3015]/10 rounded-2xl p-4 shadow-inner">
                        <p class="text-[10px] font-black tracking-widest text-[#DD3015] uppercase mb-2">SISA WAKTU PROMO PENAWARAN PANAS:</p>
                        <x-countdown :end-date="$promo->end_date" />
                    </div>
                @endif

                {{-- Description --}}
                @if($promo->description)
                    <div class="prose prose-sm max-w-none text-gray-600 font-medium leading-relaxed bg-gray-50/50 p-4 rounded-2xl border border-gray-100/50">
                        <p class="whitespace-pre-line">{{ $promo->description }}</p>
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
                            }" class="border-t border-gray-100 pt-5">
                            <button @click="toggle()"
                                    :disabled="loading"
                                    class="flex items-center space-x-2 px-5 py-3 rounded-xl font-black text-xs uppercase tracking-wider min-h-[44px] transition-all border shadow-sm"
                                    :class="bookmarked
                                        ? 'bg-[#F3E1E1] border-[#DD3015]/20 text-[#DD3015] hover:bg-red-100'
                                        : 'bg-white border-gray-200 text-black hover:border-black'">
                                <svg class="w-4 h-4 transition-colors"
                                     :class="bookmarked ? 'fill-[#DD3015] stroke-[#DD3015]' : 'fill-none stroke-current'"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                                <span x-text="bookmarked ? 'Disimpan (' + count + ')' : 'Simpan Promo (' + count + ')'"></span>
                            </button>
                        </div>
                    @endif
                @endauth

                {{-- Seller Info Card with link to seller profile --}}
                @if($promo->seller)
                    <div class="border-t border-gray-100 pt-5">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">DITAWARKAN OLEH MITRA</p>
                        <a href="{{ route('sellers.show', $promo->seller) }}"
                           class="flex items-center space-x-4 p-4 rounded-2xl border border-gray-100 bg-gray-50/50 hover:border-[#DD3015]/30 hover:bg-[#F3E1E1]/30 transition-all group">
                            
                            @if($promo->seller->logo)
                                <img src="{{ asset('storage/' . $promo->seller->logo) }}"
                                     alt="{{ $promo->seller->business_name }}"
                                     class="w-14 h-14 rounded-full object-cover flex-shrink-0 border-2 border-white shadow-sm">
                            @else
                                <div class="w-14 h-14 rounded-full bg-[#F3E1E1] flex items-center justify-center flex-shrink-0 border-2 border-white shadow-sm">
                                    <span class="text-xl font-black text-[#DD3015]">
                                        {{ strtoupper(substr($promo->seller->business_name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            
                            <div class="flex-1 min-w-0">
                                <p class="font-black text-base text-black uppercase tracking-tight group-hover:text-[#DD3015] transition-colors truncate">
                                    {{ $promo->seller->business_name }}
                                </p>
                                <p class="text-[10px] font-black text-[#DD3015] uppercase tracking-wider mt-0.5">
                                    {{ $promo->seller->business_category ?? 'UMKM LOKAL' }}
                                </p>
                                @if($promo->seller->address)
                                    <p class="text-xs font-bold text-gray-400 truncate mt-1 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        <span class="truncate">{{ $promo->seller->address }}</span>
                                    </p>
                                @endif
                            </div>
                            
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#DD3015] flex-shrink-0 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection