<div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-100 px-5 py-3.5">
        <h3 class="font-semibold text-gray-800 text-sm">Progress Program</h3>
    </div>
    <div class="p-5">
        @php
            // Hitung statistik dari kegiatanRows
            $total = count($kegiatanRows ?? []);
            $completed = collect($kegiatanRows ?? [])
                ->where('status', 'completed')
                ->count();
            $ongoing = collect($kegiatanRows ?? [])
                ->where('status', 'ongoing')
                ->count();
            $pending = collect($kegiatanRows ?? [])
                ->where('status', 'pending')
                ->count();
            $cancelled = collect($kegiatanRows ?? [])
                ->where('status', 'cancelled')
                ->count();

            // Hitung persentase
            $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

            // Hitung stroke-dashoffset untuk progress circle
            // Keliling lingkaran = 2 * pi * r = 2 * 3.14159 * 50 = 314.159
            $circumference = 314.159;
            $offset = $circumference - ($percentage / 100) * $circumference;
        @endphp

        <div class="flex justify-center">
            <div class="relative h-32 w-32">
                <svg class="h-32 w-32 -rotate-90">
                    <circle cx="64" cy="64" r="50" stroke="#E5E7EB" stroke-width="8" fill="none" />
                    <circle cx="64" cy="64" r="50" stroke="#22C55E" stroke-width="8" fill="none"
                        stroke-linecap="round" stroke-dasharray="{{ $circumference }}"
                        stroke-dashoffset="{{ $offset }}" />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <div class="text-2xl font-bold">{{ $percentage }}%</div>
                    <div class="text-xs text-gray-500">Selesai</div>
                </div>
            </div>
        </div>

        <div class="mt-4 space-y-2 text-xs">
            <div class="flex justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>
                    <span>Selesai</span>
                </div>
                <strong>{{ $completed }}</strong>
            </div>
            <div class="flex justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                    <span>Berlangsung</span>
                </div>
                <strong>{{ $ongoing }}</strong>
            </div>
            <div class="flex justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-gray-400"></span>
                    <span>Belum Dimulai</span>
                </div>
                <strong>{{ $pending }}</strong>
            </div>
            <div class="flex justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                    <span>Dibatalkan</span>
                </div>
                <strong>{{ $cancelled }}</strong>
            </div>
        </div>

        <div class="mt-4 border-t border-gray-100 pt-3 text-center text-[10px] text-gray-500">
            Last Update: <strong>{{ now()->format('d M Y H:i') }} WIB</strong>
        </div>
    </div>
</div>
