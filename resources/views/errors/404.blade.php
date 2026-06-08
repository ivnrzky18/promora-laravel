@extends('layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan | Promora')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
    <div class="max-w-md w-full text-center">

        {{-- Error Code --}}
        <div class="mb-6">
            <span class="text-8xl font-extrabold" style="color: #DD3015;">404</span>
        </div>

        {{-- Illustration Icon --}}
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 rounded-full flex items-center justify-center" style="background-color: #F3E1E1;">
                <svg class="w-10 h-10" style="color: #DD3015;" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-2xl font-bold text-gray-800 mb-3">
            Halaman Tidak Ditemukan
        </h1>

        {{-- Message --}}
        <p class="text-gray-500 mb-8 leading-relaxed">
            Maaf, halaman yang Anda cari tidak ditemukan. Mungkin halaman telah dipindahkan, dihapus, atau URL yang Anda masukkan tidak tepat.
        </p>

        {{-- Back to Home Button --}}
        <a href="{{ url('/') }}"
           class="inline-flex items-center justify-center space-x-2 text-white font-semibold px-8 py-3 rounded-lg transition-colors min-h-[44px]"
           style="background-color: #DD3015;"
           onmouseover="this.style.backgroundColor='#F30000'"
           onmouseout="this.style.backgroundColor='#DD3015'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Kembali ke Beranda</span>
        </a>

        {{-- Helpful Links --}}
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-400 mb-3">Atau coba halaman berikut:</p>
            <div class="flex flex-wrap justify-center gap-3 text-sm">
                <a href="{{ url('/explore') }}"
                   class="text-gray-600 hover:underline min-h-[44px] flex items-center px-2 transition-colors"
                   style="" onmouseover="this.style.color='#DD3015'" onmouseout="this.style.color=''">
                    Jelajahi Promo
                </a>
                <span class="text-gray-300">•</span>
                <a href="{{ url('/hot-deals') }}"
                   class="text-gray-600 min-h-[44px] flex items-center px-2 transition-colors"
                   onmouseover="this.style.color='#DD3015'" onmouseout="this.style.color=''">
                    Hot Deals
                </a>
                <span class="text-gray-300 flex items-center">•</span>
                <a href="{{ url('/calendar') }}"
                   class="text-gray-600 min-h-[44px] flex items-center px-2 transition-colors"
                   onmouseover="this.style.color='#DD3015'" onmouseout="this.style.color=''">
                    Kalender
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
