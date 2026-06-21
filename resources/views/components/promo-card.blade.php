@props(['promo', 'isBookmarked' => false])

@php
    $bookmarkCount = $promo->bookmarks_count ?? $promo->bookmarks()->count();
    $toggleRoute = auth()->check() ? route('consumer.bookmarks.toggle', $promo) : null;

    // Support both external URLs (http) and local storage paths
    $imageUrl = $promo->poster_image
        ? (Str::startsWith($promo->poster_image, 'http')
            ? $promo->poster_image
            : asset('storage/' . $promo->poster_image))
        : null;
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col">

    {{-- Poster Image --}}
    <div class="relative aspect-video bg-gray-100 overflow-hidden">
        @if($imageUrl)
            <img src="{{ $imageUrl }}"
                 alt="{{ $promo->title }}"
                 class="w-full h-full object-cover"
                 onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gradient-to-br from-orange-100 to-orange-200\'><svg class=\'w-12 h-12 text-orange-300\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z\'/></svg></div>'">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-orange-100 to-orange-200">
                <svg class="w-12 h-12 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
        @endif

{{-- Premium Badge --}}
@if($promo->is_premium)
    <div class="absolute top-2 left-2 bg-yellow-400 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
        ⭐ Premium
    </div>
@endif

{{-- Discount Badge --}}
@if($promo->discount_percentage)
    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
        -{{ number_format($promo->discount_percentage, 0) }}%
    </div>
@endif

        {{-- Hot Deal Badge --}}
        @if($promo->is_hot_deal)
            <div class="absolute top-2 right-2 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                🔥 Hot Deal
            </div>
        @endif
    </div>

    {{-- Card Body --}}
    <div class="p-4 flex flex-col flex-1">

        {{-- Title --}}
<div class="flex items-center gap-2 mb-2">

    @if($promo->is_premium)
        <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-1 rounded-full">
            ⭐ Premium
        </span>
    @endif

</div>

<h3 class="font-semibold text-gray-800 text-sm leading-snug line-clamp-2 mb-2">
    {{ $promo->title }}
</h3>
        {{-- Seller Name --}}
        <p class="text-xs text-gray-500 mb-1 truncate">
            <svg class="w-3 h-3 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            {{ $promo->seller->business_name ?? 'Seller' }}
        </p>

        {{-- Location --}}
        @if($promo->seller?->address)
            <p class="text-xs text-gray-400 mb-1 truncate">
                <svg class="w-3 h-3 inline mr-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ $promo->seller->address }}
            </p>
        @endif

        {{-- Average Rating --}}
        <div class="flex items-center space-x-1 mb-2">
            @php $rating = $promo->seller?->averageRating() ?? 0; @endphp
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-3 h-3 {{ $i <= round($rating) ? 'text-yellow-400' : 'text-gray-200' }}"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
            <span class="text-xs text-gray-400 ml-1">{{ number_format($rating, 1) }}</span>
        </div>

        {{-- End Date --}}
        <p class="text-xs text-gray-400 mb-3">
            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Berakhir: {{ $promo->end_date->format('d M Y') }}
        </p>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Bookmark Button --}}
        <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
            @auth
                <div x-data="{
                        bookmarked: {{ $isBookmarked ? 'true' : 'false' }},
                        count: {{ $bookmarkCount }},
                        loading: false,
                        toggle() {
                            if (this.loading) return;
                            this.loading = true;
                            fetch('{{ $toggleRoute }}', {
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
                    }" class="flex items-center space-x-1">
                    <button @click="toggle()"
                            :disabled="loading"
                            class="flex items-center space-x-1 text-xs text-gray-500 hover:text-orange-500 transition-colors min-h-[44px] px-2 py-1 rounded-lg hover:bg-orange-50"
                            :class="bookmarked ? 'text-orange-500' : 'text-gray-400'"
                            :aria-label="bookmarked ? 'Hapus bookmark' : 'Tambah bookmark'">
                        <svg class="w-4 h-4 transition-colors"
                             :class="bookmarked ? 'fill-orange-500 stroke-orange-500' : 'fill-none stroke-current'"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                        <span x-text="count" class="font-medium"></span>
                    </button>
                </div>
            @else
                <a href="{{ route('consumer.login') }}"
                   class="flex items-center space-x-1 text-xs text-gray-400 hover:text-orange-500 transition-colors min-h-[44px] px-2 py-1 rounded-lg hover:bg-orange-50">
                    <svg class="w-4 h-4 fill-none stroke-current" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    <span>{{ $bookmarkCount }}</span>
                </a>
            @endauth

            {{-- View Detail Link --}}
            @if(isset($promo->id))
                <a href="{{ route('promos.show', $promo) }}"
                   class="text-xs text-orange-500 hover:text-orange-600 font-medium min-h-[44px] flex items-center px-2">
                    Lihat →
                </a>
            @endif
        </div>
    </div>
</div>