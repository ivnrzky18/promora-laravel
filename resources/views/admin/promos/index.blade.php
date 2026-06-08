@extends('layouts.admin')

@section('title', 'Moderasi Promo - Promora Admin')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Moderasi Promo</h1>
        <p class="text-gray-500 mt-1">Tinjau dan setujui promo yang dikirimkan oleh seller.</p>
    </div>

    {{-- Promos Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Promo Menunggu Persetujuan</h2>
            @if($promos->isNotEmpty())
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    {{ $promos->count() }} draft
                </span>
            @endif
        </div>

        @if($promos->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-3 text-sm font-medium text-gray-700">Tidak ada promo yang menunggu persetujuan</h3>
                <p class="mt-1 text-sm text-gray-400">Semua promo sudah dimoderasi.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Judul Promo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Seller
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kategori
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Tanggal Dibuat
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($promos as $promo)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        @if($promo->poster_image)
                                            <img src="{{ Storage::url($promo->poster_image) }}"
                                                 alt="{{ $promo->title }}"
                                                 class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">{{ $promo->title }}</p>
                                            @if($promo->discount_percentage)
                                                <p class="text-xs text-orange-500 font-medium">{{ $promo->discount_percentage }}% off</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $promo->seller->business_name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $promo->category->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $promo->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        {{-- Setujui --}}
                                        <form method="POST"
                                              action="{{ route('admin.promos.approve', $promo) }}"
                                              onsubmit="return confirm('Setujui promo ini?')">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-2 bg-green-500 text-white text-xs font-medium rounded-lg hover:bg-green-600 transition-colors min-h-[44px]">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Setujui
                                            </button>
                                        </form>

                                        {{-- Tolak --}}
                                        <form method="POST"
                                              action="{{ route('admin.promos.reject', $promo) }}"
                                              onsubmit="return confirm('Tolak dan hapus promo ini? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-2 bg-red-500 text-white text-xs font-medium rounded-lg hover:bg-red-600 transition-colors min-h-[44px]">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Tolak
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
