@extends('layouts.app')

@section('title', 'Admin Login - Promora')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden" style="background-color: #FFF5F4;">
    
    {{-- Elemen Dekoratif Latar Belakang (Meniru gaya Hero Header Promora) --}}
    <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full pointer-events-none" style="background: linear-gradient(135deg, rgba(232,40,26,0.05), rgba(255,77,61,0.05));"></div>
    <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full pointer-events-none" style="background: linear-gradient(135deg, rgba(255,140,105,0.06), rgba(232,40,26,0.03));"></div>

    <div class="max-w-md w-full space-y-6 relative z-10">

        {{-- Header / Logo --}}
        <div class="text-center">
            <a href="{{ url('/') }}" class="inline-block text-4xl font-black tracking-tight min-h-[44px] leading-none pt-2.5 transition-transform hover:scale-105" style="color: #E8281A;">
                Promora<span class="text-xs font-bold px-2 py-0.5 rounded-md ml-1 relative bottom-3 text-white" style="background: linear-gradient(135deg, #FF4D3D, #E8281A);">ADMIN</span>
            </a>
            <h2 class="mt-4 text-2xl font-extrabold text-gray-900 tracking-tight">Selamat Datang Kembali</h2>
            <p class="mt-1.5 text-sm text-gray-500">Silakan masuk untuk mengelola dashboard administrator</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-3xl p-8 border border-red-100/50" style="box-shadow: 0 10px 30px rgba(232, 40, 26, 0.05);">
            <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                @csrf

                {{-- Email Input --}}
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 transition duration-200
                                      @error('email') border-red-400 bg-red-50/50 focus:ring-red-300 @else border-gray-200 focus:ring-red-500/20 focus:border-[#E8281A] @enderror"
                               placeholder="admin@promora.id"
                               style="font-size: 14px;">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs font-medium text-red-600 flex items-center gap-1">
                            <span class="inline-block w-1 h-1 rounded-full bg-red-600"></span> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password Input --}}
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-[#E8281A] transition duration-200"
                           placeholder="••••••••"
                           style="font-size: 14px;">
                    @error('password')
                        <p class="mt-1.5 text-xs font-medium text-red-600 flex items-center gap-1">
                            <span class="inline-block w-1 h-1 rounded-full bg-red-600"></span> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Submit Button dengan Efek Gradasi Hero Promora --}}
                <div class="pt-2">
                    <button type="submit"
                            class="w-full text-white font-bold py-3.5 px-4 rounded-xl transition duration-200 min-h-[44px] focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:ring-offset-2 transform active:scale-[0.98] shadow-md hover:shadow-xl hover:opacity-95"
                            style="background: linear-gradient(135deg, #E8281A 0%, #FF4D3D 100%); font-size: 14px;">
                        Masuk ke Admin Panel
                    </button>
                </div>
            </form>
        </div>

        {{-- Footer teks --}}
        <p class="text-center text-xs font-medium text-gray-400">
            🔒 Proteksi Berlapis &bull; Akses Terbatas Administrator
        </p>
    </div>
</div>
@endsection