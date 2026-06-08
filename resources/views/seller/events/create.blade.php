@extends('layouts.seller')

@section('title', 'Tambah Event - Promora')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center space-x-3">
        <a href="{{ route('seller.events.index') }}"
           class="text-gray-400 hover:text-gray-600 transition-colors min-h-[44px] flex items-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Event</h1>
            <p class="text-gray-500 mt-0.5 text-sm">Buat event baru untuk bisnis Anda</p>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('seller.events.store') }}" enctype="multipart/form-data"
          x-data="{ imagePreview: null }">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-100">

            {{-- Basic Info --}}
            <div class="p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-700">Informasi Event</h2>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Judul Event <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}"
                           class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('title') border-red-400 @enderror"
                           placeholder="Contoh: Grand Opening Toko Baru">
                    @error('title')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('description') border-red-400 @enderror"
                              placeholder="Jelaskan detail event Anda...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Location --}}
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}"
                           class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('location') border-red-400 @enderror"
                           placeholder="Contoh: Jl. Sudirman No. 1, Jakarta">
                    @error('location')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Poster Image --}}
            <div class="p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-700">Poster Event</h2>

                <div>
                    <div x-show="imagePreview" class="mb-3">
                        <img :src="imagePreview" alt="Preview" class="w-full max-w-sm h-48 object-cover rounded-lg border border-gray-200">
                    </div>

                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-orange-400 hover:bg-orange-50 transition-colors"
                           x-show="!imagePreview">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500">Klik untuk upload gambar</p>
                            <p class="text-xs text-gray-400 mt-1">JPEG, PNG, WebP — Maks. 2MB</p>
                        </div>
                        <input type="file" name="poster_image" accept="image/jpeg,image/png,image/webp" class="hidden"
                               @change="imagePreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                    </label>

                    <button type="button" x-show="imagePreview" @click="imagePreview = null"
                            class="mt-2 text-xs text-red-500 hover:text-red-600 min-h-[44px] px-2 py-1">
                        Hapus gambar
                    </button>

                    @error('poster_image')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Dates --}}
            <div class="p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-700">Jadwal Event</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="event_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" id="event_date" name="event_date" value="{{ old('event_date') }}"
                               class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('event_date') border-red-400 @enderror">
                        @error('event_date')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berakhir</label>
                        <input type="datetime-local" id="end_date" name="end_date" value="{{ old('end_date') }}"
                               class="w-full px-3 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('end_date') border-red-400 @enderror">
                        @error('end_date')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="p-6 flex items-center justify-end space-x-3">
                <a href="{{ route('seller.events.index') }}"
                   class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors min-h-[44px] flex items-center">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition-colors min-h-[44px]">
                    Simpan Event
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
