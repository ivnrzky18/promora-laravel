@extends('layouts.consumer')

@section('title', 'Dashboard - Promora')

@push('styles')
<style>
    :root {
        --red-hero:    #E8281A;
        --red-dark:    #C41E12;
        --red-light:   #FF4D3D;
        --cream:       #FFF5F4;
        --card-bg:     #FFFFFF;
        --text-main:   #1A1A1A;
        --text-muted:  #8A8A8A;
        --text-soft:   #BBBBBB;
        --border:      #F0EEEE;
        --orange-chip: #FF6B35;
    }

    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    .hero-header {
        background: linear-gradient(135deg, var(--red-hero) 0%, var(--red-light) 60%, #FF8C69 100%);
        border-radius: 0 0 28px 28px;
        padding: 24px 20px 36px;
        position: relative;
        overflow: hidden;
    }
    .hero-header::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .hero-header::after {
        content: '';
        position: absolute;
        bottom: -30px; left: -20px;
        width: 120px; height: 120px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }

    .hero-search {
        background: rgba(255,255,255,0.18);
        border: 1.5px solid rgba(255,255,255,0.35);
        border-radius: 14px;
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0 14px;
        height: 50px;
        color: white;
        cursor: pointer;
        transition: background .2s;
    }
    .hero-search input {
        background: transparent;
        border: none;
        outline: none;
        color: white;
        font-size: 14px;
        width: 100%;
    }
    .hero-search input::placeholder { color: rgba(255,255,255,0.7); }

    .location-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 12px;
        color: rgba(255,255,255,0.9);
        cursor: pointer;
    }

    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -0.2px;
    }
    .see-all {
        font-size: 13px;
        color: var(--red-hero);
        font-weight: 600;
        text-decoration: none;
    }
    .see-all:hover { opacity: .8; }

    .promo-wrap {
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 3px 16px rgba(0,0,0,0.08);
        transition: transform .2s, box-shadow .2s;
        background: white;
    }
    .promo-wrap:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .hot-card {
        background: var(--card-bg);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 3px 16px rgba(0,0,0,0.07);
        transition: transform .2s, box-shadow .2s;
        position: relative;
    }
    .hot-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .hot-badge {
        position: absolute;
        top: 10px; left: 10px;
        background: linear-gradient(135deg, #FF4D3D, #E8281A);
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 20px;
        letter-spacing: .5px;
        text-transform: uppercase;
        z-index: 5;
    }

    .notif-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        transition: background .15s;
        border-bottom: 1px solid var(--border);
    }
    .notif-row:last-child { border-bottom: none; }
    .notif-row:hover { background: var(--cream); }

    .notif-avatar {
        width: 38px; height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .empty-state {
        background: var(--cream);
        border: 2px dashed #F0C5C0;
        border-radius: 18px;
        padding: 36px 20px;
        text-align: center;
    }

    .filter-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .filter-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
        border: 1.5px solid var(--border);
        background: var(--card-bg);
        color: var(--text-muted);
        cursor: pointer;
        transition: all .15s;
        text-decoration: none;
    }
    .filter-tag:hover, .filter-tag.active {
        border-color: var(--red-hero);
        color: var(--red-hero);
        background: #FFF0EF;
    }

    .filter-panel {
        background: var(--card-bg);
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    .filter-panel label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
        letter-spacing: .5px;
        text-transform: uppercase;
        margin-bottom: 6px;
        display: block;
    }
    .filter-input {
        width: 100%;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 13px;
        color: var(--text-main);
        outline: none;
        transition: border-color .15s;
        background: var(--card-bg);
    }
    .filter-input:focus { border-color: var(--red-hero); }

    .geo-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #3B82F6;
        background: #EFF6FF;
        border: 1.5px solid #BFDBFE;
        border-radius: 10px;
        padding: 8px 14px;
        cursor: pointer;
        transition: all .15s;
    }
    .geo-btn:hover { background: #DBEAFE; }

    .btn-primary {
        background: linear-gradient(135deg, var(--red-hero), var(--red-light));
        color: white;
        font-size: 13px;
        font-weight: 700;
        border: none;
        border-radius: 10px;
        padding: 10px 22px;
        cursor: pointer;
        transition: opacity .15s;
        letter-spacing: .2px;
    }
    .btn-primary:hover { opacity: .9; }

    .page-body {
        background: var(--cream);
        min-height: 100vh;
        padding-bottom: 80px;
    }
    .content-pad {
        padding: 0 16px;
    }
    .section-gap { margin-top: 28px; }

    .cat-chip {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: transform .15s;
        min-width: 68px;
        text-decoration: none;
    }
    .cat-chip:active { transform: scale(0.94); }
    .cat-chip-icon {
        width: 60px; height: 60px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        background: var(--card-bg);
        box-shadow: 0 3px 14px rgba(0,0,0,0.08);
        border: 1.5px solid var(--border);
        transition: border-color .15s, box-shadow .15s;
    }
    .cat-chip.active .cat-chip-icon,
    .cat-chip:hover .cat-chip-icon {
        border-color: var(--red-hero);
        box-shadow: 0 3px 14px rgba(232,40,26,0.18);
    }
    .cat-chip span.label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-main);
        text-align: center;
        line-height: 1.2;
    }

    /* ================= EVENT CARD ================= */
    .event-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 3px 16px rgba(0,0,0,0.08);
        transition: transform .2s, box-shadow .2s;
        border: 1px solid #f3e8d8;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .event-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .event-thumb {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 10;
        background: linear-gradient(135deg, #FFE8B3, #FFD369);
        overflow: hidden;
    }
    .event-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .event-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: linear-gradient(135deg, #FFB800, #FF8A00);
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 999px;
        letter-spacing: .4px;
        z-index: 3;
    }
    .event-body {
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }
    .event-meta {
        display: flex;
        flex-direction: column;
        gap: 5px;
        font-size: 12px;
        color: #6b7280;
    }
    .event-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .event-seller {
        font-size: 12px;
        font-weight: 600;
        color: #E8281A;
    }
    .event-title {
        font-size: 15px;
        font-weight: 800;
        color: #111827;
        line-height: 1.3;
    }
    .event-desc {
        font-size: 12px;
        color: #6b7280;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 36px;
    }
    .event-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }
    .event-date-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        background: #FFF7E6;
        border: 1px solid #FFE2A8;
        color: #B45309;
        font-size: 11px;
        font-weight: 700;
        border-radius: 999px;
    }
    .event-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 14px;
        border-radius: 12px;
        background: linear-gradient(135deg, #E8281A, #FF4D3D);
        color: white;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: opacity .15s;
    }
    .event-btn:hover { opacity: .92; color: white; }

    @media (min-width: 640px) {
        .content-pad { padding: 0 24px; }
        .hero-header { padding: 28px 24px 44px; }
    }
    @media (min-width: 1024px) {
        .content-pad { padding: 0 40px; }
        .hero-header { padding: 32px 40px 50px; border-radius: 0 0 32px 32px; }
    }
</style>
@endpush

@section('content')
<div class="page-body" x-data="{
    geoLoading: false,
    geoError: '',
    showFilter: false,
    getLocation() {
        if (!navigator.geolocation) {
            this.geoError = 'Browser tidak mendukung geolokasi.';
            return;
        }

        this.geoLoading = true;
        this.geoError = '';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                document.getElementById('input-location').value = pos.coords.latitude + ',' + pos.coords.longitude;
                this.geoLoading = false;
            },
            () => {
                this.geoError = 'Gagal mendapatkan lokasi.';
                this.geoLoading = false;
            }
        );
    }
}">

    {{-- HERO HEADER --}}
    <div class="hero-header">
        <div class="flex items-center justify-between mb-5 relative z-10">
            <div>
                <p class="text-white/70 text-xs font-medium mb-0.5">Selamat datang 👋</p>
                <h1 class="text-white text-xl font-bold leading-tight">{{ $user->name }}</h1>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" class="location-badge">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    Pekanbaru
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <a href="{{ route('consumer.notifications') }}"
                   class="relative w-9 h-9 bg-white/15 border border-white/25 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if(!empty($recentNotifications) && $recentNotifications->where('read_at', null)->count() > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-yellow-400 rounded-full border border-white"></span>
                    @endif
                </a>
            </div>
        </div>

        <form action="{{ route('consumer.dashboard') }}" method="GET" class="relative z-10">
            @if($mode === 'event')
                <input type="hidden" name="mode" value="event">
            @endif

            <div class="hero-search">
                <svg class="w-4 h-4 flex-shrink-0 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari promo, event, seller...">
                <button type="button" @click="showFilter = !showFilter"
                        class="flex-shrink-0 w-7 h-7 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/30 transition-colors"
                        title="Filter">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </button>
            </div>
        </form>

        <div class="grid grid-cols-4 gap-2 mt-5 relative z-10">
            @php
                $heroStats = [
                    ['val' => $bookmarkCount,           'label' => 'Bookmark',     'icon' => '🔖'],
                    ['val' => $subscriptionCount,       'label' => 'Following',    'icon' => '🔔'],
                    ['val' => $expiringPromoCount ?? 0, 'label' => 'Hampir Habis', 'icon' => '⏰'],
                    ['val' => $newSellerCount ?? 0,     'label' => 'Seller Baru',  'icon' => '🆕'],
                ];
            @endphp

            @foreach($heroStats as $stat)
                <div class="bg-white/15 border border-white/20 rounded-2xl p-3 text-center">
                    <span class="block text-base mb-0.5">{{ $stat['icon'] }}</span>
                    <span class="block text-white font-bold text-lg leading-tight">{{ $stat['val'] }}</span>
                    <span class="block text-white/65 text-[9px] font-medium leading-tight mt-0.5">{{ $stat['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ADVANCED FILTER PANEL --}}
    <div class="content-pad"
         x-show="showFilter"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="margin-top: 16px; display: none;">
        <form action="{{ route('consumer.dashboard') }}" method="GET" class="filter-panel">
            @if($mode === 'event')
                <input type="hidden" name="mode" value="event">
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label>Kata Kunci</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari promo, event, seller..." class="filter-input">
                </div>

                @if($mode !== 'event')
                    <div>
                        <label>Kategori Promo</label>
                        <select name="category_id" class="filter-input">
                            <option value="">Semua Kategori</option>
                            @foreach(\App\Models\Category::all() as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label>Lokasi</label>
                    <input type="text" id="input-location" name="location" value="{{ request('location') }}" placeholder="Kota, kecamatan..." class="filter-input">
                </div>

                @if($mode !== 'event')
                    <div>
                        <label>Urutkan Promo</label>
                        <select name="sort" class="filter-input">
                            <option value="latest" {{ request('sort','latest')==='latest'?'selected':'' }}>Terbaru</option>
                            <option value="ending_soon" {{ request('sort')==='ending_soon'?'selected':'' }}>Berakhir Segera</option>
                            <option value="most_viewed" {{ request('sort')==='most_viewed'?'selected':'' }}>Paling Banyak Dilihat</option>
                        </select>
                    </div>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click="getLocation()" :disabled="geoLoading" class="geo-btn">
                    <svg class="w-4 h-4" :class="geoLoading?'animate-spin':''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span x-text="geoLoading ? 'Mendapatkan lokasi...' : 'Gunakan Lokasi Saya'"></span>
                </button>

                <p x-show="geoError" x-text="geoError" class="text-xs text-red-500"></p>

                <button type="submit" class="btn-primary">Cari</button>

                <a href="{{ route('consumer.dashboard', $mode === 'event' ? ['mode' => 'event'] : []) }}" class="filter-tag">
                    ✕ Reset
                </a>
            </div>
        </form>
    </div>

    {{-- CATEGORY CHIPS --}}
    <div class="content-pad section-gap">
        <div class="flex items-start gap-3 overflow-x-auto pb-2 scrollbar-hide -mx-1 px-1">
            @php
                $cats = [
                    ['id'=>'',      'label'=>'Semua',      'icon'=>'☰',  'type'=>'promo'],
                    ['id'=>1,       'label'=>'Kuliner',    'icon'=>'🍜', 'type'=>'promo'],
                    ['id'=>2,       'label'=>'Fashion',    'icon'=>'👗', 'type'=>'promo'],
                    ['id'=>3,       'label'=>'Kecantikan', 'icon'=>'✨', 'type'=>'promo'],
                    ['id'=>4,       'label'=>'Elektronik', 'icon'=>'📱', 'type'=>'promo'],
                    ['id'=>5,       'label'=>'Kesehatan',  'icon'=>'❤️', 'type'=>'promo'],
                    ['id'=>6,       'label'=>'Pendidikan', 'icon'=>'📚', 'type'=>'promo'],
                    ['id'=>7,       'label'=>'Otomotif',   'icon'=>'🚗', 'type'=>'promo'],
                    ['id'=>8,       'label'=>'Hiburan',    'icon'=>'🎬', 'type'=>'promo'],
                    ['id'=>'event', 'label'=>'Event',      'icon'=>'📅', 'type'=>'event'],
                ];
            @endphp

            @foreach($cats as $cat)
                @php
                    if ($cat['type'] === 'event') {
                        $url = route('consumer.dashboard', ['mode' => 'event']);
                        $isActive = $mode === 'event';
                    } else {
                        $params = [];

                        if ($cat['id'] !== '') {
                            $params['category_id'] = $cat['id'];
                        }

                        $url = route('consumer.dashboard', $params);

                        $isActive = $mode !== 'event' &&
                            (
                                (request('category_id') == $cat['id'] && $cat['id'] !== '') ||
                                ($cat['id'] === '' && !request('category_id'))
                            );
                    }
                @endphp

                <a href="{{ $url }}"
                   class="cat-chip flex-shrink-0 {{ $isActive ? 'active' : '' }}">
                    <div class="cat-chip-icon">{{ $cat['icon'] }}</div>
                    <span class="label">{{ $cat['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ACTIVE FILTERS --}}
    @if(request()->hasAny(['q','category_id','location','sort']) || $mode === 'event')
        <div class="content-pad mt-3">
            <div class="filter-row">
                <span class="text-xs text-gray-500 font-medium">Filter aktif:</span>

                @if($mode === 'event')
                    <span class="filter-tag active">📅 Event</span>
                @endif

                @if(request('q'))
                    <span class="filter-tag active">🔍 {{ request('q') }}</span>
                @endif

                @if(request('location'))
                    <span class="filter-tag active">📍 {{ request('location') }}</span>
                @endif

                @if(request('sort') && request('sort') !== 'latest')
                    <span class="filter-tag active">
                        ↕ {{ request('sort') === 'ending_soon' ? 'Berakhir Segera' : 'Paling Dilihat' }}
                    </span>
                @endif

                <a href="{{ route('consumer.dashboard', $mode === 'event' ? ['mode' => 'event'] : []) }}"
                   class="filter-tag"
                   style="color:#E8281A;border-color:#E8281A;background:#FFF0EF;">
                    ✕ Hapus Semua
                </a>
            </div>
        </div>
    @endif

    {{-- ========================= MODE PROMO ========================= --}}
    @if($mode !== 'event')

        {{-- FEED PROMO --}}
        <section class="content-pad section-gap">
            <div class="flex items-center justify-between mb-4">
                <h2 class="section-title">Feed Promo</h2>
                <a href="{{ url('/explore') }}" class="see-all">Lihat Semua →</a>
            </div>

            @if($promoFeed->isEmpty() && $subscriptionCount == 0)
                <div class="empty-state mb-5">
                    <div class="text-4xl mb-3">🛍️</div>
                    <p class="font-bold text-gray-700 mb-1">Mulai ikuti UMKM favoritmu!</p>
                    <p class="text-gray-400 text-sm mb-4">Subscribe ke seller untuk melihat promo terbaru mereka di sini.</p>
                    <a href="{{ url('/explore') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl"
                       style="background: linear-gradient(135deg, #E8281A, #FF4D3D);">
                        Jelajahi Seller
                    </a>
                </div>
            @elseif($promoFeed->isEmpty())
                <div class="empty-state">
                    <div class="text-4xl mb-3">🔔</div>
                    <p class="font-bold text-gray-700 mb-1">Seller yang kamu ikuti belum punya promo aktif</p>
                    <p class="text-gray-400 text-sm mb-4">Coba jelajahi seller lainnya.</p>
                    <a href="{{ url('/explore') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl"
                       style="background: linear-gradient(135deg, #E8281A, #FF4D3D);">
                        Jelajahi Seller
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($promoFeed as $promo)
                        <div class="promo-wrap">
                            <x-promo-card :promo="$promo" />
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- HOT DEALS --}}
        <section class="content-pad section-gap">
            <div class="flex items-center justify-between mb-4">
                <h2 class="section-title">🔥 Hot Deals</h2>
                <a href="{{ url('/hot-deals') }}" class="see-all">Lihat Semua →</a>
            </div>

            @if($hotDeals->isEmpty())
                <div class="empty-state">
                    <div class="text-4xl mb-2">🔥</div>
                    <p class="text-gray-400 text-sm">Tidak ada hot deals saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($hotDeals as $promo)
                        <div class="hot-card">
                            <span class="hot-badge">🔥 Hot</span>
                            <x-promo-card :promo="$promo" />
                            <div class="px-4 pb-4 -mt-1">
                                <x-countdown :end_date="$promo->end_date" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

    @endif

    {{-- ========================= MODE EVENT ========================= --}}
    @if($mode === 'event')

        {{-- FEED EVENT --}}
        <section class="content-pad section-gap">
            <div class="flex items-center justify-between mb-4">
                <h2 class="section-title">📅 Feed Event</h2>
                <span class="text-sm text-gray-500 font-medium">{{ $eventFeed->count() }} event</span>
            </div>

            @if($eventFeed->isEmpty())
                <div class="empty-state">
                    <div class="text-4xl mb-3">📭</div>
                    <p class="font-bold text-gray-700 mb-1">Belum ada event aktif saat ini</p>
                    <p class="text-gray-400 text-sm">Coba cek lagi nanti ya, siapa tahu ada event baru dari seller favoritmu.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($eventFeed as $event)
                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all border border-gray-100">
                            <div class="relative h-52 bg-gray-100 overflow-hidden">
                                @if($event->poster_image)
                                    <img src="{{ asset('storage/' . $event->poster_image) }}"
                                         alt="{{ $event->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-50 to-orange-50">
                                        <svg class="w-14 h-14 text-[#DD3015]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif

                                @if($event->is_premium)
                                    <div class="absolute top-3 left-3">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-400 text-white text-xs font-bold shadow">
                                            ⭐ Premium
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <h3 class="text-lg font-bold text-black leading-tight line-clamp-2">
                                        {{ $event->title }}
                                    </h3>
                                </div>

                                <p class="text-sm text-gray-500 mb-2 font-medium">
                                    {{ $event->seller->business_name ?? 'Seller' }}
                                </p>

                                @if($event->description)
                                    <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                                        {{ $event->description }}
                                    </p>
                                @endif

                                <div class="space-y-2 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#DD3015]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>
                                            {{ $event->event_date ? $event->event_date->translatedFormat('d M Y, H:i') : '-' }}
                                        </span>
                                    </div>

                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-[#DD3015] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="line-clamp-2">
                                            {{ $event->location ?: ($event->seller->address ?? 'Lokasi belum tersedia') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <a href="{{ route('sellers.show', $event->seller) }}"
                                       class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-[#DD3015] hover:bg-[#F30000] text-white text-sm font-bold rounded-xl transition">
                                        Lihat Seller
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

       

        {{-- EVENT TERBARU / UPCOMING --}}
        <section class="content-pad section-gap">
            <div class="flex items-center justify-between mb-4">
                <h2 class="section-title">🎉 Event Terbaru</h2>
                <a href="{{ route('consumer.dashboard', ['mode' => 'event']) }}" class="see-all">Lihat Semua →</a>
            </div>

            @if($upcomingEvents->isEmpty())
                <div class="empty-state">
                    <div class="text-4xl mb-2">📅</div>
                    <p class="text-gray-400 text-sm">Belum ada event yang tersedia saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($upcomingEvents as $event)
                        <div class="event-card">
                            <div class="event-thumb">
                                @if($event->is_premium)
                                    <span class="event-badge">PREMIUM</span>
                                @endif

                                @if($event->poster_image)
                                    <img src="{{ Storage::url($event->poster_image) }}" alt="{{ $event->title }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-5xl">🎊</div>
                                @endif
                            </div>

                            <div class="event-body">
                                <div>
                                    <p class="event-seller">{{ $event->seller->business_name ?? 'Seller' }}</p>
                                    <h3 class="event-title">{{ $event->title }}</h3>
                                </div>

                                <p class="event-desc">
                                    {{ \Illuminate\Support\Str::limit($event->description, 110) ?: 'Event menarik yang bisa kamu kunjungi di Promora.' }}
                                </p>

                                <div class="event-meta">
                                    <div class="event-meta-item">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $event->event_date ? $event->event_date->format('d M Y, H:i') : '-' }}
                                    </div>

                                    @if($event->end_date)
                                        <div class="event-meta-item">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Sampai {{ $event->end_date->format('d M Y, H:i') }}
                                        </div>
                                    @endif

                                    <div class="event-meta-item">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $event->location ?: ($event->seller->address ?? 'Lokasi menyusul') }}
                                    </div>
                                </div>

                                <div class="event-footer">
                                    <span class="event-date-pill">
                                        📅 {{ $event->event_date ? $event->event_date->translatedFormat('d M') : '-' }}
                                    </span>

                                    @if($event->seller)
                                        <a href="{{ route('sellers.show', $event->seller) }}" class="event-btn">
                                            Lihat Seller
                                        </a>
                                    @else
                                        <span class="event-btn">Detail</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

    @endif

    {{-- NOTIFIKASI TERBARU --}}
    <section class="content-pad section-gap">
        <div class="flex items-center justify-between mb-4">
            <h2 class="section-title">🔔 Notifikasi Terbaru</h2>
            <a href="{{ route('consumer.notifications') }}" class="see-all">Lihat Semua →</a>
        </div>

        @if(empty($recentNotifications) || $recentNotifications->isEmpty())
            <div class="empty-state">
                <div class="text-4xl mb-2">📭</div>
                <p class="text-gray-400 text-sm">Belum ada notifikasi terbaru.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl overflow-hidden" style="box-shadow:0 3px 16px rgba(0,0,0,0.07);">
                @foreach($recentNotifications as $notif)
                    <div class="notif-row {{ $notif->read_at ? 'opacity-60' : '' }}">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <div class="notif-avatar" style="background:{{ $notif->read_at ? '#F3F4F6' : '#FFF0EF' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     style="color:{{ $notif->read_at ? '#9CA3AF' : '#E8281A' }}">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $notif->data['title'] ?? 'Notifikasi' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $notif->data['message'] ?? '' }}</p>
                                <p class="text-xs mt-1" style="color:var(--text-soft)">{{ $notif->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 ml-4">
                            @if(!$notif->read_at)
                                <form action="{{ route('consumer.notifications.read', $notif->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[11px] text-gray-400 hover:text-gray-600 underline whitespace-nowrap">
                                        Tandai dibaca
                                    </button>
                                </form>
                            @endif

                            @if(isset($notif->data['promo_id']))
                                <a href="{{ route('promos.show', $notif->data['promo_id']) }}"
                                   class="text-xs text-red-500 font-medium hover:underline whitespace-nowrap">
                                    Lihat Promo
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

</div>
@endsection