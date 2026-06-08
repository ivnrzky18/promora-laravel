@props(['endDate'])

@php
    $endTimestamp = $endDate instanceof \Carbon\Carbon
        ? $endDate->timestamp
        : \Carbon\Carbon::parse($endDate)->timestamp;
    $uniqueId = 'countdown_' . uniqid();
@endphp

<div x-data="{
        endTime: {{ $endTimestamp }} * 1000,
        hours: '00',
        minutes: '00',
        seconds: '00',
        expired: false,
        init() {
            this.update();
            setInterval(() => this.update(), 1000);
        },
        update() {
            const now = Date.now();
            const diff = this.endTime - now;
            if (diff <= 0) {
                this.expired = true;
                this.hours = '00';
                this.minutes = '00';
                this.seconds = '00';
                return;
            }
            const totalSeconds = Math.floor(diff / 1000);
            const h = Math.floor(totalSeconds / 3600);
            const m = Math.floor((totalSeconds % 3600) / 60);
            const s = totalSeconds % 60;
            this.hours   = String(h).padStart(2, '0');
            this.minutes = String(m).padStart(2, '0');
            this.seconds = String(s).padStart(2, '0');
        }
    }"
     class="inline-flex items-center space-x-1">

    {{-- "Berakhir Segera" badge --}}
    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 mr-1">
        ⏰ Berakhir Segera
    </span>

    <template x-if="!expired">
        <div class="flex items-center space-x-0.5 font-mono text-sm font-bold text-orange-600">
            <span class="bg-orange-50 border border-orange-200 rounded px-1.5 py-0.5" x-text="hours"></span>
            <span class="text-orange-400">:</span>
            <span class="bg-orange-50 border border-orange-200 rounded px-1.5 py-0.5" x-text="minutes"></span>
            <span class="text-orange-400">:</span>
            <span class="bg-orange-50 border border-orange-200 rounded px-1.5 py-0.5" x-text="seconds"></span>
        </div>
    </template>

    <template x-if="expired">
        <span class="text-xs font-semibold text-red-500">Berakhir</span>
    </template>
</div>
