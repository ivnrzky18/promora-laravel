@extends('layouts.app')

@section('title', '500 - Terjadi Kesalahan Server | Promora')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
    <div class="max-w-md w-full text-center">

        {{-- Error Code --}}
        <div class="mb-6">
            <span class="text-8xl font-extrabold" style="color: #DD3015;">500</span>
        </div>

        {{-- Illustration Icon --}}
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 rounded-full flex items-center justify-center" style="background-color: #F3E1E1;">
                <svg class="w-10 h-10" style="color: #DD3015;" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-2xl font-bold text-gray-800 mb-3">
            Terjadi Kesalahan Server
        </h1>

        {{-- Message --}}
        <p class="text-gray-500 mb-8 leading-relaxed">
            Maaf, terjadi kesalahan pada server. Silakan coba lagi nanti. Jika masalah berlanjut, tim teknis kami sedang bekerja untuk memperbaikinya.
        </p>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            {{-- Retry / Reload button --}}
            <button onclick="window.location.reload()"
                    class="inline-flex items-center justify-center space-x-2 text-white font-semibold px-8 py-3 rounded-lg transition-colors min-h-[44px] w-full sm:w-auto"
                    style="background-color: #DD3015;"
                    onmouseover="this.style.backgroundColor='#F30000'"
                    onmouseout="this.style.backgroundColor='#DD3015'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Coba Lagi</span>
            </button>

            {{-- Back to Home Button --}}
            <a href="{{ url('/') }}"
               class="inline-flex items-center justify-center space-x-2 font-semibold px-8 py-3 rounded-lg border-2 transition-colors min-h-[44px] w-full sm:w-auto"
               style="border-color: #DD3015; color: #DD3015;"
               onmouseover="this.style.backgroundColor='#F3E1E1'"
               onmouseout="this.style.backgroundColor=''">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
        </div>

    </div>
</div>
@endsection
