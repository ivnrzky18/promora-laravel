@extends('layouts.admin')

@section('title', 'Edit Kategori - Promora Admin')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.categories.index') }}"
           class="text-gray-400 hover:text-gray-600 transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Kategori</h1>
            <p class="text-gray-500 mt-0.5 text-sm">Perbarui informasi kategori.</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6"
         x-data="{
             name: '{{ old('name', $category->name) }}',
             slug: '{{ old('slug', $category->slug) }}',
             slugManuallyEdited: true,
             generateSlug() {
                 if (!this.slugManuallyEdited) {
                     this.slug = this.name
                         .toLowerCase()
                         .replace(/[^a-z0-9\s-]/g, '')
                         .trim()
                         .replace(/\s+/g, '-');
                 }
             }
         }">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       x-model="name"
                       @input="generateSlug()"
                       placeholder="Contoh: Kuliner"
                       class="w-full px-4 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                              @error('name') border-red-400 @enderror"
                       required>
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug --}}
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                    Slug <span class="text-red-500">*</span>
                    <span class="text-xs text-gray-400 font-normal ml-1">(otomatis dari nama)</span>
                </label>
                <input type="text"
                       id="slug"
                       name="slug"
                       x-model="slug"
                       @input="slugManuallyEdited = true"
                       placeholder="contoh: kuliner"
                       class="w-full px-4 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                              @error('slug') border-red-400 @enderror"
                       required>
                @error('slug')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Icon --}}
            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">
                    Ikon (Emoji)
                    <span class="text-xs text-gray-400 font-normal ml-1">(opsional, maks. 10 karakter)</span>
                </label>
                <input type="text"
                       id="icon"
                       name="icon"
                       value="{{ old('icon', $category->icon) }}"
                       placeholder="🍜"
                       maxlength="10"
                       class="w-32 px-4 py-2.5 min-h-[44px] border border-gray-300 rounded-lg text-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                              @error('icon') border-red-400 @enderror">
                @error('icon')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center space-x-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors min-h-[44px]">
                    Perbarui Kategori
                </button>
                <a href="{{ route('admin.categories.index') }}"
                   class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors min-h-[44px] flex items-center">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
