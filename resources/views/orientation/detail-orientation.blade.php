<x-app-layout>
    <script src="https://cdn.tailwindcss.com"></script>

    <div class="min-h-screen bg-[#F4F5F7] py-6">
        <div class="max-w-[1440px] mx-auto px-6">

            {{-- ========================================================= --}}
            {{-- HERO - COMPACT --}}
            {{-- ========================================================= --}}
            <section
                class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#7A1113] via-[#9B1F14] to-[#D71920] shadow-lg">
                <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/10"></div>
                <div class="absolute right-16 bottom-0 h-24 w-24 rounded-full bg-white/10"></div>

                <div class="relative z-10 flex items-center justify-between px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
                    <div class="flex items-center gap-3 sm:gap-4">
                        {{-- Tombol Back --}}
                        <a href="{{ route('orientation.index') }}"
                            class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-xl bg-white/10 backdrop-blur hover:bg-white/20 transition flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6 text-white"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        {{-- Icon Program --}}
                        <div
                            class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-xl bg-white/10 backdrop-blur flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-7 sm:h-7 text-white"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>

                        {{-- Title --}}
                        <div>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                                <h1 class="text-lg sm:text-xl lg:text-2xl font-bold text-white">
                                    {{ $orientation->batch_name ?? 'Kobin Orientation Program' }}</h1>
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100/90 text-green-700',
                                        'completed' => 'bg-blue-100/90 text-blue-700',
                                        'cancelled' => 'bg-red-100/90 text-red-700',
                                        'pending' => 'bg-yellow-100/90 text-yellow-700',
                                    ];
                                    $statusLabels = [
                                        'active' => 'Aktif',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                        'pending' => 'Pending',
                                    ];
                                @endphp
                                <span
                                    class="rounded-full {{ $statusColors[$orientation->status] ?? 'bg-gray-100/90 text-gray-700' }} px-2 py-0.5 text-[10px] sm:text-xs font-semibold">
                                    {{ $statusLabels[$orientation->status] ?? ucfirst($orientation->status) }}
                                </span>
                            </div>
                            <div
                                class="mt-0.5 sm:mt-1 flex flex-wrap items-center gap-2 sm:gap-3 text-xs sm:text-sm text-red-100">
                                @php
                                    // Ambil tanggal dari kegiatan pertama dan terakhir
                                    $firstActivity = $kegiatanRows[0] ?? null;
                                    $lastActivity = $kegiatanRows[count($kegiatanRows) - 1] ?? null;
                                    $startDate = $firstActivity ? $firstActivity['tanggal'] : null;
                                    $endDate = $lastActivity ? $lastActivity['tanggal'] : null;
                                @endphp
                                @if ($startDate && $endDate)
                                    <span>{{ date('d M Y', strtotime($startDate)) }} -
                                        {{ date('d M Y', strtotime($endDate)) }}</span>
                                    <span class="hidden xs:inline">•</span>
                                @endif
                                <span>{{ $orientation->plant->name_plant ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ========================================================= --}}
            {{-- TAB MENU - COMPACT --}}
            {{-- ========================================================= --}}
            <div class="mt-4 border-b border-gray-200">
                <nav class="flex gap-6">
                    <button class="border-b-2 border-red-600 px-1 py-3 text-sm font-semibold text-red-600">Jadwal &
                        Materi</button>
                </nav>
            </div>

            {{-- ========================================================= --}}
            {{-- 1 ROW dengan 2 KOLOM (KIRI & KANAN) --}}
            {{-- ========================================================= --}}
            <div class="mt-4 grid grid-cols-12 gap-6 items-start">

                {{-- ========================================================= --}}
                {{-- KOLOM KIRI (col-span-8) - Rincian Program + Jadwal & Materi --}}
                {{-- ========================================================= --}}
                <div class="col-span-12 xl:col-span-9 space-y-4">

                    {{-- RINCIAN PROGRAM --}}
                    @include('orientation.component-detail.rincian-program')

                    {{-- JADWAL & MATERI --}}
                    @include('orientation.component-detail.jadwal-materi')

                    {{-- PESERTA --}}
                    @include('orientation.component-detail.participant')

                    {{-- INFORMASI & CATATAN --}}
                    @if ($canManage)
                        @include('orientation.component-detail.informasi-catatan')
                    @endif
                </div>

                {{-- ========================================================= --}}
                {{-- KOLOM KANAN (col-span-4) - Progress + Timeline + Activity + Button --}}
                {{-- ========================================================= --}}
                <div class="col-span-12 xl:col-span-3 space-y-4">

                    {{-- Progress --}}
                    @include('orientation.component-detail.progress')

                    {{-- Timeline --}}
                    @include('orientation.component-detail.timeline')

                    @if ($canManage)
                        {{-- AKSI CEPAT - VERSION 2 --}}
                        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-purple-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-purple-600"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 text-sm">Aksi Cepat</h3>
                                    <p class="text-[10px] text-gray-500">Kelola program dengan cepat</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 space-y-2.5">
                            {{-- Export Jadwal PDF --}}
                            <a href="{{ route('orientation.export.schedule', $orientation->id) }}" target="_blank"
                                class="flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 hover:border-blue-200 hover:bg-blue-50/30 transition">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm font-medium text-gray-800">Export Jadwal</div>
                                        <div class="text-[10px] text-gray-500">PDF • Unduh jadwal lengkap</div>
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            {{-- Export Peserta PDF --}}
                            <a href="{{ route('orientation.export.participants', $orientation->id) }}" target="_blank"
                                class="flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 hover:border-green-200 hover:bg-green-50/30 transition">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm font-medium text-gray-800">Export Peserta</div>
                                        <div class="text-[10px] text-gray-500">PDF • Unduh daftar peserta</div>
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            {{-- Batalkan Program --}}
                            @if ($orientation->status !== 'completed' && $orientation->status !== 'cancelled')
                                <form action="{{ route('orientation.cancel', $orientation->id) }}" method="POST"
                                    onsubmit="return confirmCancelProgram('{{ $orientation->batch_name }}')">
                                    @csrf
                                    <button type="submit"
                                        class="flex w-full items-center justify-between rounded-xl border border-red-200 bg-red-50/30 px-4 py-3 hover:border-red-300 hover:bg-red-50 transition">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </div>
                                            <div class="text-left">
                                                <div class="text-sm font-medium text-red-700">Batalkan Program</div>
                                                <div class="text-[10px] text-red-500">Konfirmasi • Batalkan seluruh
                                                    program</div>
                                            </div>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                        {{-- Finish Button --}}
                        @if ($orientation->status !== 'completed' && $orientation->status !== 'cancelled')
                        <form action="{{ route('orientation.complete', $orientation->id) }}" method="POST"
                            onsubmit="return confirmCompleteProgram('{{ $orientation->batch_name }}')">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-green-600 px-4 py-3.5 text-sm font-semibold text-white shadow-lg hover:bg-green-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Tandai Program Selesai
                            </button>
                        </form>
                        @elseif($orientation->status === 'completed')
                        <div class="rounded-2xl bg-green-50 border border-green-200 px-4 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm font-semibold text-green-700">Program telah selesai</span>
                            </div>
                        </div>
                        @elseif($orientation->status === 'cancelled')
                        <div class="rounded-2xl bg-red-50 border border-red-200 px-4 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span class="text-sm font-semibold text-red-700">Program telah dibatalkan</span>
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Konfirmasi Batalkan Program
        function confirmCancelProgram(programName) {
            Swal.fire({
                title: 'Batalkan Program?',
                html: `Apakah Anda yakin ingin membatalkan program <strong>"${programName}"</strong>?<br><br>
                   <span class="text-red-600 text-sm">⚠️ Semua kegiatan akan dibatalkan dan tidak dapat dikembalikan!</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    return true;
                }
                return false;
            });
            return false;
        }

        // Konfirmasi Tandai Program Selesai
        function confirmCompleteProgram(programName) {
            Swal.fire({
                title: 'Tandai Program Selesai?',
                html: `Apakah Anda yakin ingin menandai program <strong>"${programName}"</strong> sebagai selesai?<br><br>
                   <span class="text-blue-600 text-sm">✅ Semua kegiatan akan ditandai selesai.</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22C55E',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    return true;
                }
                return false;
            });
            return false;
        }

        // Untuk form submission dengan SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
            // Intercept form submission untuk cancel
            const cancelForm = document.querySelector('form[action*="cancel"]');
            if (cancelForm) {
                cancelForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const programName = '{{ $orientation->batch_name }}';

                    Swal.fire({
                        title: 'Batalkan Program?',
                        html: `Apakah Anda yakin ingin membatalkan program <strong>"${programName}"</strong>?<br><br>
                           <span class="text-red-600 text-sm">⚠️ Semua kegiatan akan dibatalkan dan tidak dapat dikembalikan!</span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#EF4444',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Ya, Batalkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }

            // Intercept form submission untuk complete
            const completeForm = document.querySelector('form[action*="complete"]');
            if (completeForm) {
                completeForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const programName = '{{ $orientation->batch_name }}';

                    Swal.fire({
                        title: 'Tandai Program Selesai?',
                        html: `Apakah Anda yakin ingin menandai program <strong>"${programName}"</strong> sebagai selesai?<br><br>
                           <span class="text-blue-600 text-sm">✅ Semua kegiatan akan ditandai selesai.</span>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#22C55E',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Ya, Selesai!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>
