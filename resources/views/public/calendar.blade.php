@extends('layouts.app')

@section('title', 'Kalender Promo & Event - Promora')

@push('styles')
<style>
    /* FullCalendar Overrides agar Serasi dengan Desain Promora */
    .fc {
        font-family: inherit;
    }
    /* Mengubah tombol navigasi (Bulan, Minggu, Daftar, Hari Ini) */
    .fc .fc-button-primary {
        background-color: #DD3015 !important; /* Merah Promora */
        border-color: #DD3015 !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.05em;
        padding: 0.6rem 1rem !important;
        border-radius: 10px !important;
        transition: all 0.2s ease;
    }
    .fc .fc-button-primary:hover {
        background-color: #F30000 !important; /* Merah Cerah saat Hover */
        border-color: #F30000 !important;
    }
    .fc .fc-button-primary:disabled {
        background-color: #DD3015 !important;
        opacity: 0.5;
    }
    /* Tombol Aktif (Misal sedang di tab 'Bulan') berubah jadi Hitam */
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
        background-color: #000000 !important;
        border-color: #000000 !important;
    }
    /* Judul Bulan/Tahun di Tengah */
    .fc .fc-toolbar-title {
        font-size: 1.3rem !important;
        font-weight: 900 !important;
        color: #000000 !important;
        text-transform: uppercase;
        letter-spacing: -0.025em;
    }
    /* Header Nama Hari (Sen, Sel, Rab...) */
    .fc .fc-col-header-cell {
        background-color: #FFFFFF;
        padding: 12px 0 !important;
    }
    .fc .fc-col-header-cell-cushion {
        color: #DD3015 !important; /* Teks Hari jadi Merah Promora */
        font-weight: 800 !important;
        text-decoration: none !important;
        font-size: 0.85rem;
    }
    /* Angka Tanggal di dalam Kalender */
    .fc .fc-daygrid-day-number {
        color: #000000 !important;
        font-weight: 700 !important;
        padding: 8px !important;
        text-decoration: none !important;
        font-size: 0.85rem;
    }
    /* Highlight Hari Ini menggunakan Pink Soft (#F3E1E1) */
    .fc .fc-daygrid-day.fc-day-today {
        background-color: #F3E1E1 !important;
    }
    /* Styling Pill Judul Event/Promo yang tampil di Kalender */
    .fc .fc-daygrid-event {
        border-radius: 6px !important;
        padding: 3px 6px !important;
        border: none !important;
        font-size: 0.75rem !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08) !important;
    }
    .fc .fc-event-title {
        font-weight: 700 !important;
        color: #FFFFFF !important;
    }
    /* Tampilan List/Daftar */
    .fc .fc-list-day-cushion {
        background-color: #F3E1E1 !important;
    }
    .fc .fc-list-event:hover td {
        background-color: #fff1f1 !important;
    }
    /* Responsif untuk HP */
    @media (max-width: 640px) {
        .fc .fc-toolbar {
            flex-direction: column !important;
            gap: 0.75rem !important;
        }
        .fc .fc-toolbar-title {
            font-size: 1.1rem !important;
        }
    }
</style>
@endpush

@section('content')
<!-- Background Utama diset ke #F3E1E1 (Pink Soft) -->
<div x-data="calendarApp()" x-init="initCalendar()" class="min-h-screen bg-[#F3E1E1] py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Page Header --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4 bg-white p-6 rounded-2xl border border-[#DD3015]/10 shadow-sm">
            <div>
                <span class="inline-block text-xs font-black tracking-widest text-[#DD3015] uppercase mb-1">AGENDA UMKM</span>
                <h1 class="text-3xl sm:text-4xl font-black text-black tracking-tight">
                    KALENDER <span class="text-[#DD3015]">PROMO</span> & EVENT
                </h1>
                <p class="mt-1 text-sm text-gray-500">Temukan promo hemat dan acara seru dari UMKM lokal di sekitar Anda</p>
            </div>

            {{-- Legend Warna --}}
            <div class="flex items-center gap-4 text-xs font-bold uppercase tracking-wider bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                <span class="flex items-center gap-2">
                    <span class="inline-block w-3 h-3 rounded-full bg-[#DD3015]"></span>
                    Promo (Merah)
                </span>
                <span class="flex items-center gap-2 border-l pl-4 border-gray-200">
                    <span class="inline-block w-3 h-3 rounded-full bg-black"></span>
                    Event (Hitam)
                </span>
            </div>
        </div>

        {{-- Filter Row --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <label for="category-filter" class="text-xs font-black text-black uppercase tracking-wider whitespace-nowrap">
                    Kategori:
                </label>
                <div class="relative flex-1 sm:flex-none">
                    <select
                        id="category-filter"
                        x-model="selectedCategoryId"
                        @change="onCategoryChange()"
                        class="block w-full sm:w-64 rounded-xl border border-gray-200 bg-white px-4 py-2.5 pr-10 text-sm font-bold text-black shadow-sm focus:border-[#DD3015] focus:outline-none focus:ring-2 focus:ring-[#DD3015]/20 min-h-[44px] appearance-none cursor-pointer"
                    >
                        <option value="">SEMUA KATEGORI</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ strtoupper($category->name) }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-[#DD3015]">
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kalender Utama (FullCalendar akan di-render di sini) --}}
        <div class="bg-white rounded-3xl shadow-xl border border-[#DD3015]/10 p-4 sm:p-6">
            <div id="calendar"></div>
        </div>

    </div>

    {{-- Event Detail Modal Pop-up --}}
    <div
        x-show="modalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="modalOpen = false"
        @keydown.escape.window="modalOpen = false"
        style="display: none;"
    >
        {{-- Backdrop Gelap Lembut --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        {{-- Modal Box --}}
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden z-10 border border-gray-100"
            role="dialog"
            aria-modal="true"
            :aria-labelledby="'modal-title'"
        >
            {{-- Modal Header: Menyesuaikan warna dinamis (Merah jika promo, Hitam jika event) --}}
            <div
                class="px-6 py-6 flex items-start justify-between text-white"
                :class="modalEvent.type === 'promo' ? 'bg-[#DD3015]' : 'bg-black'"
            >
                <div class="flex-1 min-w-0 pr-4">
                    <span
                        class="inline-block px-2.5 py-0.5 bg-[#FFB800] text-black text-[10px] font-black uppercase tracking-widest rounded-md mb-2"
                        x-text="modalEvent.type"
                    ></span>
                    <h2
                        id="modal-title"
                        class="text-xl font-black leading-tight uppercase tracking-tight"
                        x-text="modalEvent.title"
                    ></h2>
                </div>
                <button
                    @click="modalOpen = false"
                    class="flex-shrink-0 text-white/80 hover:text-white transition-colors w-8 h-8 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20"
                    aria-label="Tutup modal"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="px-6 py-6 space-y-5 bg-white">

                {{-- Tanggal --}}
                <div class="flex items-start gap-3.5">
                    <div class="w-9 h-9 rounded-xl bg-[#F3E1E1] text-[#DD3015] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Masa Berlaku / Pelaksanaan</p>
                        <p class="text-sm font-bold text-black mt-0.5" x-text="modalEvent.dateRange"></p>
                    </div>
                </div>

                {{-- Lokasi (Khusus Event) --}}
                <template x-if="modalEvent.location">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Tempat / Lokasi</p>
                            <p class="text-sm font-bold text-black mt-0.5" x-text="modalEvent.location"></p>
                        </div>
                    </div>
                </template>

                {{-- Seller / Nama Toko --}}
                <template x-if="modalEvent.seller">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Mitra UMKM</p>
                            <p class="text-sm font-bold text-black mt-0.5" x-text="modalEvent.seller"></p>
                        </div>
                    </div>
                </template>

                {{-- Informasi Diskon (Khusus Promo) --}}
                <template x-if="modalEvent.discount">
                    <div class="p-3 bg-[#F30000]/5 border border-[#F30000]/10 rounded-2xl flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#F30000] text-[#FFB800] flex items-center justify-center flex-shrink-0 font-bold text-lg">
                            %
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-[#F30000] uppercase tracking-wider">Keuntungan Diskoni</p>
                            <p class="text-base font-black text-[#F30000]" x-text="modalEvent.discount"></p>
                        </div>
                    </div>
                </template>

            </div>

            {{-- Modal Footer --}}
            <div class="px-6 pb-6 pt-2 bg-gray-50 flex flex-col gap-2">
                <template x-if="modalEvent.url && modalEvent.url !== '#'">
                    <a
                        :href="modalEvent.url"
                        class="w-full inline-flex items-center justify-center gap-2 bg-[#DD3015] hover:bg-black text-white font-black text-xs uppercase tracking-widest py-3.5 rounded-xl transition-all shadow-md shadow-red-600/10"
                    >
                        Buka Halaman Promo
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </template>
                <button
                    @click="modalOpen = false"
                    class="w-full py-3 text-xs font-bold uppercase text-gray-400 hover:text-black transition-colors"
                >
                    Kembali ke Kalender
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- FullCalendar Engine CDN --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
function calendarApp() {
    return {
        selectedCategoryId: '',
        modalOpen: false,
        modalEvent: {
            type: 'promo',
            title: '',
            dateRange: '',
            location: null,
            seller: null,
            discount: null,
            url: null,
        },
        calendar: null,

        initCalendar() {
            const self = this;
            const calendarEl = document.getElementById('calendar');

            self.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listMonth',
                },
                buttonText: {
                    today: 'Hari Ini',
                    month: 'Bulan',
                    week: 'Minggu',
                    list: 'Daftar',
                },
                events: {
                    url: '{{ route("calendar.events") }}',
                    extraParams: function () {
                        return {
                            category_id: self.selectedCategoryId,
                        };
                    },
                    failure: function () {
                        console.error('Gagal memuat data kalender.');
                    },
                },
                eventClick: function (info) {
                    info.jsEvent.preventDefault();

                    const event = info.event;
                    const props = event.extendedProps;
                    const isPromo = event.id.startsWith('promo-');

                    let dateRange = '';
                    if (event.start) {
                        const startStr = self.formatDate(event.start);
                        if (event.end) {
                            const displayEnd = new Date(event.end);
                            if (isPromo) {
                                displayEnd.setDate(displayEnd.getDate() - 1);
                            }
                            const endStr = self.formatDate(displayEnd);
                            dateRange = startStr === endStr ? startStr : startStr + ' – ' + endStr;
                        } else {
                            dateRange = startStr;
                        }
                    }

                    self.modalEvent = {
                        type: isPromo ? 'promo' : 'event',
                        title: event.title,
                        dateRange: dateRange,
                        location: props.location || null,
                        seller: props.seller || null,
                        discount: props.discount || null,
                        url: event.url || null,
                    };

                    self.modalOpen = true;
                },
                eventDidMount: function (info) {
                    info.el.style.cursor = 'pointer';
                    
                    // WARNA PILL EVENT DI KALENDER:
                    // Jika data diawali id 'promo-', pil berwarna Merah Promora (#DD3015). Jika tidak, Hitam Murni (#000000)
                    if (info.event.id.startsWith('promo-')) {
                        info.el.style.backgroundColor = '#DD3015';
                    } else {
                        info.el.style.backgroundColor = '#000000';
                    }
                },
                noEventsContent: 'Tidak ada promo atau event untuk ditampilkan.',
            });

            self.calendar.render();
        },

        onCategoryChange() {
            if (this.calendar) {
                this.calendar.refetchEvents();
            }
        },

        formatDate(date) {
            if (!date) return '';
            return new Intl.DateTimeFormat('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            }).format(date);
        },
    };
}
</script>
@endpush