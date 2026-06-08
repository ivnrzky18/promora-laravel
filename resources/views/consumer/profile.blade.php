@extends('layouts.consumer')

@section('title', 'Profil Saya - Promora')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Profil Saya</h1>
    <p class="text-gray-500 mt-1 text-sm">Kelola informasi akun kamu.</p>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <form method="POST" action="{{ route('consumer.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Avatar Section --}}
            <div class="flex items-center space-x-5 mb-6 pb-6 border-b border-gray-100">
                <div class="relative" x-data="{ preview: null }">
                    {{-- Current / Preview Avatar --}}
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200">
                        <template x-if="preview">
                            <img :src="preview" alt="Preview avatar" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!preview">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                     alt="{{ $user->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-orange-100">
                                    <span class="text-2xl font-bold text-orange-500">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </template>
                    </div>

                    {{-- Upload Button --}}
                    <label for="avatar"
                           class="absolute -bottom-1 -right-1 w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center cursor-pointer hover:bg-orange-600 transition-colors shadow-sm"
                           title="Ganti foto profil">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </label>
                    <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp"
                           class="hidden"
                           @change="
                               const file = $event.target.files[0];
                               if (file) {
                                   const reader = new FileReader();
                                   reader.onload = e => preview = e.target.result;
                                   reader.readAsDataURL(file);
                               }
                           ">
                </div>

                <div>
                    <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, atau WebP. Maks 2MB.</p>
                </div>
            </div>

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Name --}}
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent
                              @error('name') border-red-400 @enderror"
                       placeholder="Nama lengkap kamu"
                       required>
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent
                              @error('email') border-red-400 @enderror"
                       placeholder="email@contoh.com"
                       required>
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Location --}}
            <div class="mb-6">
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                    Lokasi
                </label>
                <input type="text" id="location" name="location"
                       value="{{ old('location', $user->location) }}"
                       class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent
                              @error('location') border-red-400 @enderror"
                       placeholder="Kota atau kecamatan kamu">
                @error('location')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('consumer.dashboard') }}"
                   class="px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors min-h-[44px] flex items-center">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors min-h-[44px]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
