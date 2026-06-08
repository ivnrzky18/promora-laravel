@extends('layouts.consumer')

@section('title', 'Bookmark Saya - Promora')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Bookmark Saya</h1>
    <p class="text-gray-500 mt-1 text-sm">Promo yang kamu simpan untuk nanti.</p>
</div>

@if($bookmarks->isEmpty())
    <div class="bg-white rounded-xl border border-dashed border-gray-200 p-12 text-center">
        <svg class="w-14 h-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
        </svg>
        <p class="text-gray-500 font-medium mb-1">Belum ada bookmark</p>
        <p class="text-gray-400 text-sm mb-4">Simpan promo favoritmu dengan menekan tombol bookmark.</p>
        <a href="{{ url('/explore') }}"
           class="inline-flex items-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors min-h-[44px]">
            Jelajahi Promo
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($bookmarks as $bookmark)
            @php $promo = $bookmark->promo; @endphp

            @if($promo)
                @php
                    $isUnavailable = $promo->trashed() || $promo->status === 'expired';
                @endphp

                <div class="relative">
                    <x-promo-card
                        :promo="$promo"
                        :isBookmarked="true"
                    />

                    {{-- "Tidak Tersedia" overlay for soft-deleted or expired promos --}}
                    @if($isUnavailable)
                        <div class="absolute inset-0 bg-gray-900 bg-opacity-60 rounded-xl flex items-center justify-center">
                            <div class="text-center">
                                <span class="inline-block bg-gray-700 text-white text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wide">
                                    Tidak Tersedia
                                </span>
                                @if($promo->trashed())
                                    <p class="text-gray-300 text-xs mt-1">Promo telah dihapus</p>
                                @else
                                    <p class="text-gray-300 text-xs mt-1">Promo telah berakhir</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
@endif

@endsection
