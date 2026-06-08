@extends('layouts.app')

@section('title', 'Jelajahi Promo - Promora')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Jelajahi Promo</h1>
        <p class="text-gray-500 text-sm mt-1">Temukan promo terbaik dari UMKM lokal di sekitar Anda</p>
    </div>

    {{-- Filter Form --}}
    <div x-data="{
            lat: '{{ request('lat') }}',
            lng: '{{ request('lng') }}',
            geoLoading: false,
            geoError: '',
            getLocation() {
                if (!navigator.geolocation) {
                    this.geoError = 'Browser Anda tidak mendukung geolokasi.';
                    return;
                }
                this.geoLoading = true;
                this.geoError = '';
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.lat = pos.coords.latitude;
                        this.lng = pos.coords.longitude;
                        this.geoLoading = false;
                    },
                    (err) => {
                        this.geoError = 'Gagal mendapatkan lokasi: ' + err.message;
                        this.geoLoading = false;
                    }
                );
            }
        }"
         class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">

        <form method="GET" action="{{ route('explore') }}" class="space-y-4">

            {{-- Hidden lat/lng inputs --}}
            <input type="hidden" name="lat" :value="lat">
            <input type="hidden" name="lng" :value="lng">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Keyword Search --}}
                <div>
                    <label for="q" class="block text-xs font-medium text-gray-600 mb-1">Kata Kunci</label>
                    <input type="text"
                           id="q"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Cari promo, seller..."
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 min-h-[44px]">
                </div>

                {{-- Category Dropdown --}}
                <div>
                    <label for="category_id" class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                    <select id="category_id"
                            name="category_id"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 min-h-[44px]">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Location Text Input --}}
                <div>
                    <label for="location" class="block text-xs font-medium text-gray-600 mb-1">Lokasi</label>
                    <input type="text"
                           id="location"
                           name="location"
                           value="{{ request('location') }}"
                           placeholder="Kota, kecamatan..."
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 min-h-[44px]">
                </div>

                {{-- Sort --}}
                <div>
                    <label for="sort" class="block text-xs font-medium text-gray-600 mb-1">Urutkan</label>
                    <select id="sort"
                            name="sort"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 min-h-[44px]">
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="ending_soon" {{ request('sort') === 'ending_soon' ? 'selected' : '' }}>Berakhir Segera</option>
                        <option value="most_viewed" {{ request('sort') === 'most_viewed' ? 'selected' : '' }}>Paling Banyak Dilihat</option>
                        <option value="nearest" {{ request('sort') === 'nearest' ? 'selected' : '' }}>Terdekat</option>
                    </select>
                </div>
            </div>

            {{-- Geolocation Button & Actions --}}
            <div class="flex flex-wrap items-center gap-3">

                {{-- Geolocation Button --}}
                <button type="button"
                        @click="getLocation()"
                        :disabled="geoLoading"
                        class="flex items-center space-x-2 text-sm text-blue-600 hover:text-blue-700 border border-blue-200 hover:border-blue-300 rounded-lg px-3 py-2 min-h-[44px] transition-colors bg-blue-50 hover:bg-blue-100">
                    <svg class="w-4 h-4" :class="geoLoading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span x-text="geoLoading ? 'Mendapatkan lokasi...' : (lat ? 'Lokasi Terdeteksi ✓' : 'Gunakan Lokasi Saya')"></span>
                </button>

                {{-- Geo Error --}}
                <p x-show="geoError" x-text="geoError" class="text-xs text-red-500"></p>

                {{-- Submit --}}
                <button type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-medium px-5 py-2 rounded-lg text-sm min-h-[44px] transition-colors">
                    Cari Promo
                </button>

                {{-- Reset --}}
                @if(request()->hasAny(['q', 'category_id', 'location', 'sort', 'lat', 'lng']))
                    <a href="{{ route('explore') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2 min-h-[44px] flex items-center transition-colors">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results Count --}}
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">
            Menampilkan <span class="font-semibold text-gray-700">{{ $promos->total() }}</span> promo
            @if(request('q'))
                untuk "<span class="font-semibold text-orange-500">{{ request('q') }}</span>"
            @endif
        </p>
    </div>

    {{-- Promo Grid --}}
    @if($promos->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($promos as $promo)
                @php
                    $isBookmarked = auth()->check()
                        ? $promo->bookmarks()->where('user_id', auth()->id())->exists()
                        : false;
                @endphp
                <x-promo-card :promo="$promo" :isBookmarked="$isBookmarked" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $promos->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-16">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">Tidak ada promo ditemukan</h3>
            <p class="text-gray-400 text-sm mb-4">Coba ubah filter pencarian Anda</p>
            <a href="{{ route('explore') }}"
               class="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white font-medium px-5 py-2 rounded-lg text-sm transition-colors min-h-[44px]">
                Lihat Semua Promo
            </a>
        </div>
    @endif

</div>
@endsection
