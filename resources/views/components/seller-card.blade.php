@props(['seller'])

@php
    $avgRating   = $seller->averageRating();
    $activePromos = $seller->promos()->active()->count();
@endphp

<a href="{{ route('sellers.show', $seller) }}"
   class="block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow p-4">

    <div class="flex items-start space-x-3">

        {{-- Logo --}}
        <div class="flex-shrink-0">
            @if($seller->logo)
                <img src="{{ asset('storage/' . $seller->logo) }}"
                     alt="{{ $seller->business_name }}"
                     class="w-14 h-14 rounded-full object-cover border border-gray-100">
            @else
                <div class="w-14 h-14 rounded-full bg-orange-100 flex items-center justify-center border border-orange-200">
                    <span class="text-xl font-bold text-orange-500">
                        {{ strtoupper(substr($seller->business_name, 0, 1)) }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-gray-800 truncate">{{ $seller->business_name }}</h3>
            <p class="text-xs text-orange-500 font-medium mb-1">{{ $seller->business_category }}</p>

            {{-- Address --}}
            <p class="text-xs text-gray-500 truncate mb-2">
                <svg class="w-3 h-3 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ $seller->address }}
            </p>

            {{-- Rating & Promo Count --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-1">
                    <x-star-rating :value="$avgRating" :readonly="true" />
                    <span class="text-xs text-gray-400 ml-1">{{ number_format($avgRating, 1) }}</span>
                </div>
                <span class="text-xs text-gray-500">
                    {{ $activePromos }} promo aktif
                </span>
            </div>
        </div>
    </div>
</a>
