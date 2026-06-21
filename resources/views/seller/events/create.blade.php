@extends('layouts.seller')

@section('title', 'Tambah Event - Promora')

@section('content')
<div class="min-h-screen p-4 sm:p-6" style="background-color: #F3E1E1;">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center space-x-3 bg-white p-4 rounded-2xl shadow-sm border border-red-100">
            <a href="{{ route('seller.events.index') }}"
               class="text-gray-400 hover:text-[#DD3015] transition-colors min-h-[44px] flex items-center p-2 rounded-lg hover:bg-red-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-black">Tambah Event</h1>
                <p class="text-gray-500 mt-0.5 text-sm">Buat event baru yang menarik untuk bisnis Anda</p>
            </div>
        </div>

        <form method="POST"
              action="{{ route('seller.events.store') }}"
              enctype="multipart/form-data"
              x-data="{
                    imagePreview: null,
                    title: @js(old('title')),
                    description: @js(old('description')),
                    location: @js(old('location')),
                    eventDate: @js(old('event_date')),
                    endDate: @js(old('end_date')),
                    isPremium: {{ old('is_premium') ? 'true' : 'false' }},
                    premiumPrice: @js(old('premium_price', 20000))
              }">
            @csrf

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                {{-- LEFT COLUMN --}}
                <div class="xl:col-span-2">
                    <div class="bg-white rounded-2xl shadow-md border border-red-100 overflow-hidden divide-y divide-gray-100">

                        {{-- INFORMASI EVENT --}}
                        <div class="p-6 space-y-5">
                            <div class="flex items-center space-x-2 pb-2 border-b border-gray-100">
                                <div class="w-2 h-5 bg-[#DD3015] rounded-full"></div>
                                <h2 class="text-base font-bold text-black">Informasi Event</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label for="title" class="block text-sm font-semibold text-black mb-1">
                                        Judul Event <span class="text-[#F30000]">*</span>
                                    </label>
                                    <input type="text"
                                           id="title"
                                           name="title"
                                           x-model="title"
                                           class="w-full px-4 py-3 min-h-[48px] border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('title') border-[#F30000] ring-1 ring-[#F30000] @enderror"
                                           placeholder="Contoh: Grand Opening Toko Baru">
                                    @error('title')
                                        <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-semibold text-black mb-1">
                                        Deskripsi Event
                                    </label>
                                    <textarea id="description"
                                              name="description"
                                              rows="5"
                                              x-model="description"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('description') border-[#F30000] ring-1 ring-[#F30000] @enderror"
                                              placeholder="Jelaskan detail event Anda, apa yang akan terjadi, siapa yang bisa hadir, dll.">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="location" class="block text-sm font-semibold text-black mb-1">
                                        Lokasi Event
                                    </label>
                                    <input type="text"
                                           id="location"
                                           name="location"
                                           x-model="location"
                                           class="w-full px-4 py-3 min-h-[48px] border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('location') border-[#F30000] ring-1 ring-[#F30000] @enderror"
                                           placeholder="Contoh: Jl. Sudirman No. 1, Pekanbaru">
                                    @error('location')
                                        <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- TANGGAL EVENT --}}
                        <div class="p-6 space-y-5">
                            <div class="flex items-center space-x-2 pb-2 border-b border-gray-100">
                                <div class="w-2 h-5 bg-[#DD3015] rounded-full"></div>
                                <h2 class="text-base font-bold text-black">Tanggal Event</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="event_date" class="block text-sm font-semibold text-black mb-1">
                                        Tanggal Mulai <span class="text-[#F30000]">*</span>
                                    </label>
                                    <input type="date"
                                           id="event_date"
                                           name="event_date"
                                           x-model="eventDate"
                                           class="w-full px-4 py-3 min-h-[48px] border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('event_date') border-[#F30000] @enderror">
                                    @error('event_date')
                                        <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="end_date" class="block text-sm font-semibold text-black mb-1">
                                        Tanggal Selesai
                                    </label>
                                    <input type="date"
                                           id="end_date"
                                           name="end_date"
                                           x-model="endDate"
                                           class="w-full px-4 py-3 min-h-[48px] border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#DD3015] focus:border-transparent @error('end_date') border-[#F30000] @enderror">
                                    @error('end_date')
                                        <p class="mt-1 text-xs font-medium text-[#F30000]">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="p-3 rounded-xl border border-red-100 bg-red-50 text-sm text-[#DD3015]">
                                Pastikan tanggal event sudah sesuai agar pengunjung bisa melihat jadwal event dengan jelas.
                            </div>
                        </div>

                        {{-- ACTION BUTTONS MOBILE --}}
                        <div class="p-6 bg-gray-50 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 xl:hidden">
                            <a href="{{ route('seller.events.index') }}"
                               class="px-5 py-3 text-sm font-bold text-black bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-colors min-h-[48px] flex items-center justify-center shadow-sm">
                                Batal
                            </a>
                            <button type="submit"
                                    class="px-6 py-3 text-sm font-bold text-white bg-[#DD3015] rounded-xl hover:bg-[#F30000] focus:ring-4 focus:ring-[#FFB800]/50 transition-all duration-150 min-h-[48px] shadow-md hover:shadow-lg">
                                Simpan Event
                            </button>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="xl:col-span-1 space-y-6">

                    {{-- TIPS --}}
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-[#DD3015] shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a7 7 0 00-4 12.75V17a1 1 0 001 1h6a1 1 0 001-1v-2.25A7 7 0 0012 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-black text-sm">Tips membuat event menarik</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Gunakan judul yang jelas, deskripsi singkat, dan poster berkualitas agar event lebih menarik.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- POSTER EVENT --}}
                    <div class="bg-white rounded-2xl shadow-md border border-red-100 overflow-hidden">
                        <div class="p-6 space-y-4">
                            <div class="flex items-center space-x-2 pb-2 border-b border-gray-100">
                                <div class="w-2 h-5 bg-[#DD3015] rounded-full"></div>
                                <h2 class="text-base font-bold text-black">Poster Event</h2>
                            </div>

                            <template x-if="imagePreview">
                                <div class="relative rounded-2xl overflow-hidden border border-gray-200">
                                    <img :src="imagePreview" alt="Preview" class="w-full h-56 object-cover">
                                    <button type="button"
                                            @click="imagePreview = null; $refs.posterInput.value = ''"
                                            class="absolute top-3 right-3 px-3 py-1.5 bg-white/90 hover:bg-white text-[#F30000] text-xs font-bold rounded-lg shadow">
                                        Hapus
                                    </button>
                                </div>
                            </template>

                            <label x-show="!imagePreview"
                                   class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-red-200 rounded-2xl cursor-pointer hover:border-[#DD3015] hover:bg-red-50/40 transition-all duration-200">
                                <div class="flex flex-col items-center justify-center px-6 text-center">
                                    <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-[#DD3015]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-black">Upload Poster Event</p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP (Maks. 2MB)</p>

                                    <span class="mt-4 inline-flex items-center px-4 py-2 rounded-xl bg-[#DD3015] text-white text-sm font-bold shadow-sm">
                                        Pilih Gambar
                                    </span>
                                </div>

                                <input x-ref="posterInput"
                                       type="file"
                                       name="poster_image"
                                       accept="image/jpeg,image/png,image/webp"
                                       class="hidden"
                                       @change="imagePreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                            </label>

                            @error('poster_image')
                                <p class="text-xs font-medium text-[#F30000]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- PREMIUM --}}
                    <div class="bg-white rounded-2xl shadow-md border border-yellow-200 overflow-hidden">
                        <div class="p-6 space-y-4">
                            <div class="flex items-center space-x-2 pb-2 border-b border-yellow-100">
                                <div class="w-2 h-5 bg-[#FFB800] rounded-full"></div>
                                <h2 class="text-base font-bold text-black">Premium Listing</h2>
                            </div>

                            <label class="flex items-start gap-3 p-4 border border-yellow-300 bg-yellow-50 rounded-2xl cursor-pointer hover:bg-yellow-100/60 transition-colors">
                                <input type="checkbox"
                                       name="is_premium"
                                       value="1"
                                       x-model="isPremium"
                                       class="mt-1 w-5 h-5 rounded border-gray-300 text-[#FFB800] focus:ring-[#FFB800]">

                                <div class="flex-1">
                                    <p class="font-bold text-black flex items-center gap-2 flex-wrap">
                                        Aktifkan Premium Listing
                                        <span class="px-2 py-1 text-xs bg-yellow-400 text-white rounded-full">
                                            ⭐ Premium
                                        </span>
                                    </p>

                                    <p class="text-sm text-gray-600 mt-1">
                                        Event akan diprioritaskan tampil di halaman consumer / kalender event.
                                    </p>

                                    <div class="mt-4 pt-4 border-t border-yellow-200">
                                        <p class="text-[#DD3015] font-bold text-lg">Rp20.000 / Event</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Saat ini langsung aktif sebagai premium ketika dicentang.
                                        </p>
                                    </div>
                                </div>
                            </label>

                            <input type="hidden" name="premium_price" :value="isPremium ? premiumPrice : ''">
                        </div>
                    </div>

                    {{-- PREVIEW --}}
                    <div class="bg-white rounded-2xl shadow-md border border-red-100 overflow-hidden">
                        <div class="p-6 space-y-4">
                            <div class="flex items-center space-x-2 pb-2 border-b border-gray-100">
                                <div class="w-2 h-5 bg-[#DD3015] rounded-full"></div>
                                <h2 class="text-base font-bold text-black">Preview Event</h2>
                            </div>

                            <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm bg-white">
                                <div class="relative h-44 bg-gray-100">
                                    <template x-if="imagePreview">
                                        <img :src="imagePreview" class="w-full h-full object-cover" alt="Preview Event">
                                    </template>

                                    <template x-if="!imagePreview">
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-50 to-orange-50">
                                            <svg class="w-12 h-12 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </template>

                                    <template x-if="isPremium">
                                        <div class="absolute top-3 left-3 bg-yellow-400 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                                            ⭐ Premium
                                        </div>
                                    </template>
                                </div>

                                <div class="p-4">
                                    <h3 class="font-bold text-black text-base line-clamp-2"
                                        x-text="title || 'Judul event akan tampil di sini'"></h3>

                                    <p class="text-sm text-gray-500 mt-2 line-clamp-2"
                                       x-text="description || 'Deskripsi singkat event akan muncul di preview ini.'"></p>

                                    <div class="mt-4 space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#DD3015]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span x-text="eventDate ? eventDate + (endDate ? ' s/d ' + endDate : '') : 'Tanggal event'"></span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#DD3015]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span x-text="location || 'Lokasi event'"></span>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex items-center justify-between">
                                        <span class="text-sm font-bold text-[#DD3015]">Event</span>

                                        <template x-if="isPremium">
                                            <span class="text-xs px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700 font-semibold">
                                                Premium
                                            </span>
                                        </template>

                                        <template x-if="!isPremium">
                                            <span class="text-xs px-2.5 py-1 rounded-full bg-red-50 text-[#DD3015] font-semibold">
                                                Regular
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS DESKTOP --}}
                    <div class="hidden xl:flex items-center justify-end gap-3">
                        <a href="{{ route('seller.events.index') }}"
                           class="px-5 py-3 text-sm font-bold text-black bg-white border border-gray-300 rounded-xl hover:bg-gray-100 transition-colors min-h-[48px] flex items-center shadow-sm">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-3 text-sm font-bold text-white bg-[#DD3015] rounded-xl hover:bg-[#F30000] focus:ring-4 focus:ring-[#FFB800]/50 transition-all duration-150 min-h-[48px] shadow-md hover:shadow-lg">
                            Simpan Event
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection