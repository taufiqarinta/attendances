<div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-gray-100 px-4 sm:px-6 py-3.5">
        <div class="flex items-center gap-2.5">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 text-sm">Rincian Program</h3>
                <p class="text-xs text-gray-500">Informasi lengkap program orientation</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 md:gap-y-4 px-4 sm:px-6 py-4 md:py-5">

        {{-- Left Column --}}
        <div class="space-y-2 md:space-y-3 text-xs sm:text-sm">
            <div class="grid grid-cols-[120px_12px_1fr]">
                <span class="text-gray-500">Nama Program</span>
                <span>:</span>
                <span class="font-semibold text-gray-800 break-words">{{ $orientation->batch_name ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-[120px_12px_1fr]">
                <span class="text-gray-500">Lokasi</span>
                <span>:</span>
                <span class="font-semibold">{{ $orientation->plant->name_plant ?? '-' }}</span>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-2 md:space-y-3 text-xs sm:text-sm">
            <div class="grid grid-cols-[120px_12px_1fr]">
                <span class="text-gray-500">Periode</span>
                <span>:</span>
                <span class="font-semibold">
                    @php
                        $dates = collect($kegiatanRows)->pluck('tanggal')->filter();
                        $startDate = $dates->first();
                        $endDate = $dates->last();
                    @endphp
                    @if ($startDate && $endDate)
                        {{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}
                    @else
                        -
                    @endif
                </span>
            </div>
            <div class="grid grid-cols-[120px_12px_1fr]">
                <span class="text-gray-500">Total Peserta</span>
                <span>:</span>
                <span class="font-semibold">{{ $totalParticipants ?? 0 }} Orang</span>
            </div>
            <div class="grid grid-cols-[120px_12px_1fr]">
                <span class="text-gray-500">Total Materi</span>
                <span>:</span>
                <span class="font-semibold">{{ $totalActivities ?? 0 }} Kegiatan</span>
            </div>
            <div class="grid grid-cols-[120px_12px_1fr]">
                <span class="text-gray-500">Durasi</span>
                <span>:</span>
                <span class="font-semibold">
                    @php
                        $uniqueDates = collect($kegiatanRows)->pluck('tanggal')->unique()->count();
                    @endphp
                    {{ $uniqueDates }} Hari
                </span>
            </div>
        </div>
    </div>
</div>
