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
                        @if ($canManage)
                            <div class="flex flex-col gap-2 pt-2 border-t border-gray-100">
                                @if ($row['status'] === 'cancelled')
                                    <button disabled
                                        class="rounded-lg bg-gray-100 px-4 py-2 text-xs font-medium text-gray-400 cursor-not-allowed opacity-50 w-full">
                                        Dibatalkan
                                    </button>
                                @elseif($row['status'] === 'completed')
                                    <div
                                        class="inline-flex items-center justify-center gap-1 rounded-lg bg-green-50 px-4 py-2 text-xs font-semibold text-green-700 border border-green-200 w-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Selesai
                                    </div>
                                    <button
                                        onclick="openAttendanceModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', 'completed')"
                                        class="rounded-lg border border-blue-300 bg-blue-50 px-4 py-2 text-xs font-medium text-blue-700 hover:bg-blue-100 transition w-full">
                                        Edit Kehadiran & Nilai
                                    </button>
                                @elseif($row['status'] === 'ongoing')
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
                                @elseif($row['status'] === 'pending')
                                    <button
                                        onclick="openAttendanceModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', 'ongoing')"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition w-full">
                                        Mulai
                                    </button>
                                @endif

                                {{-- Timestamp --}}
                                @if ($row['started_at'] ?? false)
                                    <div class="text-[10px] text-green-600">🟢 Dimulai: {{ $row['started_at'] }}</div>
                                @endif
                                @if ($row['completed_at'] ?? false)
                                    <div class="text-[10px] text-blue-600">✅ Selesai: {{ $row['completed_at'] }}</div>
                                @endif
                            </div>
                        @endif
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
                        @if ($canManage)
                            <th class="px-4 py-2.5 font-semibold">Status</th>
                            <th class="px-4 py-2.5 text-center font-semibold">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($kegiatanRows as $index => $row)
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
                            @if ($canManage)
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
                                        @elseif($row['status'] === 'ongoing')
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
                                        @elseif($row['status'] === 'pending')
                                            <button
                                                onclick="openAttendanceModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', 'ongoing')"
                                                class="rounded-lg bg-blue-600 px-3 py-1.5 text-[10px] font-semibold text-white hover:bg-blue-700 transition">
                                                Mulai
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            @endif
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
                                        Catatan
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody" class="divide-y divide-gray-100">
                                {{-- Diisi via JS --}}
                            </tbody>
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
</div>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // =====================================================
    // DATA DARI BACKEND
    // =====================================================
    const orientationParticipants = @json($participantDetails ?? []);
    const activityAttendances = @json($attendancesByActivity ?? []);

    // Map status per activity (untuk judul modal dinamis)
    const activityStatusMap = @json(collect($kegiatanRows ?? [])->pluck('status', 'id'));

    let currentAttendanceData = [];

    // =====================================================
    // MODAL KEHADIRAN
    // =====================================================
    function openAttendanceModal(activityId, activityTitle, nextStatus) {
        document.getElementById('attendanceActivityId').value = activityId;
        document.getElementById('attendanceNextStatus').value = nextStatus;
        document.getElementById('attendanceKegiatan').textContent = activityTitle;

        // Tentukan judul
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

        // Ambil data attendance existing
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

        // Reset search
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

    let currentAttendanceSearch = '';

    function renderAttendanceTable() {
        const tbody = document.getElementById('attendanceTableBody');
        tbody.innerHTML = '';

        if (currentAttendanceData.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">
                    Tidak ada peserta
                </td>
            </tr>`;
            updateAttendanceCount();
            return;
        }

        // Filter by search
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
            tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">
                    Tidak ada peserta yang cocok dengan pencarian "<span class="font-semibold">${currentAttendanceSearch}</span>"
                </td>
            </tr>`;
            updateAttendanceCount();
            return;
        }

        filteredData.forEach((item, displayIndex) => {
            const index = item._originalIndex; // index asli untuk update
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

        // Update info filter
        const filterInfo = document.getElementById('attendanceFilterInfo');
        if (filterInfo) {
            if (query) {
                filterInfo.textContent = `• Ditampilkan ${filteredData.length} dari ${currentAttendanceData.length}`;
                filterInfo.classList.remove('hidden');
            } else {
                filterInfo.classList.add('hidden');
            }
        }

        updateAttendanceCount();
    }


    function filterAttendanceTable() {
        const input = document.getElementById('attendanceSearch');
        currentAttendanceSearch = input.value || '';
        const clearBtn = document.getElementById('attendanceSearchClear');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', !currentAttendanceSearch);
        }
        renderAttendanceTable();
    }

    function clearAttendanceSearch() {
        const input = document.getElementById('attendanceSearch');
        if (input) input.value = '';
        currentAttendanceSearch = '';
        document.getElementById('attendanceSearchClear')?.classList.add('hidden');
        renderAttendanceTable();
    }
    /**
     * Highlight text yang cocok dengan query
     */
    function highlightText(text, query) {
        if (!text) return '';
        if (!query) return text;

        const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return text.replace(regex, '<mark class="bg-yellow-200 text-gray-900 rounded px-0.5">$1</mark>');
    }

    function updateAttendance(index, field, value) {
        if (!currentAttendanceData[index]) return;

        currentAttendanceData[index][field] = value;

        // Kalau hadir di-uncheck → reset pre test & post test
        if (field === 'is_present') {
            if (!value) {
                currentAttendanceData[index].pre_test = '';
                currentAttendanceData[index].post_test = '';
            }
            renderAttendanceTable();
            return;
        }

        // Kalau pre test diubah
        if (field === 'pre_test') {
            // Kalau pre test dikosongkan → reset post test
            if (value === '' || value === null) {
                currentAttendanceData[index].post_test = '';
            }
            renderAttendanceTable();
            return;
        }

        // Kalau post test diubah, cukup update
        if (field === 'post_test') {
            return;
        }

        // Field lain (note), tidak perlu re-render
    }

    function toggleAllAttendance(checkbox) {
        currentAttendanceData.forEach((item) => {
            item.is_present = checkbox.checked;
            // Kalau uncheck semua → reset pre & post test
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
        }
    });
</script>
