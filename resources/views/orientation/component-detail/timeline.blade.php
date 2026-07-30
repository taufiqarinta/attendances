<div class="rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-100 px-4 py-2.5">
        <div class="flex items-center gap-2">
            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-red-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-red-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 text-xs">Timeline Program</h3>
                <p class="text-[10px] text-gray-500">Progress pelaksanaan</p>
            </div>
        </div>
    </div>
    <div class="p-3.5">
        @if (isset($kegiatanRows) && count($kegiatanRows) > 0)
            @php
                // Urutkan berdasarkan tanggal dan waktu
                $sortedRows = collect($kegiatanRows)
                    ->sortBy([['tanggal', 'asc'], ['waktu_mulai', 'asc']])
                    ->values()
                    ->all();
            @endphp

            <div class="relative">
                <div class="absolute left-[11px] top-2 bottom-2 w-[1.5px] bg-gray-200"></div>

                @foreach ($sortedRows as $index => $row)
                    @php
                        $status = $row['status'] ?? 'pending';
                        $isCompleted = $status === 'completed';
                        $isOngoing = $status === 'ongoing';
                        $isPending = $status === 'pending';
                        $isCancelled = $status === 'cancelled';

                        $color = match ($status) {
                            'completed' => 'green',
                            'ongoing' => 'blue',
                            'pending' => 'gray',
                            'cancelled' => 'red',
                            default => 'gray',
                        };

                        $statusLabels = [
                            'pending' => 'Belum Dimulai',
                            'ongoing' => 'Berlangsung',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                        ];
                        $statusLabel = $statusLabels[$status] ?? ucfirst($status);

                        $timeDisplay = '-';
                        if (isset($row['waktu_mulai'])) {
                            $timeDisplay = $row['waktu_mulai'];
                            if (isset($row['waktu_selesai']) && $row['waktu_selesai']) {
                                $timeDisplay .= ' - ' . $row['waktu_selesai'];
                            }
                            $timeDisplay .= ' WIB';
                        }

                        $dateDisplay = $row['tanggal_formatted'] ?? '-';
                        $isToday = isset($row['tanggal']) && $row['tanggal'] === date('Y-m-d');
                        $isTomorrow = isset($row['tanggal']) && $row['tanggal'] === date('Y-m-d', strtotime('+1 day'));

                        if ($isToday) {
                            $dateDisplay = 'Hari ini, ' . $dateDisplay;
                        } elseif ($isTomorrow) {
                            $dateDisplay = 'Besok, ' . $dateDisplay;
                        }

                        $hasScore = isset($row['score']) && $row['score'] !== null && $row['score'] !== '';
                        $scoreValue = $hasScore ? (int) $row['score'] : '';

                        $hasStartedAt = isset($row['started_at']) && $row['started_at'];
                        $hasCompletedAt = isset($row['completed_at']) && $row['completed_at'];
                    @endphp

                    <div class="relative mb-3.5 flex gap-2.5 last:mb-0">
                        {{-- Timeline Dot --}}
                        <div class="relative z-10 mt-0.5">
                            @if ($isCompleted)
                                <div
                                    class="flex h-5 w-5 items-center justify-center rounded-full bg-green-500 text-white text-[8px] shadow-sm">
                                    ✓
                                </div>
                            @elseif($isOngoing)
                                <div
                                    class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-500 text-white animate-pulse text-[8px]">
                                    ●
                                </div>
                            @elseif($isCancelled)
                                <div
                                    class="flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white text-[8px] shadow-sm">
                                    ✕
                                </div>
                            @else
                                <div
                                    class="flex h-5 w-5 items-center justify-center rounded-full border-2 border-gray-300 bg-white">
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <div class="font-semibold text-gray-800 text-xs">{{ $row['title'] ?? 'Kegiatan' }}</div>
                                @if ($hasScore && $canManage)
                                    <span
                                        class="text-[8px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded-full">
                                        Nilai: {{ $scoreValue }}
                                    </span>
                                @endif
                            </div>

                            <div class="text-[10px] text-gray-500">
                                {{ $dateDisplay }} • {{ $timeDisplay }}
                            </div>

                            @if (isset($row['pic_name']) && $row['pic_name'] && $row['pic_name'] !== '-')
                                <div class="text-[8px] text-gray-400 mt-0.5">
                                    PIC: <span class="font-medium text-gray-600">{{ $row['pic_name'] }}</span>
                                </div>
                            @endif

                            {{-- Tampilkan waktu mulai dan selesai --}}
                            @if ($hasStartedAt || $hasCompletedAt)
                                <div class="mt-0.5 space-y-0.5">
                                    @if ($hasStartedAt)
                                        <div class="text-[8px] text-green-600">
                                            🟢 Dimulai: {{ $row['started_at'] }}
                                        </div>
                                    @endif
                                    @if ($hasCompletedAt)
                                        <div class="text-[8px] text-blue-600">
                                            ✅ Selesai: {{ $row['completed_at'] }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <span
                                class="mt-0.5 inline-block text-[8px] font-semibold rounded-full px-1.5 py-0.5 
                                @if ($isCompleted) text-green-700 bg-green-100 
                                @elseif($isOngoing) text-blue-700 bg-blue-100 
                                @elseif($isCancelled) text-red-700 bg-red-100 
                                @else text-gray-600 bg-gray-100 @endif">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="py-8 text-center">
                <svg class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-gray-500">Belum ada kegiatan</p>
                <p class="text-xs text-gray-400 mt-1">Timeline akan muncul setelah ada kegiatan</p>
            </div>
        @endif
    </div>
</div>
