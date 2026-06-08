@extends('layouts.app')

@section('title', 'Promora - Temukan Promo UMKM Terbaik di Sekitar Anda')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    * { font-family: 'Poppins', sans-serif; }

    :root {
        --merah:     #DD3015;
        --merah-cerah: #F30000;
        --kuning:    #FFB800;
        --bg-pink:   #F3E1E1;
        --putih:     #FFFFFF;
        --overlay:   rgba(221, 48, 21, 0.60);
        --merah-dark: #B5250F;
    }

    /* ── Floating Emojis ── */
    @keyframes floatUp {
        0%   { transform: translateY(0px) rotate(0deg); opacity: 0.85; }
        50%  { transform: translateY(-18px) rotate(6deg); opacity: 1; }
        100% { transform: translateY(0px) rotate(0deg); opacity: 0.85; }
    }
    @keyframes floatUpAlt {
        0%   { transform: translateY(0px) rotate(0deg) scale(1); opacity: 0.7; }
        50%  { transform: translateY(-22px) rotate(-8deg) scale(1.1); opacity: 1; }
        100% { transform: translateY(0px) rotate(0deg) scale(1); opacity: 0.7; }
    }
    .float-emoji {
        position: absolute;
        pointer-events: none;
        animation: floatUp 4s ease-in-out infinite;
        user-select: none;
        line-height: 1;
        filter: drop-shadow(0 4px 10px rgba(0,0,0,0.18));
    }
    .float-emoji.alt { animation: floatUpAlt 5s ease-in-out infinite; }
    .float-emoji.slow { animation-duration: 6.5s; }
    .float-emoji.fast { animation-duration: 3s; }

    /* ── Hero gradient ── */
    .hero-bg {
        background: linear-gradient(140deg, #DD3015 0%, #c0250e 55%, #9e1c09 100%);
    }

    /* ── Noise texture overlay ── */
    .hero-bg::before {
        content: '';
        position: absolute; inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
        opacity: 0.06;
        pointer-events: none;
    }

    /* ── Shine line top ── */
    .hero-top-line {
        background: linear-gradient(90deg, transparent 0%, #FFB800 30%, #FFD966 50%, #FFB800 70%, transparent 100%);
        height: 3px;
    }

    /* ── Badge pill ── */
    .badge-pill {
        background: rgba(255,184,0,0.18);
        border: 1px solid rgba(255,184,0,0.45);
        color: #FFD966;
        backdrop-filter: blur(4px);
    }

    /* ── CTA buttons ── */
    .btn-masuk {
        background: #FFB800;
        color: #7A1A00;
        border: none;
        font-weight: 700;
        transition: background 0.2s, transform 0.18s, box-shadow 0.2s;
        box-shadow: 0 4px 18px rgba(255,184,0,0.35);
    }
    .btn-masuk:hover {
        background: #FFD255;
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(255,184,0,0.5);
    }
    .btn-daftar {
        background: rgba(255,255,255,0.10);
        color: #fff;
        border: 1.5px solid rgba(255,255,255,0.35);
        font-weight: 600;
        backdrop-filter: blur(6px);
        transition: background 0.2s, transform 0.18s;
    }
    .btn-daftar:hover {
        background: rgba(255,255,255,0.22);
        transform: translateY(-3px);
    }

    /* ── Stats card ── */
    .stat-card {
        background: rgba(255,255,255,0.10);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.16);
        border-radius: 16px;
    }

    /* ── Wave separator ── */
    .wave-sep {
        line-height: 0;
        overflow: hidden;
        margin-top: -2px;
    }
    .wave-sep svg { display: block; }

    /* ── Section headings ── */
    .section-title {
        font-size: clamp(1.3rem, 3vw, 1.75rem);
        font-weight: 800;
        color: var(--merah-dark);
        letter-spacing: -0.01em;
    }
    .section-sub { color: #b07070; font-size: 0.875rem; }

    /* ── Promo card ── */
    .promo-card {
        background: var(--putih);
        border-radius: 18px;
        overflow: hidden;
        border: 1.5px solid #f0cece;
        box-shadow: 0 2px 12px rgba(221,48,21,0.06);
        transition: transform 0.22s, box-shadow 0.22s;
    }
    .promo-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 32px rgba(221,48,21,0.14);
    }

    /* ── Hot deal card ── */
    .hot-card {
        background: var(--putih);
        border-radius: 18px;
        overflow: hidden;
        border: 1.5px solid #f0cece;
        box-shadow: 0 2px 12px rgba(221,48,21,0.06);
        transition: transform 0.22s, box-shadow 0.22s;
        position: relative;
    }
    .hot-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 32px rgba(221,48,21,0.16);
    }

    /* ── Discount badge ── */
    .badge-disc {
        background: var(--kuning);
        color: #7A1A00;
        font-weight: 700;
        font-size: 0.7rem;
        padding: 3px 9px;
        border-radius: 99px;
        position: absolute; top: 10px; left: 10px;
    }
    .badge-hot {
        background: var(--merah);
        color: #fff;
        font-weight: 700;
        font-size: 0.7rem;
        padding: 3px 9px;
        border-radius: 99px;
        position: absolute; top: 10px; right: 10px;
    }

    /* ── Category pill ── */
    .cat-pill {
        background: var(--putih);
        border: 1.5px solid #f0cece;
        border-radius: 14px;
        padding: 14px 8px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 6px;
        transition: background 0.2s, border-color 0.2s, transform 0.2s;
        cursor: pointer;
        text-decoration: none;
    }
    .cat-pill:hover {
        background: var(--merah);
        border-color: var(--merah);
        transform: translateY(-4px);
    }
    .cat-pill:hover .cat-label { color: #FFD966; }
    .cat-pill:hover .cat-count { color: rgba(255,217,102,0.75); }

    /* ── CTA section ── */
    .cta-section {
        background: linear-gradient(135deg, var(--merah) 0%, #b0200a 100%);
        position: relative; overflow: hidden;
    }

    /* ── View all button ── */
    .btn-view-all {
        display: inline-flex; align-items: center; gap: 6px;
        font-weight: 700; font-size: 0.875rem;
        padding: 11px 28px; border-radius: 12px;
        background: var(--merah); color: var(--putih);
        transition: background 0.2s, transform 0.18s;
        box-shadow: 0 4px 14px rgba(221,48,21,0.3);
    }
    .btn-view-all:hover { background: var(--merah-cerah); transform: translateY(-2px); }
    .btn-view-all.gold {
        background: var(--kuning); color: #7A1A00;
        box-shadow: 0 4px 14px rgba(255,184,0,0.35);
    }
    .btn-view-all.gold:hover { background: #FFD255; }

    /* ── Page background ── */
    body { background: var(--bg-pink); }
</style>
@endpush

@section('content')

{{-- ════════════════════════════════════════════════════════
     HERO SECTION
════════════════════════════════════════════════════════ --}}
<section class="hero-bg relative min-h-screen flex flex-col justify-center overflow-hidden">

    {{-- Gold shine line top --}}
    <div class="hero-top-line absolute top-0 left-0 right-0 z-10"></div>

    {{-- Floating Emojis --}}
    <span class="float-emoji text-5xl" style="top: 12%; left: 5%; animation-delay: 0s;">🏪</span>
    <span class="float-emoji alt text-4xl" style="top: 22%; left: 88%; animation-delay: 1.2s;">🎉</span>
    <span class="float-emoji slow text-3xl" style="top: 65%; left: 8%; animation-delay: 0.5s;">🛍️</span>
    <span class="float-emoji fast text-4xl" style="top: 75%; left: 82%; animation-delay: 0.8s;">💸</span>
    <span class="float-emoji alt text-3xl" style="top: 40%; left: 92%; animation-delay: 2s;">✨</span>
    <span class="float-emoji slow text-4xl" style="top: 48%; left: 3%; animation-delay: 1.5s;">🔥</span>
    <span class="float-emoji text-3xl" style="top: 85%; left: 45%; animation-delay: 0.3s;">🎁</span>
    <span class="float-emoji alt text-2xl" style="top: 15%; left: 52%; animation-delay: 2.5s;">⭐</span>

    {{-- Decorative circles --}}
    <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full pointer-events-none"
         style="background: rgba(255,184,0,0.09);"></div>
    <div class="absolute -bottom-28 -left-28 w-80 h-80 rounded-full pointer-events-none"
         style="background: rgba(0,0,0,0.08);"></div>
    <div class="absolute top-1/3 left-1/2 w-60 h-60 rounded-full pointer-events-none"
         style="background: rgba(255,255,255,0.03); transform: translate(-50%,-50%);"></div>

    <div class="relative z-10 max-w-2xl mx-auto px-5 sm:px-8 py-20 sm:py-28 text-center">

        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full badge-pill text-xs sm:text-sm font-semibold mb-7 tracking-wide">
            🏅 Platform Promo UMKM #1 Indonesia
        </div>

        {{-- Wordmark --}}
        <h1 style="
            font-size: clamp(3.2rem, 12vw, 6rem);
            font-weight: 900;
            color: #FFB800;
            letter-spacing: 0.07em;
            line-height: 1;
            text-shadow: 0 4px 0 rgba(0,0,0,0.18), 0 8px 30px rgba(0,0,0,0.12);
            font-family: 'Poppins', sans-serif;
        ">PROMORA</h1>

        <div class="mx-auto mt-3 mb-6 rounded-full"
             style="width: 72px; height: 3px; background: linear-gradient(90deg, transparent, #FFB800, transparent);"></div>

        {{-- Welcome tagline --}}
        <p class="text-lg sm:text-xl font-600 mb-2" style="color: rgba(255,240,230,0.95); font-weight: 600;">
            Satu Platform, Satu Tujuan
        </p>
        <p class="text-sm sm:text-base leading-relaxed max-w-md mx-auto mb-10"
           style="color: rgba(255,225,210,0.80); font-weight: 400;">
            Temukan Semua Promo di Sekitarmu
        </p>

        {{-- CTA Buttons: Masuk & Daftar --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 mb-14">
            <a href="{{ route('consumer.login') }}"
               class="btn-masuk w-full sm:w-auto inline-flex items-center justify-center px-9 py-3.5 rounded-2xl text-base min-h-[48px]">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Masuk
            </a>
            <a href="{{ route('consumer.register') }}"
               class="btn-daftar w-full sm:w-auto inline-flex items-center justify-center px-9 py-3.5 rounded-2xl text-base min-h-[48px]">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Daftar Gratis
            </a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-3 max-w-sm mx-auto sm:max-w-md">
            <div class="stat-card py-4 px-2 text-center">
                <p class="text-2xl sm:text-3xl font-extrabold" style="color: #FFB800;">500+</p>
                <p class="text-xs mt-0.5" style="color: rgba(255,225,210,0.7);">Promo Aktif</p>
            </div>
            <div class="stat-card py-4 px-2 text-center">
                <p class="text-2xl sm:text-3xl font-extrabold" style="color: #FFB800;">200+</p>
                <p class="text-xs mt-0.5" style="color: rgba(255,225,210,0.7);">UMKM Terdaftar</p>
            </div>
            <div class="stat-card py-4 px-2 text-center">
                <p class="text-2xl sm:text-3xl font-extrabold" style="color: #FFB800;">10K+</p>
                <p class="text-xs mt-0.5" style="color: rgba(255,225,210,0.7);">Pengguna Aktif</p>
            </div>
        </div>

    </div>

    {{-- Gold shine line bottom --}}
    <div class="hero-top-line absolute bottom-0 left-0 right-0 z-10"></div>
</section>

{{-- Wave separator --}}
<div class="wave-sep" style="background: #DD3015;">
    <svg viewBox="0 0 1440 56" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" width="100%" height="56">
        <path d="M0 56 C360 0 1080 0 1440 56 L1440 56 L0 56 Z" fill="#F3E1E1"/>
    </svg>
</div>

{{-- ════════════════════════════════════════════════════════
     KATEGORI SECTION
════════════════════════════════════════════════════════ --}}
@if($categories->count() > 0)
<section class="py-12 sm:py-16" style="background: #F3E1E1;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-8">
            <h2 class="section-title">Jelajahi Kategori</h2>
            <p class="section-sub mt-1">Pilih kategori favorit Anda dan temukan promo terbaik</p>
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
            @foreach($categories as $category)
                <a href="{{ route('explore', ['category_id' => $category->id]) }}" class="cat-pill">
                    <span class="text-3xl leading-none" aria-hidden="true">{{ $category->icon ?? '🏷️' }}</span>
                    <span class="cat-label text-xs font-semibold text-center leading-tight transition-colors" style="color: #7A1A00;">
                        {{ $category->name }}
                    </span>
                    <span class="cat-count text-xs transition-colors" style="color: #b07070;">
                        {{ $category->promos_count ?? 0 }} promo
                    </span>
                </a>
            @endforeach
        </div>

    </div>
</section>
@endif


{{-- ════════════════════════════════════════════════════════
     CTA SECTION — untuk Guest
════════════════════════════════════════════════════════ --}}
@guest
<section class="cta-section py-16 sm:py-20">

    {{-- Decorative blobs --}}
    <div class="absolute -top-14 -right-14 w-64 h-64 rounded-full pointer-events-none"
         style="background: rgba(255,184,0,0.10);"></div>
    <div class="absolute -bottom-14 -left-14 w-64 h-64 rounded-full pointer-events-none"
         style="background: rgba(0,0,0,0.08);"></div>

    {{-- Gold shine line top --}}
    <div class="hero-top-line absolute top-0 left-0 right-0"></div>

    <div class="relative max-w-xl mx-auto px-5 sm:px-8 text-center">

        <div class="text-5xl mb-4">🏪</div>
        <h2 class="text-2xl sm:text-3xl font-extrabold mb-2" style="color: #FFB800; font-family: 'Poppins', sans-serif;">
            Punya Usaha Lokal?
        </h2>
        <p class="text-sm font-semibold mb-2" style="color: rgba(255,240,230,0.95);">Daftarkan Sekarang — Gratis!</p>
        <p class="text-sm mb-10 leading-relaxed" style="color: rgba(255,225,210,0.75);">
            Bergabung bersama ratusan UMKM yang sudah mempromosikan bisnis mereka di Promora.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('seller.register') }}"
               class="btn-masuk w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-2xl text-base min-h-[48px]">
                Daftarkan UMKM Saya
            </a>
            <a href="{{ route('consumer.register') }}"
               class="btn-daftar w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-2xl text-base min-h-[48px]">
                Daftar sebagai Konsumen
            </a>
        </div>
    </div>

    {{-- Gold shine line bottom --}}
    <div class="hero-top-line absolute bottom-0 left-0 right-0"></div>
</section>
@endguest

@endsection