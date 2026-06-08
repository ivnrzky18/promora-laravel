@extends('layouts.app')

@section('title', '403 - Akses Ditolak | Promora')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
    <div class="max-w-md w-full text-center">

        {{-- Error Code --}}
        <div class="mb-6">
            <span class="text-8xl font-extrabold" style="color: #DD3015;">403</span>
        </div>

        {{-- Illustration Icon --}}
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 rounded-full flex items-center justify-center" style="background-color: #F3E1E1;">
                <svg class="w-10 h-10" style="color: #DD3015;" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-2xl font-bold text-gray-800 mb-3">
            Akses Ditolak
        </h1>

        {{-- Message --}}
        <p class="text-gray-500 mb-8 leading-relaxed">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Pastikan Anda telah masuk dengan akun yang sesuai atau hubungi administrator jika Anda merasa ini adalah kesalahan.
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

        {{-- Login suggestion for guests --}}
        @guest
        <div class="mt-4">
            <a href="{{ route('consumer.login') }}"
               class="inline-flex items-center justify-center space-x-2 font-semibold px-8 py-3 rounded-lg border-2 transition-colors min-h-[44px]"
               style="border-color: #DD3015; color: #DD3015;"
               onmouseover="this.style.backgroundColor='#F3E1E1'"
               onmouseout="this.style.backgroundColor=''">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span>Masuk ke Akun Anda</span>
            </a>
        </div>
        @endguest

    </div>
</div>
@endsection
