@extends('layouts.app')

@section('title', 'Admin Login - Promora')

@section('content')
<div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">

        {{-- Header --}}
        <div class="text-center">
            <a href="{{ url('/') }}" class="inline-block text-3xl font-bold text-orange-500 min-h-[44px] leading-none pt-2.5">Promora</a>
            <h2 class="mt-4 text-2xl font-bold text-gray-800">Admin Panel</h2>
            <p class="mt-2 text-sm text-gray-500">Masuk dengan akun administrator</p>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
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
                           class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-transparent transition
                                  @error('email') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                           placeholder="admin@promora.id">
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
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-transparent transition"
                           placeholder="Masukkan kata sandi">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full bg-gray-800 hover:bg-gray-900 text-white font-semibold py-3 px-4 rounded-lg transition-colors min-h-[44px] focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Masuk ke Admin Panel
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-400">
            Akses terbatas untuk administrator Promora
        </p>
    </div>
</div>
@endsection
