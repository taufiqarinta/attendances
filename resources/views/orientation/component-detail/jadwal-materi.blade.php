<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
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
        {{-- Mobile: Card View --}}
        <div class="block sm:hidden">
            <div class="divide-y divide-gray-100">
                @foreach ($kegiatanRows as $index => $row)
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
                        $hasScore = isset($row['score']) && $row['score'] !== null && $row['score'] !== '';
                        $scoreValue = $hasScore ? (int) $row['score'] : '';
                        $scoreNote = isset($row['score_note']) && $row['score_note'] !== null ? $row['score_note'] : '';
                    @endphp
                    <div class="p-4 space-y-2" data-activity-id="{{ $row['id'] }}">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-sm text-gray-500">#{{ $index + 1 }}</span>
                                <div>
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
                        <div class="grid grid-cols-2 gap-1 text-xs">
                            <div>
                                <span class="text-gray-500">Tanggal</span>
                                <div class="font-medium">{{ $row['tanggal_formatted'] ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500">Waktu</span>
                                <div class="font-medium">{{ $row['waktu_range'] ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500">PIC</span>
                                <div class="font-medium">{{ $row['pic_name'] ?? '-' }}</div>
                            </div>
                            @if ($canManage)
                                <div>
                                    <span class="text-gray-500">Nilai</span>
                                    @if ($hasScore)
                                        <button
                                            onclick="openDetailNilai({{ $row['id'] }}, '{{ addslashes($row['title']) }}', '{{ $scoreValue }}', '{{ addslashes($scoreNote) }}')"
                                            class="font-medium text-green-600 font-semibold hover:underline cursor-pointer text-left">
                                            {{ $scoreValue }}
                                        </button>
                                    @else
                                        <div class="text-gray-400">-</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        @if ($canManage)
                        <div class="flex flex-col gap-2">
                            @if (in_array($row['status'], ['ongoing', 'completed']))
                                <button
                                    onclick="openNilaiModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', '{{ $scoreValue }}', '{{ addslashes($scoreNote) }}')"
                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 transition w-full">
                                    {{ $hasScore ? '+ Edit Nilai' : '+ Masukkan Nilai' }}
                                </button>
                            @endif
                            <div class="flex justify-end">
                                @if ($row['status'] === 'completed' || $row['status'] === 'cancelled')
                                    <button
                                        class="rounded-lg bg-gray-100 px-4 py-2 text-xs font-medium text-gray-400 cursor-not-allowed opacity-50 w-full sm:w-auto"
                                        disabled>
                                        {{ $row['status'] === 'cancelled' ? 'Dibatalkan' : 'Selesai' }}
                                    </button>
                                @elseif($row['status'] === 'ongoing')
                                    <button onclick="updateStatus({{ $row['id'] }}, 'completed')"
                                        class="rounded-lg bg-green-600 px-4 py-2 text-xs font-semibold text-white hover:bg-green-700 transition w-full">
                                        Selesai
                                    </button>
                                @elseif($row['status'] === 'pending')
                                    <button onclick="updateStatus({{ $row['id'] }}, 'ongoing')"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition w-full sm:w-auto">
                                        Mulai
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if ($canManage)
                            {{-- Tampilkan waktu mulai dan selesai jika ada --}}
                            @if ($row['started_at'] ?? false)
                                <div class="text-[10px] text-green-600">
                                    🟢 Dimulai: {{ $row['started_at'] }}
                                </div>
                            @endif
                            @if ($row['completed_at'] ?? false)
                                <div class="text-[10px] text-blue-600">
                                    ✅ Selesai: {{ $row['completed_at'] }}
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Desktop Table --}}
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
                            <th class="px-4 py-2.5 text-center font-semibold">Nilai</th>
                            <th class="px-4 py-2.5 text-center font-semibold">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($kegiatanRows as $index => $row)
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
                            $hasScore = isset($row['score']) && $row['score'] !== null && $row['score'] !== '';
                            $scoreValue = $hasScore ? (int) $row['score'] : '';
                            $scoreNote =
                                isset($row['score_note']) && $row['score_note'] !== null ? $row['score_note'] : '';
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
                            <td class="px-4 py-3 text-sm">
                                {{ $row['waktu_range'] ?? '-' }}
                            </td>
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
                            <td class="px-4 py-3 text-center">
                                @if ($hasScore)
                                    <button
                                        onclick="openDetailNilai({{ $row['id'] }}, '{{ addslashes($row['title']) }}', '{{ $scoreValue }}', '{{ addslashes($scoreNote) }}')"
                                        class="inline-flex items-center justify-center rounded-lg bg-green-50 px-3 py-1 text-sm font-bold text-green-700 hover:bg-green-100 hover:scale-105 transition cursor-pointer">
                                        {{ $scoreValue }}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1 text-green-500"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-2">
                                    @if (in_array($row['status'], ['ongoing', 'completed']))
                                        <button
                                            onclick="openNilaiModal({{ $row['id'] }}, '{{ addslashes($row['title']) }}', '{{ $scoreValue }}', '{{ addslashes($scoreNote) }}')"
                                            class="inline-flex items-center gap-1 rounded-lg border border-dashed border-gray-300 bg-white px-3 py-1.5 text-[10px] font-medium text-gray-500 hover:border-blue-400 hover:text-blue-600 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            {{ $hasScore ? 'Edit Nilai' : 'Nilai' }}
                                        </button>
                                    @endif

                                    @if ($row['status'] === 'completed' || $row['status'] === 'cancelled')
                                        <button
                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-[10px] font-medium text-gray-400 cursor-not-allowed opacity-50"
                                            disabled>
                                            {{ $row['status'] === 'cancelled' ? 'Dibatalkan' : 'Selesai' }}
                                        </button>
                                    @elseif($row['status'] === 'ongoing')
                                        <button onclick="updateStatus({{ $row['id'] }}, 'completed')"
                                            class="rounded-lg bg-green-600 px-3 py-1.5 text-[10px] font-semibold text-white hover:bg-green-700 transition">
                                            Selesai
                                        </button>
                                    @elseif($row['status'] === 'pending')
                                        <button onclick="updateStatus({{ $row['id'] }}, 'ongoing')"
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

        {{-- Footer Summary --}}
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
        {{-- Empty State --}}
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

    {{-- MODAL INPUT NILAI --}}
    <div id="nilaiModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeNilaiModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm" id="modalTitle">Masukkan Nilai</h3>
                                <p class="text-xs text-gray-500" id="modalKegiatan">-</p>
                            </div>
                        </div>
                        <button onclick="closeNilaiModal()" class="rounded-lg p-1 hover:bg-gray-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <input type="hidden" id="activity_id" value="">
                    <div>
                        <label for="score" class="block text-sm font-medium text-gray-700 mb-1">Nilai <span
                                class="text-red-500">*</span></label>
                        <input type="number" id="score" name="score" min="0" max="100"
                            step="1" placeholder="Masukkan nilai (0-100)"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition">
                        <p class="text-[10px] text-gray-400 mt-1">Masukkan nilai antara 0 - 100</p>
                    </div>
                    <div>
                        <label for="score_note"
                            class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea id="score_note" name="score_note" rows="2" placeholder="Tambahkan catatan jika diperlukan..."
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition resize-none"></textarea>
                    </div>
                </div>
                <div class="border-t border-gray-100 px-6 py-4 bg-gray-50/50 flex gap-3 justify-end">
                    <button onclick="closeNilaiModal()"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button onclick="submitNilai()"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">
                        Simpan Nilai
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL NILAI --}}
    <div id="detailNilaiModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDetailNilai()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm">Detail Nilai</h3>
                                <p class="text-xs text-gray-500" id="detailKegiatan">-</p>
                            </div>
                        </div>
                        <button onclick="closeDetailNilai()" class="rounded-lg p-1 hover:bg-gray-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <span class="text-sm text-gray-600">Nilai</span>
                        <span class="text-2xl font-bold text-green-700" id="detailNilai">-</span>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <label class="text-sm text-gray-600 block mb-2">Keterangan</label>
                        <p class="text-sm text-gray-800" id="detailKeterangan">-</p>
                    </div>
                </div>
                <div class="border-t border-gray-100 px-6 py-4 bg-gray-50/50 flex justify-end">
                    <button onclick="closeDetailNilai()"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Fungsi untuk membuka modal input nilai
    function openNilaiModal(id, kegiatan, score = '', score_note = '') {
        document.getElementById('activity_id').value = id;

        const hasScore = score && score !== '' && score !== 'null' && score !== null;

        if (hasScore) {
            document.getElementById('modalTitle').textContent = 'Edit Nilai';
            document.getElementById('score').value = score;
            document.getElementById('score_note').value = score_note || '';
        } else {
            document.getElementById('modalTitle').textContent = 'Masukkan Nilai';
            document.getElementById('score').value = '';
            document.getElementById('score_note').value = '';
        }

        document.getElementById('nilaiModal').classList.remove('hidden');
        document.getElementById('modalKegiatan').textContent = kegiatan;

        setTimeout(() => {
            document.getElementById('score').focus();
        }, 100);
    }

    // Fungsi untuk menutup modal input nilai
    function closeNilaiModal() {
        document.getElementById('nilaiModal').classList.add('hidden');
        document.getElementById('activity_id').value = '';
    }

    // Fungsi untuk submit nilai
    function submitNilai() {
        const id = document.getElementById('activity_id').value;
        const score = document.getElementById('score').value;
        const score_note = document.getElementById('score_note').value;

        if (!id) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'ID kegiatan tidak ditemukan. Silakan refresh halaman.',
                confirmButtonColor: '#EF4444',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!score || score < 0 || score > 100) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Mohon masukkan nilai yang valid (0-100)',
                confirmButtonColor: '#3B82F6',
                confirmButtonText: 'OK'
            });
            return;
        }

        const kegiatanName = document.getElementById('modalKegiatan').textContent;

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menyimpan nilai ' + score + ' untuk "' + kegiatanName + '"?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3B82F6',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('{{ route('orientation.activity.score') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: id,
                            score: score,
                            score_note: score_note
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Nilai ' + score + ' berhasil disimpan untuk "' +
                                    kegiatanName + '"',
                                confirmButtonColor: '#3B82F6',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Gagal menyimpan nilai',
                                confirmButtonColor: '#EF4444',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menyimpan nilai: ' + error.message,
                            confirmButtonColor: '#EF4444',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        });

        closeNilaiModal();
    }

    // Fungsi untuk update status kegiatan
    function updateStatus(id, status) {
        const statusLabels = {
            'pending': 'Belum Dimulai',
            'ongoing': 'Berlangsung',
            'completed': 'Selesai',
            'cancelled': 'Dibatalkan'
        };

        const statusColors = {
            'pending': '#6B7280',
            'ongoing': '#3B82F6',
            'completed': '#22C55E',
            'cancelled': '#EF4444'
        };

        let statusMessage = 'Apakah Anda yakin ingin mengubah status menjadi "' + statusLabels[status] + '"?';

        if (status === 'ongoing') {
            statusMessage += '\n\n⏰ Waktu mulai akan dicatat: ' + new Date().toLocaleString('id-ID');
        } else if (status === 'completed') {
            statusMessage += '\n\n⏰ Waktu selesai akan dicatat: ' + new Date().toLocaleString('id-ID');
        }

        Swal.fire({
            title: 'Konfirmasi',
            text: statusMessage,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: statusColors[status],
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mengupdate...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('{{ route('orientation.activity.status') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: id,
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let successMessage = 'Status berhasil diubah menjadi "' + statusLabels[status] +
                                '"';

                            if (data.data && data.data.started_at) {
                                successMessage += '\n\n🟢 Dimulai: ' + data.data.started_at;
                            }
                            if (data.data && data.data.completed_at) {
                                successMessage += '\n✅ Selesai: ' + data.data.completed_at;
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: successMessage,
                                confirmButtonColor: '#3B82F6',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Gagal mengupdate status',
                                confirmButtonColor: '#EF4444',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengupdate status',
                            confirmButtonColor: '#EF4444',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        });
    }

    // Fungsi untuk membuka modal detail nilai
    function openDetailNilai(id, kegiatan, score, score_note) {
        const displayScore = (score && score !== 'null' && score !== 'undefined' && score !== '') ? score : '-';
        const displayNote = (score_note && score_note !== 'null' && score_note !== 'undefined' && score_note !== '') ?
            score_note : 'Tidak ada keterangan';

        document.getElementById('detailNilaiModal').classList.remove('hidden');
        document.getElementById('detailKegiatan').textContent = kegiatan || '-';
        document.getElementById('detailNilai').textContent = displayScore;
        document.getElementById('detailKeterangan').textContent = displayNote;
    }

    // Fungsi untuk menutup modal detail nilai
    function closeDetailNilai() {
        document.getElementById('detailNilaiModal').classList.add('hidden');
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeNilaiModal();
            closeDetailNilai();
        }
    });

    // Close modal with Enter key when input focused
    document.addEventListener('DOMContentLoaded', function() {
        const scoreInput = document.getElementById('score');
        if (scoreInput) {
            scoreInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    submitNilai();
                }
            });
        }
    });
</script>
