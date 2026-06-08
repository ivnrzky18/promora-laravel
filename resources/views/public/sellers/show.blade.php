@extends('layouts.app')

@section('title', $sellerProfile->business_name . ' - Promora')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Seller Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:space-x-6 space-y-4 sm:space-y-0">

            {{-- Logo --}}
            <div class="flex-shrink-0">
                @if($sellerProfile->logo)
                    <img src="{{ asset('storage/' . $sellerProfile->logo) }}"
                         alt="{{ $sellerProfile->business_name }}"
                         class="w-24 h-24 rounded-full object-cover border-2 border-orange-100">
                @else
                    <div class="w-24 h-24 rounded-full bg-orange-100 flex items-center justify-center border-2 border-orange-200">
                        <span class="text-3xl font-bold text-orange-500">
                            {{ strtoupper(substr($sellerProfile->business_name, 0, 1)) }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $sellerProfile->business_name }}</h1>
                        <p class="text-orange-500 font-medium">{{ $sellerProfile->business_category }}</p>
                        <p class="text-sm text-gray-500 mt-1 flex items-center space-x-1">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $sellerProfile->address }}</span>
                        </p>

                        {{-- Average Rating --}}
                        <div class="flex items-center space-x-2 mt-2">
                            <x-star-rating :value="$averageRating" :readonly="true" />
                            <span class="text-sm text-gray-500">
                                {{ number_format($averageRating, 1) }}
                                ({{ $reviews->count() }} ulasan)
                            </span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap gap-2">

                        {{-- Subscribe/Unsubscribe Button --}}
                        @auth
                            @if(auth()->user()->role === 'consumer')
                                <div x-data="{
                                        subscribed: {{ $isSubscribed ? 'true' : 'false' }},
                                        loading: false,
                                        toggle() {
                                            if (this.loading) return;
                                            this.loading = true;
                                            fetch('{{ route('consumer.subscriptions.toggle', $sellerProfile) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                    'Accept': 'application/json',
                                                    'Content-Type': 'application/json'
                                                }
                                            })
                                            .then(r => r.json())
                                            .then(data => { this.subscribed = data.subscribed; })
                                            .catch(() => {})
                                            .finally(() => { this.loading = false; });
                                        }
                                    }">
                                    <button @click="toggle()"
                                            :disabled="loading"
                                            class="flex items-center space-x-2 px-4 py-2 rounded-lg font-medium text-sm min-h-[44px] transition-colors"
                                            :class="subscribed
                                                ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                                : 'bg-orange-500 text-white hover:bg-orange-600'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <span x-text="subscribed ? 'Berhenti Ikuti' : 'Ikuti Seller'"></span>
                                    </button>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('consumer.login') }}"
                               class="flex items-center space-x-2 bg-orange-500 text-white px-4 py-2 rounded-lg font-medium text-sm min-h-[44px] hover:bg-orange-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span>Ikuti Seller</span>
                            </a>
                        @endauth

                        {{-- Share Profile Button --}}
                        <button onclick="
                                navigator.clipboard.writeText(window.location.href)
                                    .then(() => alert('Link profil disalin ke clipboard!'))
                                    .catch(() => alert('Gagal menyalin link.'));
                            "
                            class="flex items-center space-x-2 border border-gray-200 text-gray-600 hover:text-gray-800 hover:border-gray-300 px-4 py-2 rounded-lg font-medium text-sm min-h-[44px] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            <span>Bagikan Profil</span>
                        </button>
                    </div>
                </div>

                {{-- Description --}}
                @if($sellerProfile->description)
                    <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ $sellerProfile->description }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Active Promos --}}
    <div>
        <h2 class="text-lg font-bold text-gray-800 mb-4">
            Promo Aktif
            <span class="text-sm font-normal text-gray-400">({{ $promos->count() }})</span>
        </h2>

        @if($promos->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($promos as $promo)
                    @php
                        $isBookmarked = auth()->check()
                            ? $promo->bookmarks()->where('user_id', auth()->id())->exists()
                            : false;
                    @endphp
                    <x-promo-card :promo="$promo" :isBookmarked="$isBookmarked" />
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-100 p-8 text-center">
                <p class="text-gray-400 text-sm">Belum ada promo aktif saat ini.</p>
            </div>
        @endif
    </div>

    {{-- Review Form --}}
    @auth
        @if(auth()->user()->role === 'consumer')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Tulis Ulasan</h2>

                @if($errors->has('review'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                        {{ $errors->first('review') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('reviews.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="seller_id" value="{{ $sellerProfile->id }}">

                    {{-- Star Rating Input --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <x-star-rating name="rating" :value="old('rating', 0)" :readonly="false" />
                        @error('rating')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Comment --}}
                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Komentar (opsional)</label>
                        <textarea id="comment"
                                  name="comment"
                                  rows="3"
                                  maxlength="1000"
                                  placeholder="Bagikan pengalaman Anda dengan seller ini..."
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 resize-none min-h-[44px]">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="bg-orange-500 hover:bg-orange-600 text-white font-medium px-5 py-2 rounded-lg text-sm min-h-[44px] transition-colors">
                        Kirim Ulasan
                    </button>
                </form>
            </div>
        @endif
    @endauth

    {{-- Reviews List --}}
    <div>
        <h2 class="text-lg font-bold text-gray-800 mb-4">
            Ulasan Pelanggan
            <span class="text-sm font-normal text-gray-400">({{ $reviews->count() }})</span>
        </h2>

        @if($reviews->count() > 0)
            <div class="space-y-4">
                @foreach($reviews as $review)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                {{-- Avatar --}}
                                <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    @if($review->user->avatar)
                                        <img src="{{ asset('storage/' . $review->user->avatar) }}"
                                             alt="{{ $review->user->name }}"
                                             class="w-9 h-9 rounded-full object-cover">
                                    @else
                                        <span class="text-sm font-bold text-gray-500">
                                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-gray-800">{{ $review->user->name }}</p>
                                    <x-star-rating :value="$review->rating" :readonly="true" />
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                        </div>

                        @if($review->comment)
                            <p class="text-sm text-gray-600 mt-3 leading-relaxed">{{ $review->comment }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-100 p-8 text-center">
                <p class="text-gray-400 text-sm">Belum ada ulasan untuk seller ini.</p>
            </div>
        @endif
    </div>

</div>
@endsection
