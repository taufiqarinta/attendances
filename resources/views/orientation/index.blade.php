<x-app-layout>
    {{-- CDN hanya untuk testing --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="min-h-screen bg-[#F4F5F7] py-6">
        <div class="max-w-[1440px] mx-auto px-6">

            {{-- ===================================================== --}}
            {{-- HERO --}}
            {{-- ===================================================== --}}

            <div
                class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#821313] via-[#A11616] to-[#D61E1E] shadow-lg">

                <div class="absolute right-0 top-0 h-40 w-40 rounded-full bg-white/10"></div>
                <div class="absolute right-24 -bottom-8 h-28 w-28 rounded-full bg-white/10"></div>

                <div class="relative flex items-center gap-4 px-6 py-5">

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15 backdrop-blur">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2
                                   2v12a2 2 0
                                   002 2h10a2 2 0
                                   002-2V7a2 2 0
                                   00-2-2h-2M9
                                   5a2 2 0
                                   012-2h2a2
                                   2 0
                                   012 2M9
                                   5a2 2 0
                                   002 2h2a2
                                   2 0
                                   002-2" />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold text-white">List Orientation</h1>
                        <p class="text-sm text-red-100">Kelola seluruh sesi orientasi karyawan.</p>
                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- SUMMARY --}}
            {{-- ===================================================== --}}

            <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-4">

                {{-- Total Program --}}
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9
                                       8h10M5
                                       21h14a2 2 0
                                       002-2V7a2 2
                                       0 00-2-2H5a2
                                       2 0
                                       00-2
                                       2v12a2 2 0
                                       002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Program</p>
                            <h2 class="text-2xl font-bold text-gray-800 leading-none">
                                {{ $statistics['total_programs'] }}</h2>
                            <span class="text-xs text-gray-400">Program</span>
                        </div>
                    </div>
                </div>

                {{-- Total Peserta --}}
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17
                                       20h5V18a4
                                       4 0
                                       00-5-3.87M9
                                       20H4V18a4
                                       4 0
                                       015-3.87m8-5.13a4
                                       4 0
                                       11-8
                                       0 4 4 0
                                       018
                                       0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Peserta</p>
                            <h2 class="text-2xl font-bold text-gray-800 leading-none">
                                {{ $statistics['total_participants'] }}</h2>
                            <span class="text-xs text-gray-400">Orang</span>
                        </div>
                    </div>
                </div>

                {{-- Program Aktif --}}
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12
                                       8c-1.657
                                       0-3
                                       1.343-3
                                       3s1.343
                                       3 3
                                       3 3-1.343
                                       3-3-1.343-3-3-3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Program Aktif</p>
                            <h2 class="text-2xl font-bold text-gray-800 leading-none">
                                {{ $statistics['active_programs'] }}</h2>
                            <span class="text-xs text-gray-400">Program</span>
                        </div>
                    </div>
                </div>

                {{-- Selesai --}}
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9
                                       12l2
                                       2 4-4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Selesai</p>
                            <h2 class="text-2xl font-bold text-gray-800 leading-none">
                                {{ $statistics['completed_programs'] }}</h2>
                            <span class="text-xs text-gray-400">Program</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- FILTER TOOLBAR --}}
            {{-- ===================================================== --}}

            <div class="mt-4 rounded-xl bg-white p-5 shadow-sm border border-gray-100/80">

                {{-- Search Bar --}}
                <form action="{{ route('orientation.index') }}" method="GET"
                    class="flex flex-col gap-4 lg:flex-row lg:items-center">

                    {{-- Search --}}
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-gray-400 group-focus-within:text-red-500 transition-colors duration-200"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-5.2-5.2m1.7-5.3a7 7 0 11-14 0a7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Cari program orientasi..."
                                class="h-10 w-full rounded-xl border-gray-200 bg-gray-50/50 pl-10 pr-4 text-sm focus:border-red-500 focus:ring-red-500 focus:ring-2 focus:bg-white transition-all duration-200 placeholder:text-gray-400">
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2 lg:gap-3">
                        <button type="submit"
                            class="flex h-10 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-gray-600 to-gray-700 px-5 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:from-gray-700 hover:to-gray-800 hover:shadow-md active:scale-95">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M21 21l-5.2-5.2m1.7-5.3a7 7 0 11-14 0a7 7 0 0114 0z" />
                            </svg>
                            Cari
                        </button>

                        @if ($search)
                            <a href="{{ route('orientation.index') }}"
                                class="flex h-10 items-center justify-center gap-2 rounded-xl bg-gray-200 px-4 text-sm font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-300 hover:shadow-md active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Reset
                            </a>
                        @endif

                        @if ($canManage)
                            <a href="{{ route('orientation.create') }}"
                                class="flex h-10 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-5 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:from-red-700 hover:to-red-800 hover:shadow-md active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Buat Program
                            </a>
                        @endif
                    </div>

                </form>

            </div>


            {{-- ===================================================== --}}
            {{-- TABLE START --}}
            {{-- ===================================================== --}}

            <div class="mt-4 rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">

                {{-- ===================================================== --}}
                {{-- TABLE HEADER WITH COUNTER --}}
                {{-- ===================================================== --}}

                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700">
                            <span class="text-gray-900 font-semibold">{{ $programs->total() }}</span> Program Orientasi
                        </span>
                        <span class="h-4 w-px bg-gray-300"></span>
                        <span class="text-sm text-gray-500">
                            Menampilkan <span
                                class="font-medium text-gray-700">{{ $programs->firstItem() ?? 0 }}</span>
                            - <span class="font-medium text-gray-700">{{ $programs->lastItem() ?? 0 }}</span>
                        </span>
                    </div>
                </div>

                {{-- ===================================================== --}}
                {{-- TABLE --}}
                {{-- ===================================================== --}}

                @if ($programs->isEmpty())
                    <div class="py-12 text-center">
                        <svg class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm text-gray-500">Tidak ada program orientasi ditemukan</p>
                        @if ($canManage)
                            <p class="text-xs text-gray-400 mt-1">Klik tombol "Buat Program" untuk menambahkan</p>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50/80 border-b border-gray-100">
                                <tr class="text-left">
                                    <th
                                        class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        <span class="flex items-center gap-1">
                                            No
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-50"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        </span>
                                    </th>
                                    <th
                                        class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Nama Program</th>
                                    <th
                                        class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Lokasi</th>
                                    <th
                                        class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Kegiatan</th>
                                    <th
                                        class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Peserta</th>
                                    <th
                                        class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Status</th>
                                    <th
                                        class="px-4 py-2.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($programs as $index => $program)
                                    @php
                                        $programId = $program->id;
                                        $batchColor = [
                                            'bg-red-100 text-red-700',
                                            'bg-orange-100 text-orange-700',
                                            'bg-blue-100 text-blue-700',
                                            'bg-purple-100 text-purple-700',
                                            'bg-green-100 text-green-700',
                                        ];
                                        $statusColors = [
                                            'active' => 'bg-green-50 text-green-700 border-green-100',
                                            'completed' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            'draft' => 'bg-gray-50 text-gray-700 border-gray-200',
                                            'cancelled' => 'bg-red-50 text-red-700 border-red-100',
                                        ];
                                        $statusLabels = [
                                            'active' => 'Aktif',
                                            'completed' => 'Selesai',
                                            'draft' => 'Draft',
                                            'cancelled' => 'Dibatalkan',
                                        ];
                                        $statusDots = [
                                            'active' => 'bg-green-500 animate-pulse',
                                            'completed' => 'bg-blue-500',
                                            'draft' => 'bg-gray-400',
                                            'cancelled' => 'bg-red-500',
                                        ];
                                        $batchIndex = ($program->batch ?? 1) % count($batchColor);
                                        $statusKey = $program->status ?? 'draft';
                                        $participants = count($program->participants ?? []);
                                    @endphp
                                    <tr class="hover:bg-red-50/30 transition group cursor-pointer"
                                        onclick="window.location='{{ route('orientation.detail', $programId) }}'">
                                        {{-- No --}}
                                        <td class="px-4 py-3 font-medium text-gray-700 text-sm">
                                            {{ str_pad($programs->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                                        </td>

                                        {{-- Program --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="h-8 w-8 rounded-lg bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center flex-shrink-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-4 w-4 text-red-600" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div
                                                        class="font-medium text-gray-800 text-sm group-hover:text-red-600 transition">
                                                        {{ $program->batch_name ?? 'Kobin Orientation' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        Plant:
                                                        {{ $program->plant->name_plant ?? 'Tidak ada lokasi' }}</div>

                                                </div>
                                            </div>
                                        </td>

                                        {{-- Lokasi --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-3.5 w-3.5 text-gray-400" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span
                                                    class="text-sm text-gray-700">{{ $program->plant->name_plant ?? 'Tidak ada lokasi' }}</span>
                                            </div>
                                        </td>

                                        {{-- Jumlah Kegiatan --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-3.5 w-3.5 text-gray-400" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-700">
                                                        {{ $program->activities->count() }} Kegiatan
                                                    </div>
                                                    <div class="text-xs text-gray-400">Rincian orientation</div>
                                                </div>
                                            </div>
                                        </td>


                                        {{-- Peserta --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-3.5 w-3.5 text-gray-400" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-800">
                                                        {{ $participants }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">Peserta</div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-4 py-3">
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-full {{ $statusColors[$statusKey] ?? 'bg-gray-50 text-gray-700 border-gray-200' }} px-2.5 py-0.5 text-xs font-medium border">
                                                <span
                                                    class="h-1.5 w-1.5 rounded-full {{ $statusDots[$statusKey] ?? 'bg-gray-400' }}"></span>
                                                {{ $statusLabels[$statusKey] ?? ucfirst($statusKey) }}
                                            </span>
                                        </td>

                                        {{-- Action --}}
                                        <td class="px-4 py-3" onclick="event.stopPropagation();">
                                            <div class="flex items-center justify-center gap-1">
                                                @if ($canManage)
                                                    <a href="{{ route('orientation.edit', $program) }}"
                                                        class="p-1.5 rounded-lg hover:bg-blue-50 text-gray-400 hover:text-blue-600 transition group-hover:opacity-100 opacity-70">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('orientation.destroy', $program) }}"
                                                        method="POST" class="inline"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus program ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="p-1.5 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-600 transition group-hover:opacity-100 opacity-70">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if (!$canManage)
                                                    <span class="text-xs text-gray-400">Lihat detail</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- ===================================================== --}}
                {{-- TABLE FOOTER --}}
                {{-- ===================================================== --}}

                <div
                    class="flex flex-col items-center justify-between gap-3 border-t border-gray-100 bg-gray-50/50 px-4 py-3 lg:flex-row">

                    {{-- Info --}}
                    <div class="text-sm text-gray-500 flex items-center gap-3">
                        <span>
                            Menampilkan
                            <span class="font-semibold text-gray-700">{{ $programs->firstItem() ?? 0 }}</span>
                            - <span class="font-semibold text-gray-700">{{ $programs->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-semibold text-gray-700">{{ $programs->total() }}</span>
                            data
                        </span>
                        <span class="h-4 w-px bg-gray-300"></span>
                        <span class="text-xs text-gray-400">
                            Terakhir diperbarui: <span
                                class="font-medium text-gray-500">{{ now()->diffForHumans() }}</span>
                        </span>
                    </div>

                    {{-- Pagination --}}
                    <div class="flex items-center gap-3">

                        {{-- Per Page --}}
                        <form action="{{ route('orientation.index') }}" method="GET"
                            class="flex items-center gap-1.5">
                            <input type="hidden" name="search" value="{{ $search }}">
                            <span class="text-xs text-gray-500">Tampil</span>
                            <select name="per_page" onchange="this.form.submit()"
                                class="rounded-lg border-gray-200 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 focus:ring-1 h-8">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-xs text-gray-500">data</span>
                        </form>

                        {{-- Page --}}
                        <div class="flex items-center gap-1">

                            {{-- Prev --}}
                            @if ($programs->onFirstPage())
                                <button disabled
                                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-50 hover:border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                            @else
                                <a href="{{ $programs->previousPageUrl() }}"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-50 hover:border-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </a>
                            @endif

                            {{-- Pages --}}
                            @php
                                $currentPage = $programs->currentPage();
                                $lastPage = $programs->lastPage();
                                $range = 2;
                            @endphp

                            @for ($i = 1; $i <= $lastPage; $i++)
                                @if ($i == 1 || $i == $lastPage || abs($i - $currentPage) <= $range)
                                    @if ($i == $currentPage)
                                        <span
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-600 text-sm font-semibold text-white shadow-sm">
                                            {{ $i }}
                                        </span>
                                    @else
                                        <a href="{{ $programs->url($i) }}"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition">
                                            {{ $i }}
                                        </a>
                                    @endif
                                @elseif($i == 2 && $currentPage > $range + 2)
                                    <span class="text-sm text-gray-400 px-1">...</span>
                                @elseif($i == $lastPage - 1 && $currentPage < $lastPage - $range - 1)
                                    <span class="text-sm text-gray-400 px-1">...</span>
                                @endif
                            @endfor

                            {{-- Next --}}
                            @if ($programs->hasMorePages())
                                <a href="{{ $programs->nextPageUrl() }}"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-50 hover:border-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @else
                                <button disabled
                                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-50 hover:border-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

        @if (session('deleted_success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil dihapus',
                    text: @json(session('deleted_success')),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc2626',
                });
            </script>
        @endif
</x-app-layout>
