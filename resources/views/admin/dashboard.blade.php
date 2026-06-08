@extends('layouts.admin')

@section('title', 'Dashboard Admin - Promora')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
        <p class="text-gray-500 mt-1">Kelola verifikasi seller yang menunggu persetujuan.</p>
    </div>

    {{-- Pending Sellers Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Seller Menunggu Verifikasi</h2>
            @if($pendingSellers->isNotEmpty())
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    {{ $pendingSellers->count() }} pending
                </span>
            @endif
        </div>

        @if($pendingSellers->isEmpty())
            {{-- Empty State --}}
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-3 text-sm font-medium text-gray-700">Tidak ada seller yang menunggu verifikasi</h3>
                <p class="mt-1 text-sm text-gray-400">Semua seller sudah diverifikasi.</p>
            </div>
        @else
            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nama Bisnis
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kategori Bisnis
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Alamat
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Tanggal Daftar
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($pendingSellers as $seller)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        @if($seller->logo)
                                            <img src="{{ Storage::url($seller->logo) }}"
                                                 alt="{{ $seller->business_name }}"
                                                 class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-red-600 font-semibold text-sm">
                                                    {{ strtoupper(substr($seller->business_name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">{{ $seller->business_name }}</p>
                                            <p class="text-xs text-gray-400">{{ $seller->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $seller->business_category }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                    {{ $seller->address }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $seller->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        {{-- Setujui --}}
                                        <form method="POST"
                                              action="{{ route('admin.sellers.verify', $seller) }}"
                                              onsubmit="return confirm('Setujui seller {{ addslashes($seller->business_name) }}?')">
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
                                              action="{{ route('admin.sellers.reject', $seller) }}"
                                              onsubmit="return confirm('Tolak dan hapus seller {{ addslashes($seller->business_name) }}? Tindakan ini tidak dapat dibatalkan.')">
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
