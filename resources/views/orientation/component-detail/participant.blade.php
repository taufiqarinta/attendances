{{-- Daftar Peserta --}}
@if (isset($participantDetails) && $participantDetails->count() > 0)
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm mt-6" x-data="participantTable()">
        {{-- HEADER --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-4 sm:px-6 py-3.5 flex-wrap gap-3">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 text-sm">Daftar Peserta</h3>
                    <p class="text-xs text-gray-500">
                        Total {{ $participantStats['total'] ?? 0 }} peserta ·
                        {{ $totalActivities ?? 0 }} kegiatan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2 text-[10px]">
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-green-700 font-semibold">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        ≥80%
                    </span>
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-yellow-50 px-2 py-0.5 text-yellow-700 font-semibold">
                        <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                        50-79%
                    </span>
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 text-red-700 font-semibold">
                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                        &lt;50%
                    </span>
                </div>

                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-5.2-5.2m1.7-5.3a7 7 0 11-14 0a7 7 0 0114 0z" />
                    </svg>
                    <input type="text" x-model="search" placeholder="Cari nama / NIK..."
                        class="h-9 w-full sm:w-56 rounded-lg border border-gray-200 pl-9 pr-3 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- STATISTIK RINGKAS --}}
        <div
            class="grid grid-cols-3 gap-3 px-4 sm:px-6 py-3 bg-gradient-to-r from-blue-50/50 to-transparent border-b border-gray-100">
            <div class="text-center">
                <div class="text-xs text-gray-500">Rata-rata Kehadiran</div>
                <div class="text-lg font-bold text-blue-600">
                    {{ round(collect($participantDetails)->avg('attendance_percentage') ?? 0) }}%
                </div>
            </div>
            <div class="text-center">
                <div class="text-xs text-gray-500">Rata-rata Pre Test</div>
                <div class="text-lg font-bold text-purple-600">
                    {{ collect($participantDetails)->whereNotNull('pre_test_avg')->avg('pre_test_avg') ? round(collect($participantDetails)->whereNotNull('pre_test_avg')->avg('pre_test_avg'), 1) : '-' }}
                </div>
            </div>
            <div class="text-center">
                <div class="text-xs text-gray-500">Rata-rata Post Test</div>
                <div class="text-lg font-bold text-green-600">
                    {{ collect($participantDetails)->whereNotNull('post_test_avg')->avg('post_test_avg') ? round(collect($participantDetails)->whereNotNull('post_test_avg')->avg('post_test_avg'), 1) : '-' }}
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[1000px]">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 w-10"></th>
                        <th class="px-3 py-3 w-12">No</th>
                        <th class="px-3 py-3">Peserta</th>
                        <th class="px-3 py-3 hidden md:table-cell">Jabatan</th>
                        <th class="px-3 py-3 text-center">Kehadiran</th>
                        <th class="px-3 py-3 text-center">Pre Test</th>
                        <th class="px-3 py-3 text-center">Post Test</th>
                        <th class="px-3 py-3 text-center">Progress</th>
                        <th class="px-3 py-3 text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($participantDetails as $index => $participant)
                        @php
                            $nik = $participant['nik'] ?? '';
                            $hadir = $participant['hadir_count'] ?? 0;
                            $total = $participant['total_activities'] ?? 0;
                            $percent = $participant['attendance_percentage'] ?? 0;
                            $preTest = $participant['pre_test_avg'] ?? null;
                            $postTest = $participant['post_test_avg'] ?? null;
                            $status = $participant['status'] ?? 'pending';

                            // warna kehadiran
                            if ($percent >= 80) {
                                $hadirText = 'text-green-600';
                                $hadirBg = 'bg-green-500';
                            } elseif ($percent >= 50) {
                                $hadirText = 'text-yellow-600';
                                $hadirBg = 'bg-yellow-500';
                            } elseif ($percent > 0) {
                                $hadirText = 'text-orange-600';
                                $hadirBg = 'bg-orange-500';
                            } else {
                                $hadirText = 'text-red-600';
                                $hadirBg = 'bg-red-500';
                            }

                            // warna nilai
                            $scoreColor = function ($score) {
                                if ($score === null) {
                                    return 'text-gray-400';
                                }
                                if ($score >= 80) {
                                    return 'text-green-600';
                                }
                                if ($score >= 60) {
                                    return 'text-yellow-600';
                                }
                                return 'text-red-600';
                            };

                            // initials
                            $words = explode(' ', trim($participant['nama'] ?? '?'));
                            $initials =
                                count($words) === 1
                                    ? strtoupper(substr($words[0], 0, 1))
                                    : strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
                        @endphp

                        {{-- ROW UTAMA --}}
                        <tr class="participant-row border-b border-gray-100 hover:bg-blue-50/30 transition-colors"
                            data-search="{{ strtolower(($participant['nama'] ?? '') . ' ' . $nik . ' ' . ($participant['jabatan'] ?? '')) }}">
                            <td class="px-3 py-3 text-center">
                                <button @click="toggleExpand('{{ $nik }}')" type="button"
                                    class="p-1 rounded hover:bg-gray-100 transition">
                                    <svg class="h-4 w-4 text-gray-400 transition-transform duration-200"
                                        :class="expandedRows.includes('{{ $nik }}') ? 'rotate-90' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </td>
                            <td class="px-3 py-3 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-semibold flex-shrink-0">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-gray-800 truncate">{{ $participant['nama'] }}
                                        </div>
                                        <div class="text-[10px] text-gray-500 font-mono">{{ $nik }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-gray-600 hidden md:table-cell">
                                <div class="text-xs truncate max-w-[150px]">{{ $participant['jabatan'] ?? '-' }}</div>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <div class="inline-flex flex-col items-center gap-1">
                                    <span
                                        class="text-sm font-bold {{ $hadirText }}">{{ $hadir }}/{{ $total }}</span>
                                    <div class="w-16 h-1.5 rounded-full bg-gray-200 overflow-hidden">
                                        <div class="h-full {{ $hadirBg }}" style="width: {{ $percent }}%">
                                        </div>
                                    </div>
                                    <span class="text-[10px] text-gray-500">{{ $percent }}%</span>
                                </div>
                            </td>
                            {{-- Pre Test --}}
                            <td class="px-3 py-3 text-center">
                                @if ($preTest !== null)
                                    <div class="text-sm font-bold {{ $scoreColor($preTest) }}">{{ $preTest }}
                                    </div>
                                    <div class="text-[10px] text-gray-400">avg</div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- Post Test + Keterangan --}}
                            <td class="px-3 py-3 text-center">
                                @if ($postTest !== null)
                                    @php
                                        // Bandingkan pre vs post
                                        $diff = null;
                                        $trend = null;
                                        $trendLabel = '';
                                        $trendClass = '';
                                        $trendIcon = '';

                                        if ($preTest !== null) {
                                            $diff = round($postTest - $preTest, 1);
                                            if ($diff > 0) {
                                                $trend = 'up';
                                                $trendLabel = 'Meningkat';
                                                $trendClass = 'bg-green-50 text-green-700';
                                                $trendIcon = '↑';
                                            } elseif ($diff < 0) {
                                                $trend = 'down';
                                                $trendLabel = 'Menurun';
                                                $trendClass = 'bg-red-50 text-red-700';
                                                $trendIcon = '↓';
                                            } else {
                                                $trend = 'same';
                                                $trendLabel = 'Stabil';
                                                $trendClass = 'bg-yellow-50 text-yellow-700';
                                                $trendIcon = '=';
                                            }
                                        }
                                    @endphp

                                    <div class="text-sm font-bold {{ $scoreColor($postTest) }}">{{ $postTest }}
                                    </div>
                                    <div class="text-[10px] text-gray-400">avg</div>

                                    @if ($trend)
                                        <div class="mt-1 inline-flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[9px] font-semibold {{ $trendClass }}"
                                            title="Pre: {{ $preTest }} → Post: {{ $postTest }} ({{ $diff > 0 ? '+' : '' }}{{ $diff }})">
                                            <span>{{ $trendIcon }}</span>
                                            {{ $trendLabel }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                @if ($status === 'completed')
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-[10px] font-semibold text-green-700">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Selesai
                                    </span>
                                @elseif ($status === 'active')
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                <button type="button" @click="openDetailByNik('{{ $nik }}')"
                                    class="inline-flex items-center gap-1 rounded-lg border border-blue-300 bg-blue-50 px-2 py-1 text-[10px] font-medium text-blue-700 hover:bg-blue-100 transition">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </button>
                            </td>
                        </tr>

                        {{-- EXPANDED ROW --}}
                        <tr x-show="expandedRows.includes('{{ $nik }}')" x-cloak>
                            <td colspan="9" class="p-0 bg-blue-50/30 border-b border-blue-100">
                                <div class="p-4">
                                    <div class="text-xs font-semibold text-gray-600 mb-2">
                                        Detail per Kegiatan — {{ $participant['nama'] }}
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @forelse ($participant['activity_breakdown'] ?? [] as $idx => $act)
                                            <div
                                                class="flex items-center gap-2 p-2 rounded-lg bg-white border border-gray-100">
                                                <div
                                                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-gray-100 text-gray-600 text-[10px] font-semibold flex-shrink-0">
                                                    {{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-xs font-medium text-gray-800 truncate">
                                                        {{ $act['title'] }}</div>
                                                    <div class="text-[10px] text-gray-500">{{ $act['tanggal'] }}</div>
                                                </div>
                                                @if (!empty($act['is_present']))
                                                    <span
                                                        class="rounded-full bg-green-50 px-2 py-0.5 text-[10px] font-semibold text-green-700">Hadir</span>
                                                @else
                                                    <span
                                                        class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-semibold text-red-700">Tidak</span>
                                                @endif
                                                <div class="w-12 text-center">
                                                    <div class="text-[9px] text-gray-400">Pre</div>
                                                    <div
                                                        class="text-xs font-bold {{ $scoreColor($act['pre_test'] ?? null) }}">
                                                        {{ $act['pre_test'] ?? '-' }}
                                                    </div>
                                                </div>
                                                <div class="w-12 text-center">
                                                    <div class="text-[9px] text-gray-400">Post</div>
                                                    <div
                                                        class="text-xs font-bold {{ $scoreColor($act['post_test'] ?? null) }}">
                                                        {{ $act['post_test'] ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-xs text-gray-400 py-2">Belum ada kegiatan</div>
                                        @endforelse
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Empty State --}}
        <div x-show="visibleCount === 0" class="py-12 text-center">
            <p class="text-sm text-gray-500">Tidak ada peserta yang cocok dengan pencarian</p>
        </div>

        {{-- FOOTER --}}
        <div class="border-t border-gray-100 px-4 sm:px-6 py-3 bg-gray-50 rounded-b-2xl">
            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-gray-600">
                <div class="flex items-center gap-4">
                    <span>Ditampilkan: <strong x-text="visibleCount"></strong> dari
                        <strong>{{ $participantStats['total'] }}</strong> peserta</span>
                </div>
                <div class="text-gray-400">
                    Terakhir diupdate: {{ now()->format('d M Y H:i') }}
                </div>
            </div>
        </div>

        {{-- MODAL DETAIL PESERTA --}}
        <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50"
            @keydown.escape.window="showDetailModal = false">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDetailModal = false"></div>
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-auto overflow-hidden flex flex-col max-h-[92vh]"
                    @click.stop>

                    {{-- Header --}}
                    <div
                        class="border-b border-gray-100 px-6 py-4 flex-shrink-0 bg-gradient-to-r from-blue-50 to-transparent">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-bold text-lg"
                                    x-text="selectedParticipant ? getInitials(selectedParticipant.nama) : '?'"></div>
                                <div>
                                    <h3 class="font-semibold text-gray-800" x-text="selectedParticipant?.nama"></h3>
                                    <p class="text-xs text-gray-500">
                                        <span x-text="selectedParticipant?.nik"></span> ·
                                        <span x-text="selectedParticipant?.jabatan || '-'"></span> ·
                                        <span x-text="selectedParticipant?.dept || '-'"></span>
                                    </p>
                                </div>
                            </div>
                            <button @click="showDetailModal = false"
                                class="p-1 rounded-lg hover:bg-gray-100 transition">
                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Statistik Ringkas --}}
                    <div class="grid grid-cols-4 gap-2 px-6 py-4 border-b border-gray-100">
                        <div class="text-center p-2 bg-blue-50 rounded-xl">
                            <div class="text-[10px] text-blue-600 uppercase font-semibold">Kehadiran</div>
                            <div class="text-lg font-bold text-blue-700"
                                x-text="`${selectedParticipant?.hadir_count ?? 0}/${selectedParticipant?.total_activities ?? 0}`">
                            </div>
                            <div class="text-[10px] text-blue-500"
                                x-text="`${selectedParticipant?.attendance_percentage ?? 0}%`"></div>
                        </div>
                        <div class="text-center p-2 bg-purple-50 rounded-xl">
                            <div class="text-[10px] text-purple-600 uppercase font-semibold">Pre Test</div>
                            <div class="text-lg font-bold text-purple-700"
                                x-text="selectedParticipant?.pre_test_avg ?? '-'"></div>
                            <div class="text-[10px] text-purple-500">avg</div>
                        </div>
                        <div class="text-center p-2 bg-green-50 rounded-xl">
                            <div class="text-[10px] text-green-600 uppercase font-semibold">Post Test</div>
                            <div class="text-lg font-bold text-green-700"
                                x-text="selectedParticipant?.post_test_avg ?? '-'"></div>
                            <div class="text-[10px] text-green-500">avg</div>
                        </div>
                        <div class="text-center p-2 bg-amber-50 rounded-xl">
                            <div class="text-[10px] text-amber-600 uppercase font-semibold">Status</div>
                            <div class="text-sm font-bold text-amber-700"
                                x-text="selectedParticipant?.status === 'completed' ? 'Selesai' : (selectedParticipant?.status === 'active' ? 'Aktif' : 'Pending')">
                            </div>
                            <div class="text-[10px] text-amber-500"
                                x-text="`${selectedParticipant?.notes?.length || 0} catatan`"></div>
                        </div>
                    </div>

                    {{-- Breakdown --}}
                    <div class="flex-1 overflow-y-auto p-6">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Detail per Kegiatan</h4>
                        <div class="space-y-2">
                            <template x-for="(act, idx) in selectedParticipant?.activity_breakdown || []"
                                :key="idx">
                                <div
                                    class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-600 text-xs font-semibold flex-shrink-0"
                                        x-text="String(idx + 1).padStart(2, '0')"></div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-800 truncate" x-text="act.title">
                                        </div>
                                        <div class="text-[10px] text-gray-500" x-text="act.tanggal"></div>
                                    </div>
                                    <div class="text-center flex-shrink-0">
                                        <template x-if="act.is_present">
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-[10px] font-semibold text-green-700">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Hadir
                                            </span>
                                        </template>
                                        <template x-if="!act.is_present">
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-semibold text-red-700">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Tidak
                                            </span>
                                        </template>
                                    </div>
                                    <div class="w-16 text-center flex-shrink-0">
                                        <div class="text-[10px] text-gray-400">Pre</div>
                                        <div class="text-sm font-bold" :class="getScoreColor(act.pre_test)"
                                            x-text="act.pre_test ?? '-'"></div>
                                    </div>
                                    <div class="w-16 text-center flex-shrink-0">
                                        <div class="text-[10px] text-gray-400">Post</div>
                                        <div class="text-sm font-bold" :class="getScoreColor(act.post_test)"
                                            x-text="act.post_test ?? '-'"></div>
                                    </div>
                                    <div class="w-40 text-xs text-gray-500 truncate" x-text="act.note || '-'"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine.js Component --}}
    <script>
        function participantTable() {
            return {
                search: '',
                expandedRows: [],
                showDetailModal: false,
                selectedParticipant: null,
                participants: @json($participantDetails),
                visibleCount: {{ $participantDetails->count() }},

                init() {
                    // Watch search untuk filter baris Blade
                    this.$watch('search', (value) => {
                        const q = (value || '').toLowerCase().trim();
                        const rows = document.querySelectorAll('.participant-row');
                        let visible = 0;

                        rows.forEach(row => {
                            const dataSearch = row.getAttribute('data-search') || '';
                            const match = !q || dataSearch.includes(q);
                            row.style.display = match ? '' : 'none';

                            // Sembunyikan expanded row juga kalau row utama hidden
                            const expandedRow = row.nextElementSibling;
                            if (expandedRow) {
                                if (!match) expandedRow.style.display = 'none';
                                else expandedRow.style.display = '';
                            }

                            if (match) visible++;
                        });

                        this.visibleCount = visible;
                    });
                },

                toggleExpand(nik) {
                    const idx = this.expandedRows.indexOf(nik);
                    if (idx > -1) this.expandedRows.splice(idx, 1);
                    else this.expandedRows.push(nik);
                },

                openDetailByNik(nik) {
                    this.selectedParticipant = this.participants.find(p => p.nik === nik) || null;
                    this.showDetailModal = true;
                },

                getInitials(name) {
                    if (!name) return '?';
                    const words = name.trim().split(' ');
                    if (words.length === 1) return words[0].charAt(0).toUpperCase();
                    return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
                },

                getScoreColor(score) {
                    if (score === null || score === undefined || score === '') return 'text-gray-400';
                    const n = parseFloat(score);
                    if (n >= 80) return 'text-green-600';
                    if (n >= 60) return 'text-yellow-600';
                    return 'text-red-600';
                }
            }
        }
    </script>
@endif
