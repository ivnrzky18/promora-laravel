@extends('layouts.app')

@section('title', 'Daftar sebagai Penjual - Promora')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background-color: #F3E1E1;">
    <div class="max-w-lg w-full space-y-8">

        {{-- Header --}}
        <div class="text-center">
            <a href="{{ url('/') }}" class="inline-block text-3xl font-bold min-h-[44px] leading-none pt-2.5" style="color: #DD3015;">Promora</a>
            <h2 class="mt-4 text-2xl font-bold text-gray-800">Daftarkan UMKM Anda</h2>
            <p class="mt-2 text-sm text-gray-500">
                Sudah punya akun?
                <a href="{{ route('seller.login') }}" class="inline-flex items-center font-medium min-h-[44px] px-1" style="color: #DD3015;">
                    Masuk di sini
                </a>
            </p>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl shadow-sm border p-8" style="border-color: #F3E1E1;">
            <form method="POST" action="{{ route('seller.register') }}" class="space-y-5">
                @csrf

                <h3 class="text-base font-semibold text-gray-700 border-b border-gray-100 pb-3">
                    Informasi Akun
                </h3>

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Pemilik <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           autocomplete="name"
                           class="w-full px-4 py-3 border rounded-lg focus:outline-none transition @error('name') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                           onfocus="this.style.borderColor='#DD3015';this.style.boxShadow='0 0 0 2px rgba(221,48,21,0.2)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                           placeholder="Nama lengkap pemilik usaha">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autocomplete="email"
                           class="w-full px-4 py-3 border rounded-lg focus:outline-none transition @error('email') border-red-400 bg-red-50 @else border-gray-300 @enderror"
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
                        Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           autocomplete="new-password"
                           class="w-full px-4 py-3 border rounded-lg focus:outline-none transition @error('password') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                           onfocus="this.style.borderColor='#DD3015';this.style.boxShadow='0 0 0 2px rgba(221,48,21,0.2)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                           placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           required
                           autocomplete="new-password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none transition"
                           onfocus="this.style.borderColor='#DD3015';this.style.boxShadow='0 0 0 2px rgba(221,48,21,0.2)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                           placeholder="Ulangi kata sandi">
                </div>

                <h3 class="text-base font-semibold text-gray-700 border-b border-gray-100 pb-3 pt-2">
                    Informasi Bisnis
                </h3>

                {{-- Business Name --}}
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Bisnis / UMKM <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="business_name"
                           name="business_name"
                           value="{{ old('business_name') }}"
                           required
                           class="w-full px-4 py-3 border rounded-lg focus:outline-none transition @error('business_name') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                           onfocus="this.style.borderColor='#DD3015';this.style.boxShadow='0 0 0 2px rgba(221,48,21,0.2)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                           placeholder="Nama usaha Anda">
                    @error('business_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Business Category --}}
                <div>
                    <label for="business_category" class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori Bisnis <span class="text-red-500">*</span>
                    </label>
                    <select id="business_category"
                            name="business_category"
                            required
                            class="w-full px-4 py-3 border rounded-lg focus:outline-none transition bg-white @error('business_category') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                            onfocus="this.style.borderColor='#DD3015';this.style.boxShadow='0 0 0 2px rgba(221,48,21,0.2)'"
                            onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                        <option value="" disabled {{ old('business_category') ? '' : 'selected' }}>Pilih kategori bisnis</option>
                        <option value="Kuliner" {{ old('business_category') === 'Kuliner' ? 'selected' : '' }}>🍜 Kuliner</option>
                        <option value="Fashion" {{ old('business_category') === 'Fashion' ? 'selected' : '' }}>👗 Fashion</option>
                        <option value="Jasa" {{ old('business_category') === 'Jasa' ? 'selected' : '' }}>🔧 Jasa</option>
                        <option value="Kesehatan" {{ old('business_category') === 'Kesehatan' ? 'selected' : '' }}>💊 Kesehatan</option>
                        <option value="Pendidikan" {{ old('business_category') === 'Pendidikan' ? 'selected' : '' }}>📚 Pendidikan</option>
                        <option value="Hiburan" {{ old('business_category') === 'Hiburan' ? 'selected' : '' }}>🎭 Hiburan</option>
                    </select>
                    @error('business_category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Address --}}
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat Usaha <span class="text-red-500">*</span>
                    </label>
                    <textarea id="address"
                              name="address"
                              required
                              rows="3"
                              class="w-full px-4 py-3 border rounded-lg focus:outline-none transition resize-none @error('address') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                              onfocus="this.style.borderColor='#DD3015';this.style.boxShadow='0 0 0 2px rgba(221,48,21,0.2)'"
                              onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                              placeholder="Alamat lengkap usaha Anda">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi Bisnis
                        <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="4"
                              class="w-full px-4 py-3 border rounded-lg focus:outline-none transition resize-none @error('description') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                              onfocus="this.style.borderColor='#DD3015';this.style.boxShadow='0 0 0 2px rgba(221,48,21,0.2)'"
                              onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                              placeholder="Ceritakan tentang bisnis Anda...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full text-white font-semibold py-3 px-4 rounded-lg transition-colors min-h-[44px] focus:outline-none"
                        style="background-color: #DD3015;"
                        onmouseover="this.style.backgroundColor='#F30000'"
                        onmouseout="this.style.backgroundColor='#DD3015'">
                    Daftarkan UMKM Saya
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500">
            Ingin bergabung sebagai konsumen?
            <a href="{{ route('consumer.register') }}" class="inline-flex items-center font-medium min-h-[44px] px-1" style="color: #DD3015;">
                Daftar di sini
            </a>
        </p>
    </div>
</div>
@endsection