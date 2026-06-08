@extends('layouts.seller')

@section('title', 'Event Saya - Promora')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Event Saya</h1>
            <p class="text-gray-500 mt-1">Kelola semua event bisnis Anda</p>
        </div>
        <a href="{{ route('seller.events.create') }}"
           class="inline-flex items-center justify-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors min-h-[44px] w-full sm:w-auto">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Event
        </a>
    </div>

    {{-- Events Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        @if($events->isEmpty())
            <div class="px-6 py-16 text-center">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-700 mb-1">Belum ada event</h3>
                <p class="text-gray-400 text-sm mb-4">Buat event untuk mengumumkan kegiatan bisnis Anda.</p>
                <a href="{{ route('seller.events.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors min-h-[44px]">
                    Buat Event Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left border-b border-gray-100">
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($events as $event)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($event->poster_image)
                                        <img src="{{ asset('storage/' . $event->poster_image) }}"
                                             alt="{{ $event->title }}"
                                             class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <p class="font-medium text-gray-800 truncate max-w-xs">{{ $event->title }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 truncate max-w-xs">{{ $event->location ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($event->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                @elseif($event->status === 'draft')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Draft</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $event->event_date->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('seller.events.edit', $event) }}"
                                       class="inline-flex items-center px-3 py-2 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors min-h-[44px]">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('seller.events.destroy', $event) }}"
                                          onsubmit="return confirm('Hapus event ini?')">
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
