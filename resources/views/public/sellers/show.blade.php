@extends('layouts.app')

@section('title', $sellerProfile->business_name . ' - Promora')

@section('content')
<div class="min-h-screen bg-[#F3E1E1] py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- ================= HEADER SELLER ================= --}}
        <div class="bg-white rounded-[2rem] shadow-xl shadow-red-900/5 border border-[#DD3015]/5 p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-[#DD3015] via-black to-[#DD3015]"></div>

            <div class="flex flex-col md:flex-row md:items-start md:space-x-8 space-y-6 md:space-y-0 pt-2">

                {{-- Logo --}}
                <div class="flex-shrink-0 flex justify-center md:justify-start">
                    @if($sellerProfile->logo)
                        <div class="p-1 rounded-full bg-white shadow-md border-2 border-[#DD3015]/20">
                            <img src="{{ asset('storage/' . $sellerProfile->logo) }}"
                                 alt="{{ $sellerProfile->business_name }}"
                                 class="w-28 h-28 rounded-full object-cover">
                        </div>
                    @else
                        <div class="w-28 h-28 rounded-full bg-[#F3E1E1] flex items-center justify-center border-4 border-white shadow-md">
                            <span class="text-4xl font-black text-[#DD3015]">
                                {{ strtoupper(substr($sellerProfile->business_name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Info seller --}}
                <div class="flex-1 text-center md:text-left flex flex-col justify-between min-h-[112px]">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <span class="inline-block text-[10px] font-black tracking-widest text-[#DD3015] bg-[#F3E1E1] px-2.5 py-1 rounded-md uppercase mb-2">
                                {{ $sellerProfile->business_category ?? 'MITRA UMKM' }}
                            </span>

                            <h1 class="text-3xl font-black text-black tracking-tight uppercase">
                                {{ $sellerProfile->business_name }}
                            </h1>

                            <p class="text-xs font-bold text-gray-500 mt-2 flex items-center justify-center md:justify-start gap-1.5">
                                <svg class="w-4 h-4 text-[#DD3015] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>{{ $sellerProfile->address }}</span>
                            </p>

                            {{-- rating --}}
                            <div class="flex items-center justify-center md:justify-start gap-2 mt-3 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100 w-fit mx-auto md:mx-0">
                                <x-star-rating :value="$averageRating" :readonly="true" />
                                <span class="text-xs font-black text-black">
                                    {{ number_format($averageRating, 1) }}
                                    <span class="text-gray-400 font-bold">({{ $reviews->count() }} Ulasan)</span>
                                </span>
                            </div>
                        </div>

                        {{-- tombol aksi --}}
                        <div class="flex flex-wrap justify-center md:justify-end gap-2.5">
                            @auth
                                @if(auth()->user()->role === 'consumer')
                                    <div x-data="{
                                        subscribed: {{ $isSubscribed ? 'true' : 'false' }},
                                        loading: false,
                                        toggle() {
                                            if (this.loading) return;
                                            this.loading = true;
                                            fetch('{{ route('consumer.subscriptions.toggle', $sellerProfile) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                    'Accept': 'application/json',
                                                    'Content-Type': 'application/json'
                                                }
                                            })
                                            .then(r => r.json())
                                            .then(data => { this.subscribed = data.subscribed; })
                                            .catch(() => {})
                                            .finally(() => { this.loading = false; });
                                        }
                                    }">
                                        <button @click="toggle()"
                                                :disabled="loading"
                                                class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition-all min-h-[44px]"
                                                :class="subscribed ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-[#DD3015] text-white hover:bg-black shadow-md shadow-red-600/10'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                            <span x-text="subscribed ? 'Diikuti' : 'Ikuti Seller'"></span>
                                        </button>
                                    </div>
                                @endif
                            @else
                                <a href="{{ route('consumer.login') }}"
                                   class="flex items-center gap-2 bg-[#DD3015] hover:bg-black text-white px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition-all min-h-[44px] shadow-md shadow-red-600/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <span>Ikuti Seller</span>
                                </a>
                            @endauth

                            {{-- share --}}
                            <button onclick="
                                    navigator.clipboard.writeText(window.location.href)
                                        .then(() => alert('Link profil berhasil disalin!'))
                                        .catch(() => alert('Gagal menyalin link.'));
                                "
                                class="flex items-center gap-2 bg-white border border-gray-200 text-black hover:border-black px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition-all min-h-[44px]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                                <span>Bagikan</span>
                            </button>
                        </div>
                    </div>

                    @if($sellerProfile->description)
                        <p class="text-sm text-gray-600 mt-4 pt-4 border-t border-gray-100 leading-relaxed text-left">
                            {{ $sellerProfile->description }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

    

        {{-- ================= EVENT SELLER ================= --}}
        <div>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-black text-black uppercase tracking-tight">
                    EVENT SELLER
                </h2>
                <span class="text-xs font-black bg-black text-white px-3 py-1 rounded-full">
                    {{ $events->count() }} EVENT
                </span>
            </div>

            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($events as $event)
                        <div class="bg-white rounded-[1.75rem] overflow-hidden shadow-lg border border-gray-100">
                            <div class="relative">
                                @if(isset($event->is_premium) && $event->is_premium)
                                    <span class="absolute top-4 left-4 bg-[#FFB800] text-black text-[10px] font-black px-3 py-1 rounded-full shadow z-10">
                                        PREMIUM
                                    </span>
                                @endif

                                @if($event->poster_image)
                                    <img src="{{ asset('storage/' . $event->poster_image) }}"
                                         alt="{{ $event->title }}"
                                         class="w-full h-52 object-cover">
                                @else
                                    <div class="w-full h-52 bg-gradient-to-br from-[#F3E1E1] to-[#F8F4F4] flex items-center justify-center">
                                        <span class="text-6xl">🎊</span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-5 space-y-3">
                                <div>
                                    <h3 class="text-lg font-black text-black leading-tight">
                                        {{ $event->title }}
                                    </h3>
                                    <p class="text-xs font-bold text-[#DD3015] mt-1">
                                        {{ $sellerProfile->business_name }}
                                    </p>
                                </div>

                                <p class="text-sm text-gray-600 leading-relaxed">
                                    {{ \Illuminate\Support\Str::limit($event->description, 120) }}
                                </p>

                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center gap-2 text-gray-700">
                                        <svg class="w-4 h-4 text-[#FFB800]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="font-semibold">
                                            {{ $event->event_date ? $event->event_date->format('d M Y, H:i') : '-' }}
                                        </span>
                                    </div>

                                    @if($event->end_date)
                                        <div class="flex items-center gap-2 text-gray-700">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>Sampai {{ $event->end_date->format('d M Y, H:i') }}</span>
                                        </div>
                                    @endif

                                    <div class="flex items-start gap-2 text-gray-700">
                                        <svg class="w-4 h-4 text-[#DD3015] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $event->location ?: $sellerProfile->address }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl border border-gray-100 p-12 text-center shadow-sm">
                    <div class="w-12 h-12 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl">📅</span>
                    </div>
                    <p class="text-gray-400 text-sm font-bold">UMKM ini belum memiliki event aktif saat ini.</p>
                </div>
            @endif
        </div>

        {{-- ================= PROMO AKTIF ================= --}}
        <div>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-black text-black uppercase tracking-tight">
                    PROMO AKTIF
                </h2>
                <span class="text-xs font-black bg-black text-white px-3 py-1 rounded-full">
                    {{ $promos->count() }} POS
                </span>
            </div>

            @if($promos->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($promos as $promo)
                        @php
                            $isBookmarked = auth()->check()
                                ? $promo->bookmarks()->where('user_id', auth()->id())->exists()
                                : false;
                        @endphp
                        <x-promo-card :promo="$promo" :isBookmarked="$isBookmarked" />
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl border border-gray-100 p-12 text-center shadow-sm">
                    <div class="w-12 h-12 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </div>
                    <p class="text-gray-400 text-sm font-bold">UMKM ini belum meluncurkan promo aktif saat ini.</p>
                </div>
            @endif
        </div>

        {{-- ================= FORM REVIEW ================= --}}
        @auth
            @if(auth()->user()->role === 'consumer')
                <div class="bg-white rounded-[2rem] shadow-xl shadow-red-900/5 border border-gray-100 p-6 sm:p-8">
                    <h2 class="text-xl font-black text-black uppercase tracking-tight mb-4">TULIS ULASAN ANDA</h2>

                    @if($errors->has('review'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-xs font-bold uppercase">
                            {{ $errors->first('review') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('reviews.store') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="seller_id" value="{{ $sellerProfile->id }}">

                        <div>
                            <label class="block text-xs font-black text-black uppercase tracking-wider mb-2">Beri Nilai Kepuasan</label>
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 w-fit">
                                <x-star-rating name="rating" :value="old('rating', 0)" :readonly="false" />
                            </div>
                            @error('rating')
                                <p class="text-xs font-bold text-[#DD3015] mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="comment" class="block text-xs font-black text-black uppercase tracking-wider mb-1">Isi Komentar / Testimoni</label>
                            <textarea id="comment"
                                      name="comment"
                                      rows="4"
                                      maxlength="1000"
                                      placeholder="Ketik ulasan jujur Anda untuk membantu perkembangan produk UMKM lokal ini..."
                                      class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:border-[#DD3015] focus:outline-none focus:ring-4 focus:ring-[#DD3015]/10 resize-none min-h-[44px] transition-all">{{ old('comment') }}</textarea>
                            @error('comment')
                                <p class="text-xs font-bold text-[#DD3015] mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="bg-black hover:bg-[#DD3015] text-white font-black text-xs uppercase tracking-widest px-6 py-3.5 rounded-xl min-h-[44px] transition-all shadow-md">
                            KIRIM REVIU SEKARANG
                        </button>
                    </form>
                </div>
            @endif
        @endauth

        {{-- ================= LIST REVIEW ================= --}}
        <div class="space-y-4">
            <h2 class="text-xl font-black text-black uppercase tracking-tight">
                ULASAN KOMUNITAS PELANGGAN
            </h2>

            @if($reviews->count() > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach($reviews as $review)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5 flex flex-col justify-between">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-11 h-11 rounded-full bg-[#F3E1E1] flex items-center justify-center flex-shrink-0 border border-white shadow-sm">
                                        @if($review->user->avatar)
                                            <img src="{{ asset('storage/' . $review->user->avatar) }}"
                                                 alt="{{ $review->user->name }}"
                                                 class="w-full h-full rounded-full object-cover">
                                        @else
                                            <span class="text-sm font-black text-[#DD3015]">
                                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-black text-sm text-black uppercase tracking-tight">{{ $review->user->name }}</p>
                                        <div class="mt-0.5 scale-90 origin-left">
                                            <x-star-rating :value="$review->rating" :readonly="true" />
                                        </div>
                                    </div>
                                </div>

                                <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2.5 py-1 rounded-md self-start sm:self-center">
                                    {{ $review->created_at->diffForHumans() }}
                                </span>
                            </div>

                            @if($review->comment)
                                <p class="text-sm text-gray-600 mt-4 pl-1 leading-relaxed bg-gray-50/50 p-3 rounded-xl border border-gray-100/50">
                                    "{{ $review->comment }}"
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl border border-gray-100 p-12 text-center shadow-sm">
                    <p class="text-gray-400 text-sm font-bold">Belum ada penilaian masuk. Jadilah orang pertama yang mengulas!</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection