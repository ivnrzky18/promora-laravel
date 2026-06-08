@extends('layouts.consumer')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
        <p class="text-sm text-gray-500 mt-1">Pemberitahuan promo terbaru dari seller yang kamu ikuti</p>
    </div>

    {{-- Notifications List --}}
    @if($notifications->isEmpty())
        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum ada notifikasi</h3>
            <p class="text-sm text-gray-500">Ikuti seller favoritmu untuk mendapatkan notifikasi promo terbaru.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div
                    id="notification-{{ $notification->id }}"
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 transition-colors
                           {{ is_null($notification->read_at) ? 'bg-blue-50 border-blue-100' : '' }}"
                    x-data="{ read: {{ is_null($notification->read_at) ? 'false' : 'true' }} }"
                    :class="read ? 'bg-white border-gray-100' : 'bg-blue-50 border-blue-100'"
                >
                    <div class="flex items-start justify-between gap-4">
                        {{-- Icon --}}
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center"
                                 :class="read ? 'bg-gray-100' : 'bg-orange-100'">
                                <svg class="w-4 h-4" :class="read ? 'text-gray-400' : 'text-orange-500'"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ $notification->data['title'] ?? 'Notifikasi' }}
                            </p>
                            <p class="text-sm text-gray-600 mt-0.5">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Mark Read Button --}}
                        <div class="flex-shrink-0" x-show="!read">
                            <button
                                @click="
                                    fetch('{{ route('consumer.notifications.read', $notification->id) }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json'
                                        }
                                    })
                                    .then(r => r.json())
                                    .then(data => { if (data.success) read = true; })
                                "
                                class="text-xs text-orange-500 hover:text-orange-700 font-medium whitespace-nowrap
                                       min-h-[44px] px-2 flex items-center transition-colors"
                                type="button"
                            >
                                Tandai Dibaca
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
