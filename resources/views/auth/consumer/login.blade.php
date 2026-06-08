@extends('layouts.app')

@section('title', 'Masuk sebagai Konsumen - Promora')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background-color: #F3E1E1;">
    <div class="max-w-md w-full space-y-8">

        {{-- Header --}}
        <div class="text-center">
            <a href="{{ url('/') }}" class="inline-block text-3xl font-bold min-h-[44px] leading-none pt-2.5" style="color: #DD3015;">Promora</a>
            <h2 class="mt-4 text-2xl font-bold text-gray-800">Masuk sebagai Konsumen</h2>
            <p class="mt-2 text-sm text-gray-500">
                Belum punya akun?
                <a href="{{ route('consumer.register') }}" class="inline-flex items-center font-medium min-h-[44px] px-1" style="color: #DD3015;">
                    Daftar di sini
                </a>
            </p>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl shadow-sm border p-8" style="border-color: #F3E1E1;">
            <form method="POST" action="{{ route('consumer.login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat Email
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autocomplete="email"
                           class="w-full px-4 py-3 border rounded-lg focus:outline-none transition @error('email') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                           style="focus-ring-color: #DD3015;"
                           onfocus="this.style.borderColor='#DD3015';this.style.boxShadow='0 0 0 2px rgba(221,48,21,0.2)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                           placeholder="contoh@email.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Kata Sandi
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none transition"
                           onfocus="this.style.borderColor='#DD3015';this.style.boxShadow='0 0 0 2px rgba(221,48,21,0.2)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                           placeholder="Masukkan kata sandi">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full text-white font-semibold py-3 px-4 rounded-lg transition-colors min-h-[44px] focus:outline-none"
                        style="background-color: #DD3015;"
                        onmouseover="this.style.backgroundColor='#F30000'"
                        onmouseout="this.style.backgroundColor='#DD3015'">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500">
            Masuk sebagai penjual?
            <a href="{{ route('seller.login') }}" class="inline-flex items-center font-medium min-h-[44px] px-1" style="color: #DD3015;">
                Klik di sini
            </a>
        </p>
    </div>
</div>
@endsection