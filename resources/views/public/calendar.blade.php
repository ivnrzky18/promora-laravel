@extends('layouts.app')

@section('title', 'Kalender Promo & Event - Promora')

@push('styles')
<style>
    /* FullCalendar overrides for Tailwind compatibility */
    .fc {
        font-family: inherit;
    }
    .fc .fc-button {
        background-color: #f97316;
        border-color: #f97316;
        font-weight: 500;
    }
    .fc .fc-button:hover {
        background-color: #ea6c0a;
        border-color: #ea6c0a;
    }
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
        background-color: #c2540a;
        border-color: #c2540a;
    }
    .fc .fc-button:focus {
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.3);
    }
    .fc .fc-toolbar-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1f2937;
    }
    .fc .fc-daygrid-event {
        border-radius: 4px;
        font-size: 0.75rem;
        padding: 1px 4px;
    }
    .fc .fc-event-title {
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .fc .fc-col-header-cell-cushion,
    .fc .fc-daygrid-day-number {
        color: #374151;
        text-decoration: none;
    }
    .fc .fc-daygrid-day.fc-day-today {
        background-color: #fff7ed;
    }
    .fc .fc-list-event:hover td {
        background-color: #fff7ed;
    }
    @media (max-width: 640px) {
        .fc .fc-toolbar {
            flex-direction: column;
            gap: 0.5rem;
        }
        .fc .fc-toolbar-title {
            font-size: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div
    x-data="calendarApp()"
    x-init="initCalendar()"
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Kalender Promo & Event</h1>
        <p class="mt-1 text-sm text-gray-500">Temukan promo dan acara UMKM lokal di sekitar Anda</p>
    </div>

    {{-- Legend + Filter Row --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        {{-- Legend --}}
        <div class="flex items-center gap-4 text-sm text-gray-600">
            <span class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-sm bg-orange-500"></span>
                Promo
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded-sm bg-blue-500"></span>
                Event
            </span>
        </div>

        {{-- Category Filter --}}
        <div class="flex items-center gap-2">
            <label for="category-filter" class="text-sm font-medium text-gray-700 whitespace-nowrap">
                Filter Kategori:
            </label>
            <select
                id="category-filter"
                x-model="selectedCategoryId"
                @change="onCategoryChange()"
                class="block w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200 min-h-[44px]"
            >
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Calendar Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
        <div id="calendar"></div>
    </div>

    {{-- Event Detail Modal --}}
    <div
        x-show="modalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="modalOpen = false"
        @keydown.escape.window="modalOpen = false"
        style="display: none;"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        {{-- Modal Panel --}}
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden z-10"
            role="dialog"
            aria-modal="true"
            :aria-labelledby="'modal-title'"
        >
            {{-- Modal Header --}}
            <div
                class="px-6 py-4 flex items-start justify-between"
                :class="modalEvent.type === 'promo' ? 'bg-orange-500' : 'bg-blue-500'"
            >
                <div class="flex-1 min-w-0 pr-4">
                    <span
                        class="inline-block text-xs font-semibold uppercase tracking-wider text-white/80 mb-1"
                        x-text="modalEvent.type === 'promo' ? 'Promo' : 'Event'"
                    ></span>
                    <h2
                        id="modal-title"
                        class="text-lg font-bold text-white leading-snug"
                        x-text="modalEvent.title"
                    ></h2>
                </div>
                <button
                    @click="modalOpen = false"
                    class="flex-shrink-0 text-white/80 hover:text-white transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg"
                    aria-label="Tutup modal"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="px-6 py-5 space-y-4">

                {{-- Date Range --}}
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-0.5">Tanggal</p>
                        <p class="text-sm text-gray-800" x-text="modalEvent.dateRange"></p>
                    </div>
                </div>

                {{-- Location (for events) --}}
                <template x-if="modalEvent.location">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-0.5">Lokasi</p>
                            <p class="text-sm text-gray-800" x-text="modalEvent.location"></p>
                        </div>
                    </div>
                </template>

                {{-- Seller --}}
                <template x-if="modalEvent.seller">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-0.5">UMKM</p>
                            <p class="text-sm text-gray-800" x-text="modalEvent.seller"></p>
                        </div>
                    </div>
                </template>

                {{-- Discount (for promos) --}}
                <template x-if="modalEvent.discount">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-0.5">Diskon</p>
                            <p class="text-sm font-semibold text-orange-600" x-text="modalEvent.discount"></p>
                        </div>
                    </div>
                </template>

            </div>

            {{-- Modal Footer --}}
            <div class="px-6 pb-5 flex flex-col sm:flex-row gap-3">
                <template x-if="modalEvent.url && modalEvent.url !== '#'">
                    <a
                        :href="modalEvent.url"
                        class="flex-1 inline-flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm px-4 py-3 rounded-lg transition-colors min-h-[44px]"
                    >
                        Lihat Detail
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </template>
                <button
                    @click="modalOpen = false"
                    class="flex-1 inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm px-4 py-3 rounded-lg transition-colors min-h-[44px]"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- FullCalendar from CDN --}}
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
                    // Prevent FullCalendar's default navigation to event URL
                    info.jsEvent.preventDefault();

                    const event = info.event;
                    const props = event.extendedProps;

                    // Determine type from event id prefix
                    const isPromo = event.id.startsWith('promo-');

                    // Build human-readable date range
                    let dateRange = '';
                    if (event.start) {
                        const startStr = self.formatDate(event.start);
                        if (event.end) {
                            // FullCalendar end is exclusive for all-day events; subtract 1 day for display
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
                    // Add a pointer cursor to all events
                    info.el.style.cursor = 'pointer';
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
