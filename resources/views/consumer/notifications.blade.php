@extends('layouts.consumer')

@section('content')
<div class="min-h-screen bg-[#F3E1E1] py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-0">

        {{-- Page Header --}}
        <div class="mb-8 border-b border-[#DD3015]/10 pb-5">
            <h1 class="text-3xl font-black text-black uppercase tracking-tight">Notifikasi</h1>
            <p class="text-xs font-bold text-gray-500 mt-1 uppercase tracking-wider">
                Pemberitahuan promo terbaru dari seller yang kamu ikuti
            </p>
        </div>

        {{-- Notifications List --}}
        @if($notifications->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white rounded-[2rem] shadow-xl shadow-red-900/5 border border-gray-100 p-12 text-center relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-[#DD3015] via-black to-[#DD3015]"></div>
                
                <div class="flex justify-center mb-5">
                    <div class="w-16 h-16 bg-[#F3E1E1] rounded-full flex items-center justify-center border border-[#DD3015]/10">
                        <svg class="w-8 h-8 text-[#DD3015]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-black text-black uppercase tracking-tight mb-2">Belum ada notifikasi</h3>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide max-w-sm mx-auto leading-relaxed">
                    Ikuti seller favoritmu untuk mendapatkan informasi penawaran dan diskon terbaru secara langsung.
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($notifications as $notification)
                    <div
                        id="notification-{{ $notification->id }}"
                        x-data="{ read: {{ $notification->read_at ? 'true' : 'false' }} }"
                        class="rounded-2xl border p-4 transition-all duration-300 relative overflow-hidden shadow-sm bg-white"
                        :class="read 
                            ? 'border-gray-100 shadow-sm opacity-75' 
                            : 'border-[#DD3015]/30 shadow-md shadow-red-900/5'"
                    >
                        {{-- Indikator Belum Dibaca --}}
                        <div x-show="!read" class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#DD3015]"></div>

                        <div class="flex items-start justify-between gap-4" :class="!read ? 'pl-2' : ''">
                            
                            {{-- BAGIAN KIRI DAN TENGAH: DIBUNGKUS LINK JIKA ADA PROMO_ID --}}
                            @if(isset($notification->data['promo_id']))
                                <a href="{{ route('promos.show', $notification->data['promo_id']) }}" class="flex items-start gap-4 flex-1 min-w-0 group cursor-pointer">
                                    {{-- Icon Status --}}
                                    <div class="flex-shrink-0 mt-0.5">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors duration-300 border"
                                             :class="read ? 'bg-gray-50 border-gray-100' : 'bg-[#F3E1E1] border-[#DD3015]/10 group-hover:bg-[#DD3015]/10'">
                                            <svg class="w-5 h-5 transition-colors duration-300" 
                                                 :class="read ? 'text-gray-400' : 'text-[#DD3015]'"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    {{-- Content Notifikasi --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-black text-black uppercase tracking-tight transition-colors duration-200 group-hover:text-[#DD3015] group-hover:underline decoration-1" :class="!read ? 'text-[#DD3015]' : ''">
                                            {{ $notification->data['title'] ?? 'Notifikasi Baru' }}
                                        </p>
                                        <p class="text-sm font-medium text-gray-600 mt-1 leading-relaxed group-hover:text-gray-900">
                                            {{ $notification->data['message'] ?? '' }}
                                        </p>
                                        <div class="flex items-center space-x-2 mt-2">
                                            <span class="text-[10px] font-bold text-gray-400 bg-gray-50 border border-gray-100 px-2 py-0.5 rounded-md uppercase">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            <span class="text-[9px] font-extrabold text-[#DD3015] bg-[#F3E1E1] px-2 py-0.5 rounded-md uppercase tracking-wider border border-[#DD3015]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                Lihat Promo →
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @else
                                {{-- Fallback jika data notifikasi biasa / tidak memiliki promo_id --}}
                                <div class="flex items-start gap-4 flex-1 min-w-0">
                                    {{-- Icon Status --}}
                                    <div class="flex-shrink-0 mt-0.5">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors duration-300 border"
                                             :class="read ? 'bg-gray-50 border-gray-100' : 'bg-[#F3E1E1] border-[#DD3015]/10'">
                                            <svg class="w-5 h-5 transition-colors duration-300" 
                                                 :class="read ? 'text-gray-400' : 'text-[#DD3015]'"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                        </div>
                                    </div>

                                    {{-- Content Notifikasi --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-black text-black uppercase tracking-tight" :class="!read ? 'text-[#DD3015]' : ''">
                                            {{ $notification->data['title'] ?? 'Notifikasi Baru' }}
                                        </p>
                                        <p class="text-sm font-medium text-gray-600 mt-1 leading-relaxed">
                                            {{ $notification->data['message'] ?? '' }}
                                        </p>
                                        <div class="flex items-center space-x-2 mt-2">
                                            <span class="text-[10px] font-bold text-gray-400 bg-gray-50 border border-gray-100 px-2 py-0.5 rounded-md uppercase">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Tombol Aksi Mark as Read --}}
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
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error('Network response was not ok');
                                            }
                                            return response.json();
                                        })
                                        .then(data => { 
                                            if (data.success) {
                                                read = true; 
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                        });
                                    "
                                    class="text-[10px] font-black text-[#DD3015] hover:text-black uppercase tracking-widest bg-[#F3E1E1] hover:bg-gray-100 px-3 py-2 rounded-xl transition-all border border-[#DD3015]/10 min-h-[44px] flex items-center relative z-10"
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
</div>
@endsection