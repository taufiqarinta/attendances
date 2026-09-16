<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
    {{-- HEADER --}}
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
                <h3 class="font-semibold text-gray-800 text-sm">Jadwal & Materi Orientation</h3>
                <p class="text-xs text-gray-500">Daftar kegiatan yang akan maupun telah dilaksanakan</p>
            </div>
        </div>
    </div>

    @if (isset($kegiatanRows) && count($kegiatanRows) > 0)
        @php
            $statusColors = [
                'pending' => 'bg-gray-100 text-gray-600',
                'ongoing' => 'bg-blue-100 text-blue-700',
                'completed' => 'bg-green-100 text-green-700',
                'cancelled' => 'bg-red-100 text-red-700',
            ];
            $statusLabels = [
                'pending' => 'Belum Dimulai',
                'ongoing' => 'Berlangsung',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];
            $statusDot = [
                'pending' => 'bg-gray-400',
                'ongoing' => 'bg-blue-500 animate-pulse',
                'completed' => 'bg-green-500',
                'cancelled' => 'bg-red-500',
            ];
        @endphp

        {{-- ========================================================= --}}
        {{-- MOBILE: CARD VIEW --}}
        {{-- ========================================================= --}}
        <div class="block sm:hidden">
            <div class="divide-y divide-gray-100">
                @foreach ($kegiatanRows as $index => $row)
                    @php
                        $hasReacted = in_array($row['id'], $myReactionActivityIds ?? []);
                        $reactionCount = $reactionsByActivity[$row['id']]['count'] ?? 0;
                        $reactionRespondents = $reactionsByActivity[$row['id']]['respondents'] ?? 0;
                    @endphp
                    <div class="p-4 space-y-3" data-activity-id="{{ $row['id'] }}">
                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-start gap-2 min-w-0">
                                <span
                                    class="font-semibold text-sm text-gray-500 flex-shrink-0">#{{ $index + 1 }}</span>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-800 text-sm">{{ $row['title'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $row['description'] }}</div>
                                </div>
                            </div>
                            @if ($canManage)
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold flex-shrink-0 {{ $statusColors[$row['status']] ?? 'bg-gray-100 text-gray-600' }}">
                                    <span
                                        class="h-1.5 w-1.5 rounded-full {{ $statusDot[$row['status']] ?? 'bg-gray-400' }}"></span>
                                    {{ $statusLabels[$row['status']] ?? ucfirst($row['status'] ?? 'pending') }}
                                </span>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-gray-500">Tanggal</span>
                                <div class="font-medium">{{ $row['tanggal_formatted'] ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500">Waktu</span>
                                <div class="font-medium">{{ $row['waktu_range'] ?? '-' }}</div>
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-500">PIC</span>
                                <div class="font-medium">{{ $row['pic_name'] ?? '-' }}</div>
                            </div>
                        </div>

                        {{-- Aksi --}}
                        <div class="flex flex-col gap-2 pt-2 border-t border-gray-100">
                            @if ($row['status'] === 'cancelled')
                                <button disabled
                                    class="rounded-lg bg-gray-100 px-4 py-2 text-xs font-medium text-gray-400 cursor-not-allowed opacity-50 w-full">
                                    Dibatalkan
                                </button>
                            @elseif($row['status'] === 'completed')
                                @if ($canManage)
                                    {{-- Badge Selesai --}}
                                    <div
                                        class="inline-flex items-center justify-center gap-1 rounded-lg bg-green-50 px-4 py-2 text-xs font-semibold text-green-700 border border-green-200 w-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Selesai
                                    </div>
                                    {{-- Edit Kehadiran & Nilai --}}
                                    <button
                                        onclick="openAttendanceModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', 'completed')"
                                        class="inline-flex items-center justify-center gap-1 rounded-lg border border-blue-300 bg-blue-50 px-4 py-2 text-xs font-medium text-blue-700 hover:bg-blue-100 transition w-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit Kehadiran & Nilai
                                    </button>
                                    {{-- Lihat Hasil Reaksi --}}
                                    <button
                                        onclick="openReactionResultModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}')"
                                        class="inline-flex items-center justify-center gap-1 rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-xs font-medium text-amber-700 hover:bg-amber-100 transition w-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        {{-- Lihat Hasil ({{ $reactionRespondents }}) --}}
                                        Lihat Reaksi
                                    </button>
                                @endif

                                {{-- Peserta: Beri Reaksi --}}
                                @if ($canGiveReaction)
                                    <button
                                        onclick="openReactionModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}')"
                                        class="inline-flex items-center justify-center gap-1 rounded-lg border px-4 py-2 text-xs font-medium transition w-full
                                            {{ $hasReacted
                                                ? 'border-green-300 bg-green-50 text-green-700 hover:bg-green-100'
                                                : 'border-purple-300 bg-purple-50 text-purple-700 hover:bg-purple-100' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        {{ $hasReacted ? 'Edit Reaksi' : 'Beri Reaksi' }}
                                    </button>
                                @endif
                            @elseif($row['status'] === 'ongoing')
                                @if ($canManage)
                                    <button
                                        onclick="openAttendanceModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', 'ongoing')"
                                        class="rounded-lg border border-blue-300 bg-blue-50 px-4 py-2 text-xs font-medium text-blue-700 hover:bg-blue-100 transition w-full">
                                        Update Kehadiran & Nilai
                                    </button>
                                    <button
                                        onclick="openAttendanceModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', 'completed')"
                                        class="rounded-lg bg-green-600 px-4 py-2 text-xs font-semibold text-white hover:bg-green-700 transition w-full">
                                        Selesai
                                    </button>
                                @endif
                            @elseif($row['status'] === 'pending')
                                @if ($canManage)
                                    <button
                                        onclick="openAttendanceModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', 'ongoing')"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition w-full">
                                        Mulai
                                    </button>
                                @endif
                            @endif

                            {{-- Timestamp --}}
                            @if (($row['started_at'] ?? false) && $canManage)
                                <div class="text-[10px] text-green-600">🟢 Dimulai: {{ $row['started_at'] }}</div>
                            @endif
                            @if (($row['completed_at'] ?? false) && $canManage)
                                <div class="text-[10px] text-blue-600">✅ Selesai: {{ $row['completed_at'] }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- DESKTOP: TABLE VIEW --}}
        {{-- ========================================================= --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50/80">
                    <tr class="text-left text-xs text-gray-500">
                        <th class="px-4 py-2.5 w-12 font-semibold">No</th>
                        <th class="px-4 py-2.5 font-semibold">Kegiatan</th>
                        <th class="px-4 py-2.5 font-semibold">Tanggal</th>
                        <th class="px-4 py-2.5 font-semibold">Waktu</th>
                        <th class="px-4 py-2.5 font-semibold">PIC / Trainer</th>
                        <th class="px-4 py-2.5 font-semibold">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($kegiatanRows as $index => $row)
                        @php
                            $hasReacted = in_array($row['id'], $myReactionActivityIds ?? []);
                            $reactionRespondents = $reactionsByActivity[$row['id']]['respondents'] ?? 0;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition" data-activity-id="{{ $row['id'] }}">
                            <td class="px-4 py-3 font-semibold text-sm">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div>
                                    <div class="font-semibold text-gray-800 text-sm">{{ $row['title'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $row['description'] }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $row['tanggal_formatted'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $row['waktu_range'] ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium">{{ $row['pic_name'] ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColors[$row['status']] ?? 'bg-gray-100 text-gray-600' }}">
                                    <span
                                        class="h-1.5 w-1.5 rounded-full {{ $statusDot[$row['status']] ?? 'bg-gray-400' }}"></span>
                                    {{ $statusLabels[$row['status']] ?? ucfirst($row['status'] ?? 'pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-2">
                                    @if ($row['status'] === 'cancelled')
                                        <button disabled
                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-[10px] font-medium text-gray-400 cursor-not-allowed opacity-50">
                                            Dibatalkan
                                        </button>
                                    @elseif($row['status'] === 'completed')
                                        <div class="flex flex-col gap-1.5">
                                            @if ($canManage)
                                                <button
                                                    onclick="openAttendanceModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', 'completed')"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-blue-300 bg-blue-50 px-3 py-1.5 text-[10px] font-medium text-blue-700 hover:bg-blue-100 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit Kehadiran & Nilai
                                                </button>
                                                <button
                                                    onclick="openReactionResultModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}')"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-amber-300 bg-amber-50 px-3 py-1.5 text-[10px] font-medium text-amber-700 hover:bg-amber-100 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                    {{-- Lihat Hasil ({{ $reactionRespondents }}) --}}
                                                    Lihat Reaksi
                                                </button>
                                            @endif
                                            @if ($canGiveReaction)
                                                <button
                                                    onclick="openReactionModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}')"
                                                    class="inline-flex items-center gap-1 rounded-lg border px-3 py-1.5 text-[10px] font-medium transition
                                                        {{ $hasReacted
                                                            ? 'border-green-300 bg-green-50 text-green-700 hover:bg-green-100'
                                                            : 'border-purple-300 bg-purple-50 text-purple-700 hover:bg-purple-100' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                    </svg>
                                                    {{ $hasReacted ? 'Edit Reaksi' : 'Beri Reaksi' }}
                                                </button>
                                            @endif
                                        </div>
                                    @elseif($row['status'] === 'ongoing')
                                        @if ($canManage)
                                            <button
                                                onclick="openAttendanceModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', 'ongoing')"
                                                class="inline-flex items-center gap-1 rounded-lg border border-blue-300 bg-blue-50 px-3 py-1.5 text-[10px] font-medium text-blue-700 hover:bg-blue-100 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Update Nilai
                                            </button>
                                            <button
                                                onclick="openAttendanceModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', 'completed')"
                                                class="rounded-lg bg-green-600 px-3 py-1.5 text-[10px] font-semibold text-white hover:bg-green-700 transition">
                                                Selesai
                                            </button>
                                        @endif
                                    @elseif($row['status'] === 'pending')
                                        @if ($canManage)
                                            <button
                                                onclick="openAttendanceModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', 'ongoing')"
                                                class="rounded-lg bg-blue-600 px-3 py-1.5 text-[10px] font-semibold text-white hover:bg-blue-700 transition">
                                                Mulai
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- FOOTER SUMMARY --}}
        <div
            class="flex flex-col sm:flex-row items-center justify-between border-t border-gray-100 bg-gray-50/80 px-4 sm:px-6 py-2.5 gap-2 sm:gap-0">
            @if ($canManage)
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs">
                    @php
                        $completed = collect($kegiatanRows)->where('status', 'completed')->count();
                        $ongoing = collect($kegiatanRows)->where('status', 'ongoing')->count();
                        $pending = collect($kegiatanRows)->where('status', 'pending')->count();
                        $cancelled = collect($kegiatanRows)->where('status', 'cancelled')->count();
                    @endphp
                    <div class="flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>
                        <span class="text-gray-600">Selesai ({{ $completed }})</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                        <span class="text-gray-600">Berlangsung ({{ $ongoing }})</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-gray-400"></span>
                        <span class="text-gray-600">Belum Dimulai ({{ $pending }})</span>
                    </div>
                    @if ($cancelled > 0)
                        <div class="flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            <span class="text-gray-600">Dibatalkan ({{ $cancelled }})</span>
                        </div>
                    @endif
                </div>
            @endif
            <div class="text-xs text-gray-500">
                Total: <span class="font-semibold text-gray-700">{{ count($kegiatanRows) }} Materi</span>
            </div>
        </div>
    @else
        {{-- EMPTY STATE --}}
        <div class="py-12 text-center">
            <svg class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-sm text-gray-500">Belum ada kegiatan</p>
            <p class="text-xs text-gray-400 mt-1">Belum ada jadwal materi untuk program ini</p>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- MODAL KEHADIRAN + PRE/POST TEST --}}
    {{-- ========================================================= --}}
    <div id="attendanceModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAttendanceModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-[1400px] mx-auto overflow-hidden flex flex-col max-h-[92vh]">
                {{-- Header --}}
                <div class="border-b border-gray-100 px-6 py-4 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm" id="attendanceTitle">Kehadiran &
                                    Penilaian</h3>
                                <p class="text-xs text-gray-500" id="attendanceKegiatan">-</p>
                            </div>
                        </div>
                        <button onclick="closeAttendanceModal()" class="rounded-lg p-1 hover:bg-gray-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-y-auto p-6">
                    <input type="hidden" id="attendanceActivityId">
                    <input type="hidden" id="attendanceNextStatus">

                    {{-- Search --}}
                    <div class="mb-4">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-5.2-5.2m1.7-5.3a7 7 0 11-14 0a7 7 0 0114 0z" />
                            </svg>
                            <input type="text" id="attendanceSearch" oninput="filterAttendanceTable()"
                                placeholder="Cari nama, NIK, atau catatan..."
                                class="w-full rounded-xl border border-gray-200 pl-9 pr-9 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none transition">
                            <button type="button" id="attendanceSearchClear" onclick="clearAttendanceSearch()"
                                class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-xl">
                        <table class="w-full min-w-[700px]">
                            <thead class="bg-gradient-to-r from-blue-50 to-blue-100/50 border-b border-blue-100">
                                <tr>
                                    <th
                                        class="w-12 px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">
                                        No</th>
                                    <th
                                        class="w-20 px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">
                                        <div class="flex flex-col items-center gap-1">
                                            <span>Hadir</span>
                                            <input type="checkbox" id="checkAllAttendance"
                                                onchange="toggleAllAttendance(this)" title="Centang semua"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4 cursor-pointer">
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Nama
                                        Peserta</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase w-32">
                                        NIK</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase w-28">
                                        Pre Test</th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase w-28">
                                        Post Test</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase min-w-[280px]">
                                        Catatan</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody" class="divide-y divide-gray-100"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="border-t border-gray-100 px-6 py-4 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 flex-shrink-0">
                    <div class="text-xs text-gray-500">
                        Hadir: <span class="font-semibold text-gray-700" id="attendanceCount">0</span> /
                        <span class="font-semibold text-gray-700" id="attendanceTotal">0</span> peserta
                    </div>
                    <div class="flex gap-3">
                        <button onclick="closeAttendanceModal()"
                            class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button onclick="submitAttendance()" id="submitAttendanceBtn"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL BERI REAKSI (Peserta) --}}
    {{-- ========================================================= --}}
    <div id="reactionModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeReactionModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-auto overflow-hidden flex flex-col max-h-[92vh]">
                {{-- Header --}}
                <div
                    class="border-b border-gray-100 px-6 py-4 flex-shrink-0 bg-gradient-to-r from-purple-50 to-transparent">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm" id="reactionTitle">Beri Reaksi</h3>
                                <p class="text-xs text-gray-500" id="reactionKegiatan">-</p>
                            </div>
                        </div>
                        <button onclick="closeReactionModal()" class="rounded-lg p-1 hover:bg-gray-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-y-auto p-6">
                    <input type="hidden" id="reactionActivityId">
                    <div id="reactionLoading" class="py-10 text-center">
                        <svg class="animate-spin h-8 w-8 mx-auto text-purple-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <p class="text-xs text-gray-500 mt-2">Memuat data reaksi...</p>
                    </div>
                    <div id="reactionContent" class="hidden space-y-3"></div>
                    <div id="reactionEmpty" class="hidden py-10 text-center">
                        <p class="text-sm text-gray-500">Belum ada master reaksi evaluasi</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="border-t border-gray-100 px-6 py-4 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 flex-shrink-0">
                    <div class="text-xs text-gray-500">
                        <span id="reactionProgress">0</span> dari <span id="reactionTotal">0</span> reaksi terisi
                    </div>
                    <div class="flex gap-3">
                        <button onclick="closeReactionModal()"
                            class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button onclick="submitReaction()" id="submitReactionBtn"
                            class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700 transition">
                            Simpan Reaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL HASIL REAKSI (HR PIC / Admin) --}}
    {{-- ========================================================= --}}
    <div id="reactionResultModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeReactionResultModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-auto overflow-hidden flex flex-col max-h-[92vh]">
                {{-- Header --}}
                <div
                    class="border-b border-gray-100 px-6 py-4 flex-shrink-0 bg-gradient-to-r from-amber-50 to-transparent">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50">
                                <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm">Hasil Reaksi Evaluasi</h3>
                                <p class="text-xs text-gray-500" id="reactionResultKegiatan">-</p>
                            </div>
                        </div>
                        <button onclick="closeReactionResultModal()"
                            class="rounded-lg p-1 hover:bg-gray-100 transition">
                            <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-y-auto p-6">
                    <div id="reactionResultLoading" class="py-10 text-center">
                        <svg class="animate-spin h-8 w-8 mx-auto text-amber-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <p class="text-xs text-gray-500 mt-2">Memuat hasil reaksi...</p>
                    </div>
                    <div id="reactionResultContent" class="hidden space-y-4"></div>
                    <div id="reactionResultEmpty" class="hidden py-10 text-center">
                        <p class="text-sm text-gray-500">Belum ada peserta yang memberikan reaksi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // =====================================================
    // DATA DARI BACKEND
    // =====================================================
    const orientationParticipants = @json($participantDetails ?? []);
    const activityAttendances = @json($attendancesByActivity ?? []);
    const activityStatusMap = @json(collect($kegiatanRows ?? [])->pluck('status', 'id'));

    let currentAttendanceData = [];
    let currentAttendanceSearch = '';

    // =====================================================
    // MODAL BERI REAKSI (Peserta)
    // =====================================================
    let currentReactionData = {};
    let currentReactionMasters = [];
    let currentReactionActivityId = null;
    let canEditReaction = false;

    function openReactionModal(activityId, activityTitle) {
        currentReactionActivityId = activityId;
        document.getElementById('reactionActivityId').value = activityId;
        document.getElementById('reactionKegiatan').textContent = activityTitle;
        currentReactionData = {};
        currentReactionMasters = [];

        document.getElementById('reactionModal').classList.remove('hidden');
        document.getElementById('reactionLoading').classList.remove('hidden');
        document.getElementById('reactionContent').classList.add('hidden');
        document.getElementById('reactionEmpty').classList.add('hidden');

        fetch(`/orientation/activity/${activityId}/reactions`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                document.getElementById('reactionLoading').classList.add('hidden');
                if (!res.success) throw new Error(res.message || 'Gagal memuat data');

                currentReactionMasters = res.data.masters || [];
                canEditReaction = res.data.can_edit ?? false;

                const existing = res.data.existing || {};
                Object.keys(existing).forEach(id => {
                    currentReactionData[id] = {
                        rating: existing[id].rating,
                        note: existing[id].note || ''
                    };
                });

                if (currentReactionMasters.length === 0) {
                    document.getElementById('reactionEmpty').classList.remove('hidden');
                    return;
                }

                renderReactionContent();
                document.getElementById('reactionContent').classList.remove('hidden');
            })
            .catch(err => {
                console.error(err);
                document.getElementById('reactionLoading').classList.add('hidden');
                Swal.fire('Error!', err.message || 'Gagal memuat data reaksi', 'error');
                closeReactionModal();
            });
    }

    function closeReactionModal() {
        document.getElementById('reactionModal').classList.add('hidden');
    }

    function renderReactionContent() {
        const container = document.getElementById('reactionContent');
        container.innerHTML = '';

        currentReactionMasters.forEach((master) => {
            const saved = currentReactionData[master.id] || {
                rating: 0,
                note: ''
            };
            const disabled = !canEditReaction;
            const card = document.createElement('div');
            card.className =
                'p-4 rounded-xl border border-gray-200 bg-white hover:border-purple-200 transition';
            card.innerHTML = `
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-800">${master.reaksi_name}</div>
                    </div>
                    ${disabled ? '<span class="text-[10px] text-gray-400">Read-only</span>' : ''}
                </div>
                <div class="flex items-center gap-2 mb-3">
                    ${[1, 2, 3, 4].map(n => {
                        const checked = saved.rating === n;
                        return `
                            <label class="flex-1 cursor-pointer ${disabled ? 'cursor-not-allowed opacity-60' : ''}">
                                <input type="radio" name="rating_${master.id}" value="${n}"
                                    ${checked ? 'checked' : ''}
                                    ${disabled ? 'disabled' : ''}
                                    onchange="updateReaction(${master.id}, 'rating', ${n})"
                                    class="peer sr-only">
                                <div class="flex flex-col items-center justify-center p-3 rounded-xl border-2 transition
                                            peer-checked:border-purple-500 peer-checked:bg-purple-50
                                            border-gray-200 hover:border-purple-300 ${disabled ? '' : 'cursor-pointer'}">
                                    <span class="text-lg font-bold text-gray-700 peer-checked:text-purple-600">${n}</span>
                                    <span class="text-[10px] text-gray-500 mt-0.5">${getRatingLabel(n)}</span>
                                </div>
                            </label>
                        `;
                    }).join('')}
                </div>
                <div>
                    <textarea rows="2" placeholder="Catatan (opsional)..."
                        onchange="updateReaction(${master.id}, 'note', this.value)"
                        ${disabled ? 'disabled' : ''}
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs
                               focus:border-purple-500 focus:ring-1 focus:ring-purple-200 resize-none
                               disabled:bg-gray-50 disabled:text-gray-400">${saved.note || ''}</textarea>
                </div>
            `;
            container.appendChild(card);
        });

        updateReactionProgress();
    }

    function getRatingLabel(rating) {
        const labels = {
            1: 'Kurang',
            2: 'Cukup',
            3: 'Baik',
            4: 'Sangat Baik'
        };
        return labels[rating] || '';
    }

    function updateReaction(reactionId, field, value) {
        if (!currentReactionData[reactionId]) {
            currentReactionData[reactionId] = {
                rating: 0,
                note: ''
            };
        }
        currentReactionData[reactionId][field] = value;
        if (field === 'rating') updateReactionProgress();
    }

    function updateReactionProgress() {
        const total = currentReactionMasters.length;
        const filled = Object.values(currentReactionData).filter(r => r.rating > 0).length;
        document.getElementById('reactionProgress').textContent = filled;
        document.getElementById('reactionTotal').textContent = total;
    }

    function submitReaction() {
        if (!canEditReaction) {
            Swal.fire('Info', 'Anda tidak memiliki akses untuk mengubah reaksi.', 'info');
            return;
        }

        const unfilled = currentReactionMasters.filter(m => {
            const r = currentReactionData[m.id];
            return !r || !r.rating || r.rating < 1;
        });

        if (unfilled.length > 0) {
            Swal.fire('Perhatian', `Masih ada ${unfilled.length} reaksi yang belum diisi rating.`, 'warning');
            return;
        }

        const reactions = Object.entries(currentReactionData)
            .filter(([_, r]) => r.rating > 0)
            .map(([reactionId, r]) => ({
                reaction_id: parseInt(reactionId),
                rating: r.rating,
                note: r.note || null,
            }));

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Simpan reaksi evaluasi untuk kegiatan ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#9333EA',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) return;
            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('{{ route('orientation.activity.reaction') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        orientation_activity_id: currentReactionActivityId,
                        reactions: reactions,
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            confirmButtonColor: '#9333EA'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan.', 'error');
                });
        });
    }

    // =====================================================
    // MODAL HASIL REAKSI (HR PIC / Admin)
    // =====================================================
    function openReactionResultModal(activityId, activityTitle) {
        document.getElementById('reactionResultKegiatan').textContent = activityTitle;
        document.getElementById('reactionResultModal').classList.remove('hidden');
        document.getElementById('reactionResultLoading').classList.remove('hidden');
        document.getElementById('reactionResultContent').classList.add('hidden');
        document.getElementById('reactionResultEmpty').classList.add('hidden');

        fetch(`/orientation/activity/${activityId}/reactions`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                document.getElementById('reactionResultLoading').classList.add('hidden');
                if (!res.success) throw new Error(res.message);

                const masters = res.data.masters || [];
                const results = res.data.results || {};
                const totalRespondents = res.data.total_respondents || 0;

                if (totalRespondents === 0) {
                    document.getElementById('reactionResultEmpty').classList.remove('hidden');
                    return;
                }

                const container = document.getElementById('reactionResultContent');
                container.innerHTML = '';

                // Header
                const header = document.createElement('div');
                header.className = 'text-xs text-gray-500 mb-2';
                header.innerHTML =
                    `Total responden: <span class="font-semibold text-gray-700">${totalRespondents}</span> peserta`;
                container.appendChild(header);

                masters.forEach(master => {
                    const data = results[master.id] || {
                        count: 0,
                        avg: null,
                        distribution: {},
                        participants: []
                    };
                    const dist = data.distribution || {
                        1: 0,
                        2: 0,
                        3: 0,
                        4: 0
                    };
                    const totalVotes = Object.values(dist).reduce((a, b) => a + b, 0);

                    // Build distribusi rows
                    const distRows = [4, 3, 2, 1].map(n => {
                        const count = dist[n] || 0;
                        const percent = totalVotes > 0 ? Math.round((count / totalVotes) * 100) : 0;
                        const colors = {
                            4: 'bg-green-500',
                            3: 'bg-blue-500',
                            2: 'bg-yellow-500',
                            1: 'bg-red-500'
                        };
                        const labels = {
                            4: 'Sangat Baik',
                            3: 'Baik',
                            2: 'Cukup',
                            1: 'Kurang'
                        };

                        return `
                <div class="flex items-center gap-2">
                    <div class="w-24 text-[10px] text-gray-600">${labels[n]}</div>
                    <div class="flex-1 h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full ${colors[n]}" style="width: ${percent}%"></div>
                    </div>
                    <div class="w-12 text-right text-[10px] font-semibold text-gray-700">${count}</div>
                </div>
            `;
                    }).join('');

                    // Build detail peserta
                    const detailHtml = data.participants.length > 0 ? `
            <details class="mt-2">
                <summary class="text-[10px] text-blue-600 cursor-pointer hover:underline">
                    Lihat detail ${data.participants.length} peserta
                </summary>
                <div class="mt-2 space-y-1">
                    ${data.participants.map(p => `
                        <div class="flex items-start justify-between gap-2 p-2 rounded bg-gray-50 text-xs">
                            <div class="flex-1">
                                <div class="font-medium text-gray-700">${p.nama}</div>
                                <div class="text-[10px] text-gray-500">${p.nik}</div>
                                ${p.note ? `<div class="text-[10px] text-gray-600 italic mt-1">"${p.note}"</div>` : ''}
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full font-bold text-xs
                                    ${p.rating === 4 ? 'bg-green-100 text-green-700' :
                                      p.rating === 3 ? 'bg-blue-100 text-blue-700' :
                                      p.rating === 2 ? 'bg-yellow-100 text-yellow-700' :
                                                       'bg-red-100 text-red-700'}">
                                    ${p.rating}
                                </span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </details>
        ` : '';

                    // Build card
                    const card = document.createElement('div');
                    card.className = 'p-4 rounded-xl border border-gray-200 bg-white';
                    card.innerHTML = `
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="flex-1">
                    <div class="text-sm font-semibold text-gray-800">${master.reaksi_name}</div>
                    <div class="text-[10px] text-gray-500">${data.count} responden</div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-amber-600">${data.avg ?? '-'}</div>
                    <div class="text-[10px] text-gray-500">rata-rata</div>
                </div>
            </div>

            <div class="space-y-1.5 mb-3">
                ${distRows}
            </div>

            ${detailHtml}
        `;
                    container.appendChild(card);
                });

                document.getElementById('reactionResultContent').classList.remove('hidden');
            })
            .catch(err => {
                console.error(err);
                document.getElementById('reactionResultLoading').classList.add('hidden');
                Swal.fire('Error!', err.message || 'Gagal memuat hasil reaksi', 'error');
                closeReactionResultModal();
            });
    }

    function closeReactionResultModal() {
        document.getElementById('reactionResultModal').classList.add('hidden');
    }

    // =====================================================
    // MODAL KEHADIRAN
    // =====================================================
    function openAttendanceModal(activityId, activityTitle, nextStatus) {
        document.getElementById('attendanceActivityId').value = activityId;
        document.getElementById('attendanceNextStatus').value = nextStatus;
        document.getElementById('attendanceKegiatan').textContent = activityTitle;

        const currentStatus = activityStatusMap[activityId] || 'pending';
        let title = 'Kehadiran & Penilaian';
        let btnLabel = 'Simpan';

        if (nextStatus === 'ongoing') {
            title = currentStatus === 'ongoing' ? 'Update Kehadiran & Nilai' : 'Mulai Kegiatan';
            btnLabel = currentStatus === 'ongoing' ? 'Simpan Perubahan' : 'Simpan & Mulai';
        } else if (nextStatus === 'completed') {
            title = currentStatus === 'completed' ? 'Edit Kehadiran & Nilai' : 'Selesaikan Kegiatan';
            btnLabel = currentStatus === 'completed' ? 'Simpan Perubahan' : 'Simpan & Selesai';
        }

        document.getElementById('attendanceTitle').textContent = title;
        document.getElementById('submitAttendanceBtn').textContent = btnLabel;

        const existing = activityAttendances[activityId] || {};
        currentAttendanceData = orientationParticipants.map(p => {
            const saved = existing[p.nik] || {};
            return {
                nik: p.nik,
                nama: p.nama,
                is_present: saved.is_present ?? false,
                pre_test: saved.pre_test ?? '',
                post_test: saved.post_test ?? '',
                note: saved.note ?? '',
            };
        });

        const searchInput = document.getElementById('attendanceSearch');
        if (searchInput) searchInput.value = '';
        currentAttendanceSearch = '';
        document.getElementById('attendanceSearchClear')?.classList.add('hidden');

        renderAttendanceTable();
        document.getElementById('attendanceModal').classList.remove('hidden');
    }

    function closeAttendanceModal() {
        document.getElementById('attendanceModal').classList.add('hidden');
    }

    function renderAttendanceTable() {
        const tbody = document.getElementById('attendanceTableBody');
        tbody.innerHTML = '';

        if (currentAttendanceData.length === 0) {
            tbody.innerHTML =
                `<tr><td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">Tidak ada peserta</td></tr>`;
            updateAttendanceCount();
            return;
        }

        const query = currentAttendanceSearch.toLowerCase().trim();
        const filteredData = currentAttendanceData
            .map((item, originalIndex) => ({
                ...item,
                _originalIndex: originalIndex
            }))
            .filter(item => {
                if (!query) return true;
                return (item.nama || '').toLowerCase().includes(query) ||
                    (item.nik || '').toLowerCase().includes(query) ||
                    (item.note || '').toLowerCase().includes(query);
            });

        if (filteredData.length === 0) {
            tbody.innerHTML =
                `<tr><td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">Tidak ada peserta yang cocok dengan pencarian "<span class="font-semibold">${currentAttendanceSearch}</span>"</td></tr>`;
            updateAttendanceCount();
            return;
        }

        filteredData.forEach((item, displayIndex) => {
            const index = item._originalIndex;
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-blue-50/30 transition';

            const preTestDisabled = !item.is_present;
            const postTestDisabled = !item.is_present || item.pre_test === '' || item.pre_test === null;

            tr.innerHTML = `
                <td class="px-4 py-3 text-center text-sm font-semibold text-gray-500">
                    ${String(displayIndex + 1).padStart(2, '0')}
                </td>
                <td class="px-4 py-3 text-center">
                    <input type="checkbox" ${item.is_present ? 'checked' : ''}
                        onchange="updateAttendance(${index}, 'is_present', this.checked)"
                        class="attendance-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4 cursor-pointer">
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">
                            ${getInitials(item.nama)}
                        </div>
                        <span class="text-sm font-medium text-gray-800">${highlightText(item.nama, query)}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600 font-mono">${highlightText(item.nik, query)}</td>
                <td class="px-4 py-3 text-center">
                    <input type="number" min="0" max="100" value="${item.pre_test}"
                        onchange="updateAttendance(${index}, 'pre_test', this.value)"
                        placeholder="${preTestDisabled ? 'N/A' : '0-100'}"
                        ${preTestDisabled ? 'disabled' : ''}
                        class="w-20 rounded-lg border border-gray-200 px-2 py-1 text-sm text-center
                            focus:border-blue-500 focus:ring-1 focus:ring-blue-200
                            disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                </td>
                <td class="px-4 py-3 text-center">
                    <input type="number" min="0" max="100" value="${item.post_test}"
                        onchange="updateAttendance(${index}, 'post_test', this.value)"
                        placeholder="${postTestDisabled ? 'N/A' : '0-100'}"
                        ${postTestDisabled ? 'disabled' : ''}
                        class="w-20 rounded-lg border border-gray-200 px-2 py-1 text-sm text-center
                            focus:border-blue-500 focus:ring-1 focus:ring-blue-200
                            disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                </td>
                <td class="px-4 py-3">
                    <textarea rows="2" 
                        onchange="updateAttendance(${index}, 'note', this.value)"
                        placeholder="Catatan..."
                        class="w-full min-w-[260px] rounded-lg border border-gray-200 px-3 py-2 text-xs 
                            focus:border-blue-500 focus:ring-1 focus:ring-blue-200 resize-none">${item.note || ''}</textarea>
                </td>
            `;
            tbody.appendChild(tr);
        });

        updateAttendanceCount();
    }

    function filterAttendanceTable() {
        const input = document.getElementById('attendanceSearch');
        currentAttendanceSearch = input.value || '';
        const clearBtn = document.getElementById('attendanceSearchClear');
        if (clearBtn) clearBtn.classList.toggle('hidden', !currentAttendanceSearch);
        renderAttendanceTable();
    }

    function clearAttendanceSearch() {
        const input = document.getElementById('attendanceSearch');
        if (input) input.value = '';
        currentAttendanceSearch = '';
        document.getElementById('attendanceSearchClear')?.classList.add('hidden');
        renderAttendanceTable();
    }

    function highlightText(text, query) {
        if (!text) return '';
        if (!query) return text;
        const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return text.replace(regex, '<mark class="bg-yellow-200 text-gray-900 rounded px-0.5">$1</mark>');
    }

    function updateAttendance(index, field, value) {
        if (!currentAttendanceData[index]) return;
        currentAttendanceData[index][field] = value;

        if (field === 'is_present') {
            if (!value) {
                currentAttendanceData[index].pre_test = '';
                currentAttendanceData[index].post_test = '';
            }
            renderAttendanceTable();
            return;
        }
        if (field === 'pre_test') {
            if (value === '' || value === null) {
                currentAttendanceData[index].post_test = '';
            }
            renderAttendanceTable();
            return;
        }
        if (field === 'post_test') return;
    }

    function toggleAllAttendance(checkbox) {
        currentAttendanceData.forEach((item) => {
            item.is_present = checkbox.checked;
            if (!checkbox.checked) {
                item.pre_test = '';
                item.post_test = '';
            }
        });
        renderAttendanceTable();
    }

    function updateAttendanceCount() {
        const hadir = currentAttendanceData.filter(i => i.is_present).length;
        document.getElementById('attendanceCount').textContent = hadir;
        document.getElementById('attendanceTotal').textContent = currentAttendanceData.length;
        const checkAll = document.getElementById('checkAllAttendance');
        if (checkAll) {
            checkAll.checked = currentAttendanceData.length > 0 && hadir === currentAttendanceData.length;
            checkAll.indeterminate = hadir > 0 && hadir < currentAttendanceData.length;
        }
    }

    function getInitials(name) {
        if (!name) return '?';
        const words = name.trim().split(' ');
        if (words.length === 1) return words[0].charAt(0).toUpperCase();
        return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
    }

    function submitAttendance() {
        const id = document.getElementById('attendanceActivityId').value;
        const nextStatus = document.getElementById('attendanceNextStatus').value;
        if (!id) return;

        const currentStatus = activityStatusMap[id] || 'pending';
        let confirmText = 'Simpan kehadiran?';

        if (nextStatus === 'ongoing' && currentStatus === 'pending') {
            confirmText = 'Simpan kehadiran & mulai kegiatan?';
        } else if (nextStatus === 'completed' && currentStatus !== 'completed') {
            confirmText = 'Simpan kehadiran & tandai kegiatan selesai?';
        } else {
            confirmText = 'Simpan perubahan kehadiran & nilai?';
        }

        Swal.fire({
            title: 'Konfirmasi',
            text: confirmText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3B82F6',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) return;
            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('{{ route('orientation.activity.attendance') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: id,
                        next_status: nextStatus,
                        attendances: currentAttendanceData.map(item => ({
                            nik: item.nik,
                            is_present: !!item.is_present,
                            pre_test: item.pre_test === '' ? null : parseFloat(item
                                .pre_test),
                            post_test: item.post_test === '' ? null : parseFloat(item
                                .post_test),
                            note: item.note || null,
                        }))
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            confirmButtonColor: '#3B82F6'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan.', 'error');
                });
        });
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAttendanceModal();
            closeReactionModal();
            closeReactionResultModal();
        }
    });
</script>
