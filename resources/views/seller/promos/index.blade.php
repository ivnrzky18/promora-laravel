@extends('layouts.seller')

@section('title', 'Promo Saya - Promora')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Promo Saya</h1>
            <p class="text-gray-500 mt-1">Kelola semua promo bisnis Anda</p>
        </div>
        <a href="{{ route('seller.promos.create') }}"
           class="inline-flex items-center justify-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors min-h-[44px] w-full sm:w-auto">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Promo
        </a>
    </div>

    {{-- Promos Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        @if($promos->isEmpty())
            <div class="px-6 py-16 text-center">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-700 mb-1">Belum ada promo</h3>
                <p class="text-gray-400 text-sm mb-4">Mulai buat promo pertama Anda untuk menarik lebih banyak pelanggan.</p>
                <a href="{{ route('seller.promos.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors min-h-[44px]">
                    Buat Promo Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left border-b border-gray-100">
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Promo</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Diskon</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Berakhir</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tayangan</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($promos as $promo)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($promo->poster_image)
                                        <img src="{{ asset('storage/' . $promo->poster_image) }}"
                                             alt="{{ $promo->title }}"
                                             class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <p class="font-medium text-gray-800 truncate max-w-xs">{{ $promo->title }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $promo->category->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($promo->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                @elseif($promo->status === 'draft')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Draft</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Kadaluarsa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $promo->discount_percentage ? $promo->discount_percentage . '%' : '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $promo->end_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ number_format($promo->view_count) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('seller.promos.edit', $promo) }}"
                                       class="inline-flex items-center px-3 py-2 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors min-h-[44px]">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('seller.promos.destroy', $promo) }}"
                                          onsubmit="return confirm('Hapus promo ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-2 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors min-h-[44px]">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
