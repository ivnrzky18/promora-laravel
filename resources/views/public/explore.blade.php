@extends('layouts.app')

@section('title', 'Jelajahi Promo - Promora')

@section('content')
<div class="min-h-screen bg-[#F3E1E1] py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Page Header --}}
        <div class="mb-10 text-center sm:text-left bg-white p-6 sm:p-8 rounded-3xl border border-[#DD3015]/10 shadow-sm">
            <span class="inline-block text-xs font-black tracking-widest text-[#DD3015] uppercase mb-1">PENAWARAN TERBAIK</span>
            <h1 class="text-3xl sm:text-4xl font-black text-black tracking-tight uppercase">
                🔥 PROMO TERBARU DI SEKITAR ANDA
            </h1>
            <p class="mt-2 text-sm text-gray-500 max-w-xl">Jangan lewatkan diskon gila-gilaan dan potongan harga spesial langsung dari UMKM andalan lokal.</p>
        </div>

        {{-- Grid Promo --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($promos as $promo)
                <div class="group bg-white rounded-[2rem] shadow-xl shadow-red-900/5 overflow-hidden hover:shadow-red-900/10 hover:-translate-y-1 transition-all duration-300 border-4 border-white relative flex flex-col justify-between">
                    
                    {{-- Badge Diskon Melayang di Atas Gambar --}}
                    <div class="absolute top-4 left-4 z-10">
                        <span class="bg-[#DD3015] text-white text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-xl shadow-md">
                            HEMAT {{ $promo->discount_percentage }}%
                        </span>
                    </div>

                    <div>
                        {{-- Gambar Promo (Menggunakan Logika Pengetesan Anda) --}}
                        <div class="relative overflow-hidden h-52 bg-gray-100">
                            @if($promo->title == 'Promo Makan Siang')
                                <img src="https://picsum.photos/id/292/600/400" alt="{{ $promo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @elseif($promo->title == 'Diskon Jaket')
                                <img src="https://picsum.photos/id/1005/600/400" alt="{{ $promo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @elseif($promo->title == 'Promo Cuci Sepatu')
                                <img src="https://picsum.photos/id/21/600/400" alt="{{ $promo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @elseif($promo->title == 'Cek Kesehatan Murah')
                                <img src="https://picsum.photos/id/237/600/400" alt="{{ $promo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @elseif($promo->title == 'Kursus Bahasa Inggris')
                                <img src="https://picsum.photos/id/20/600/400" alt="{{ $promo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @elseif($promo->title == 'Promo Tiket Wisata')
                                <img src="https://picsum.photos/id/1043/600/400" alt="{{ $promo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <img src="https://picsum.photos/600/400" alt="{{ $promo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @endif
                        </div>

                        {{-- Konten Card --}}
                        <div class="p-6">
                            {{-- Nama UMKM / Toko --}}
                            <div class="flex items-center gap-1.5 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                <svg class="w-4 h-4 text-[#DD3015]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>{{ $promo->seller->business_name ?? 'Mitra Promora' }}</span>
                            </div>

                            {{-- Judul Promo --}}
                            <h2 class="text-xl font-black text-black leading-tight line-clamp-1 group-hover:text-[#DD3015] transition-colors uppercase">
                                {{ $promo->title }}
                            </h2>

                            {{-- Deskripsi --}}
                            <p class="text-gray-600 mt-2 text-sm line-clamp-2 leading-relaxed">
                                {{ Str::limit($promo->description, 80) }}
                            </p>

                            {{-- Section Harga --}}
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-baseline gap-2">
                                <span class="text-2xl font-black text-[#DD3015]">
                                    Rp{{ number_format($promo->promo_price, 0, ',', '.') }}
                                </span>
                                <span class="text-xs font-bold text-gray-400 line-through">
                                    Rp{{ number_format($promo->original_price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Bagian Bawah Card (Aksi & Statistik) --}}
                    <div class="px-6 pb-6 pt-2 bg-gray-50/50 rounded-b-[2rem]">
                        <div class="flex items-center justify-between text-[11px] font-bold text-gray-400 mb-4">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Dilihat {{ number_format($promo->view_count) }} kali
                            </span>
                        </div>

                        {{-- Tombol Aksi Menuju Detail --}}
                        <a href="{{ route('promos.show', $promo->id ?? $promo->slug ?? '#') }}" 
                           class="w-full inline-flex items-center justify-center bg-black hover:bg-[#DD3015] text-white text-xs font-black uppercase tracking-widest py-3 rounded-xl transition-all duration-300 shadow-md">
                            KLAIM PROMO SEKARANG
                        </a>
                    </div>

                </div>
            @empty
                {{-- Tampilan Kosong --}}
                <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white text-center py-16 px-4 rounded-[2rem] border-2 border-dashed border-gray-200">
                    <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-black uppercase mb-1">Belum Ada Promo</h3>
                    <p class="text-sm text-gray-500 max-w-xs mx-auto">Saat ini belum ada promo aktif yang tersedia di sekitar wilayah Anda.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-12 flex justify-center">
            {{ $promos->links() }}
        </div>

    </div>
</div>
@endsection