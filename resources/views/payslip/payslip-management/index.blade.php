<x-app-layout>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
        }

        body {
            background: #f5f6f8;
        }

        .soft-shadow {
            box-shadow:
                0 1px 2px rgba(0, 0, 0, .03),
                0 8px 24px rgba(15, 23, 42, .05);
        }

        .header-shadow {
            box-shadow:
                0 10px 25px rgba(127, 29, 29, .18);
        }

        .table-row {
            transition: all .18s ease;
        }

        .table-row:hover {
            background: #fafafa;
        }

        .custom-select {
            appearance: none;
            background-image: none;
        }
    </style>


    <div class="min-h-screen bg-[#f5f6f8] px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-[1600px] mx-auto px-6">

            <!-- =====================================================
             HEADER
        ====================================================== -->

            <div
                class="relative mb-5 overflow-hidden rounded-xl bg-gradient-to-r from-red-800 via-red-700 to-red-600 px-6 py-5 shadow-sm">

                <!-- Decorative Circle -->
                <div class="pointer-events-none absolute -right-8 -top-14 h-36 w-36 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute right-12 -top-10 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex items-center justify-between gap-4">

                    <!-- Left: Title -->
                    <div class="flex items-center gap-4">

                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm">

                            <svg class="h-9 w-9 text-white" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">

                                <rect x="5" y="3" width="14" height="18" rx="2">
                                </rect>

                                <path stroke-linecap="round" d="M9 3.5V5h6V3.5">
                                </path>

                                <path stroke-linecap="round" d="M9 10h6M9 14h6M9 18h4">
                                </path>

                            </svg>
                        </div>

                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-white">
                                Slip Gaji
                            </h1>

                            <p class="mt-1 text-sm text-red-100">
                                Kelola slip gaji karyawan berdasarkan periode.
                            </p>
                        </div>

                    </div>

                    <!-- Right: Generate Button -->
                    <div class="relative shrink-0">

                        <a type="button" href="{{ route('payslip.generate') }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-white/50">

                            <!-- Icon -->
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4">
                                </path>

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5 21h14a2 2 0 002-2v-4M3 15v4a2 2 0 002 2">
                                </path>

                            </svg>

                            Generate Periode Slip Gaji

                        </a>

                    </div>

                </div>
            </div>


            <!-- =====================================================
             SUMMARY CARDS
        ====================================================== -->
            <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                <!-- Total Slip -->
                <div class="soft-shadow rounded-2xl border border-gray-100 bg-white p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-gray-500">
                                Total Slip Gaji
                            </p>

                            <p class="mt-2 text-[27px] font-bold tracking-tight text-gray-900">
                                {{ $grandTotalSlip }}
                            </p>

                            <p class="mt-0.5 text-xs text-gray-400">
                                Dokumen
                            </p>

                        </div>

                        <div
                            class="flex h-12 w-12 items-center justify-center
                               rounded-2xl bg-red-50 text-red-600">

                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 3h8l4 4v14H6a2 2 0 01-2-2V5a2 2 0 012-2z">
                                </path>

                                <path stroke-linecap="round" d="M14 3v5h5">
                                </path>

                                <path stroke-linecap="round" d="M8 13h6M8 17h5">
                                </path>

                            </svg>

                        </div>

                    </div>
                </div>


                <!-- Total Karyawan -->
                <div class="soft-shadow rounded-2xl border border-gray-100 bg-white p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-gray-500">
                                Total Karyawan
                            </p>

                            <p class="mt-2 text-[27px] font-bold tracking-tight text-gray-900">
                                {{ $totalAll }}
                            </p>

                            <p class="mt-0.5 text-xs text-gray-400">
                                Orang
                            </p>

                            @if ($hasActiveFilter)
                                <p class="mt-1 text-[11px] font-medium text-red-600" title="{{ $filterInfo }}">
                                    Sesuai filter aktif
                                </p>
                            @endif

                        </div>

                        <div
                            class="flex h-12 w-12 items-center justify-center
                               rounded-2xl bg-blue-50 text-blue-600">

                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">

                                <circle cx="9" cy="7" r="4"></circle>

                                <path stroke-linecap="round" d="M3 21v-2a6 6 0 016-6h0a6 6 0 016 6v2">
                                </path>

                                <path stroke-linecap="round" d="M16 4.5a4 4 0 010 7.5">
                                </path>

                                <path stroke-linecap="round" d="M18 13a5 5 0 013 4.6V21">
                                </path>

                            </svg>

                        </div>

                    </div>
                </div>


                <!-- Uploaded -->
                <div class="soft-shadow rounded-2xl border border-gray-100 bg-white p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-gray-500">
                                Sudah Upload
                            </p>

                            <p class="mt-2 text-[27px] font-bold tracking-tight text-gray-900">
                                {{ $totalUploaded }}
                            </p>

                            <p class="mt-0.5 text-xs text-gray-400">
                                Karyawan
                            </p>

                            @if ($hasActiveFilter)
                                <p class="mt-1 text-[11px] font-medium text-red-600" title="{{ $filterInfo }}">
                                    Sesuai filter aktif
                                </p>
                            @endif

                        </div>

                        <div
                            class="flex h-12 w-12 items-center justify-center
                               rounded-2xl bg-emerald-50 text-emerald-600">

                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">

                                <circle cx="12" cy="12" r="9"></circle>

                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 12l2.2 2.2 4.8-5">
                                </path>

                            </svg>

                        </div>

                    </div>
                </div>


                <!-- Belum Upload -->
                <div class="soft-shadow rounded-2xl border border-gray-100 bg-white p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-gray-500">
                                Belum Upload
                            </p>

                            <p class="mt-2 text-[27px] font-bold tracking-tight text-gray-900">
                                {{ $totalNotUploaded }}
                            </p>

                            <p class="mt-0.5 text-xs text-gray-400">
                                Karyawan
                            </p>

                            @if ($hasActiveFilter)
                                <p class="mt-1 text-[11px] font-medium text-red-600" title="{{ $filterInfo }}">
                                    Sesuai filter aktif
                                </p>
                            @endif

                        </div>

                        <div
                            class="flex h-12 w-12 items-center justify-center
                               rounded-2xl bg-orange-50 text-orange-500">

                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">

                                <circle cx="12" cy="12" r="9"></circle>

                                <path stroke-linecap="round" d="M12 7v5l3 2">
                                </path>

                            </svg>

                        </div>

                    </div>
                </div>

            </div>


            <!-- =====================================================
             MAIN CARD
        ====================================================== -->
            <div class="soft-shadow overflow-hidden rounded-2xl border border-gray-100 bg-white">


                <!-- =================================================
                 TABS
            ================================================== -->
                <div class="border-b border-gray-100 px-3 sm:px-5">

                    <div class="flex overflow-x-auto">

                        <button type="button" onclick="switchTab('slip')" id="tabBtnSlip"
                            class="flex items-center gap-2 whitespace-nowrap border-b-[3px] border-red-600 px-4 py-4 text-sm font-semibold text-red-600">

                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 3h8l4 4v14H6a2 2 0 01-2-2V5a2 2 0 012-2z">
                                </path>

                                <path stroke-linecap="round" d="M14 3v5h5">
                                </path>

                            </svg>

                            Daftar Slip Gaji

                        </button>


                        <button type="button" onclick="switchTab('period')" id="tabBtnPeriod"
                            class="flex items-center gap-2 whitespace-nowrap border-b-[3px] border-transparent px-4 py-4 text-sm font-medium text-gray-500 transition hover:text-red-600">

                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">

                                <rect x="3" y="4" width="18" height="17" rx="2">
                                </rect>

                                <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18">
                                </path>

                            </svg>

                            Daftar Periode

                        </button>

                    </div>

                </div>


                <!-- =================================================
                 TAB: DAFTAR SLIP GAJI
            ================================================== -->
                <div id="tabSlip">

                <!-- =================================================
                 FILTER AREA
            ================================================== -->
                <form method="GET" action="{{ route('payslip.index') }}">

                    <div class="border-b border-gray-100 p-5">

                        <div class="flex flex-col gap-3 xl:flex-row">

                            <!-- Search -->
                            <div class="relative flex-1">

                                <svg class="absolute left-3.5 top-1/2 h-5 w-5
                                   -translate-y-1/2 text-gray-400"
                                    fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                                    <circle cx="11" cy="11" r="7"></circle>

                                    <path stroke-linecap="round" d="M20 20l-4-4">
                                    </path>

                                </svg>

                                <input name="search" type="text" value="{{ $filters['search'] ?? '' }}"
                                    placeholder="Cari NIK atau nama karyawan..."
                                    class="h-11 w-full rounded-xl border border-gray-200
                                   bg-gray-50/50 pl-11 pr-4 text-sm
                                   text-gray-700 outline-none transition
                                   placeholder:text-gray-400
                                   focus:border-red-400 focus:bg-white
                                   focus:ring-4 focus:ring-red-50">

                            </div>


                            <!-- Search Button -->
                            <button type="submit"
                                class="flex h-11 items-center justify-center gap-2
                               rounded-xl bg-[#343b45] px-5 text-sm
                               font-semibold text-white transition
                               hover:bg-[#252b33]">

                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">

                                    <circle cx="11" cy="11" r="7"></circle>

                                    <path stroke-linecap="round" d="M20 20l-4-4">
                                    </path>

                                </svg>

                                Cari

                            </button>


                            <!-- Upload -->
                            {{-- <button type="button" onclick="openUploadModal()"
                            class="flex h-11 items-center justify-center gap-2
                               rounded-xl bg-[#d71920] px-5 text-sm
                               font-semibold text-white shadow-sm
                               transition hover:bg-[#b90f15]">

                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" d="M12 5v14M5 12h14">
                                </path>

                            </svg>

                            Upload Slip Gaji

                        </button> --}}

                        </div>


                        <!-- Filters -->
                        <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">

                            <!-- Periode -->
                            <div>

                                <label class="mb-1.5 block text-xs font-semibold text-gray-500">
                                    PERIODE
                                </label>

                                <div class="relative">

                                    <select name="period_id" onchange="this.form.submit()"
                                        class="custom-select h-11 w-full rounded-xl
                                       border border-gray-200 bg-gray-50/50
                                       px-3 pr-10 text-sm text-gray-700
                                       outline-none focus:border-red-400
                                       focus:bg-white focus:ring-4
                                       focus:ring-red-50">

                                        <option value="">Semua Periode</option>
                                        @foreach ($periods as $period)
                                            <option value="{{ $period->id }}"
                                                {{ ($filters['period_id'] ?? '') == $period->id ? 'selected' : '' }}>
                                                {{ $period->period_name }}
                                            </option>
                                        @endforeach

                                    </select>

                                    <svg class="pointer-events-none absolute right-3
                                       top-1/2 h-4 w-4 -translate-y-1/2
                                       text-gray-400"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6">
                                        </path>

                                    </svg>

                                </div>

                            </div>


                            <!-- Status -->
                            <div>

                                <label class="mb-1.5 block text-xs font-semibold text-gray-500">
                                    STATUS
                                </label>

                                <div class="relative">

                                    <select name="status" onchange="this.form.submit()"
                                        class="custom-select h-11 w-full rounded-xl
                                       border border-gray-200 bg-gray-50/50
                                       px-3 pr-10 text-sm text-gray-700
                                       outline-none focus:border-red-400
                                       focus:bg-white focus:ring-4
                                       focus:ring-red-50">

                                        <option value="">Semua Status</option>
                                        <option value="Uploaded"
                                            {{ ($filters['status'] ?? '') == 'Uploaded' ? 'selected' : '' }}>Uploaded
                                        </option>
                                        <option value="Belum Upload"
                                            {{ ($filters['status'] ?? '') == 'Belum Upload' ? 'selected' : '' }}>Belum
                                            Upload</option>

                                    </select>

                                    <svg class="pointer-events-none absolute right-3
                                       top-1/2 h-4 w-4 -translate-y-1/2
                                       text-gray-400"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6">
                                        </path>

                                    </svg>

                                </div>

                            </div>


                            <!-- Plant -->
                            <div>

                                <label class="mb-1.5 block text-xs font-semibold text-gray-500">
                                    PLANT
                                </label>

                                <div class="relative">

                                    <select name="plant" onchange="this.form.submit()"
                                        class="custom-select h-11 w-full rounded-xl
                                       border border-gray-200 bg-gray-50/50
                                       px-3 pr-10 text-sm text-gray-700
                                       outline-none focus:border-red-400
                                       focus:bg-white focus:ring-4
                                       focus:ring-red-50">

                                        <option value="">Semua Plant</option>
                                        <option value="1000"
                                            {{ ($filters['plant'] ?? '') == '1000' ? 'selected' : '' }}>1000 - HO -
                                            Manager & Director</option>
                                        <option value="1001"
                                            {{ ($filters['plant'] ?? '') == '1001' ? 'selected' : '' }}>1001 - KOBIN
                                        </option>
                                        <option value="1002"
                                            {{ ($filters['plant'] ?? '') == '1002' ? 'selected' : '' }}>1002 - CAKK
                                        </option>
                                        <option value="1003"
                                            {{ ($filters['plant'] ?? '') == '1003' ? 'selected' : '' }}>1003 - PK-2
                                        </option>
                                        <option value="1004"
                                            {{ ($filters['plant'] ?? '') == '1004' ? 'selected' : '' }}>1004 - MISS
                                        </option>

                                    </select>

                                    <svg class="pointer-events-none absolute right-3
                                       top-1/2 h-4 w-4 -translate-y-1/2
                                       text-gray-400"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6">
                                        </path>

                                    </svg>

                                </div>

                            </div>


                            <!-- Department -->
                            <div>

                                <label class="mb-1.5 block text-xs font-semibold text-gray-500">
                                    DEPARTEMEN
                                </label>

                                <div class="relative">

                                    <select name="department" onchange="this.form.submit()"
                                        class="custom-select h-11 w-full rounded-xl
                                       border border-gray-200 bg-gray-50/50
                                       px-3 pr-10 text-sm text-gray-700
                                       outline-none focus:border-red-400
                                       focus:bg-white focus:ring-4
                                       focus:ring-red-50">

                                        <option value="">Semua Departemen</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept }}"
                                                {{ ($filters['department'] ?? '') == $dept ? 'selected' : '' }}>
                                                {{ $dept }}
                                            </option>
                                        @endforeach

                                    </select>

                                    <svg class="pointer-events-none absolute right-3
                                       top-1/2 h-4 w-4 -translate-y-1/2
                                       text-gray-400"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6">
                                        </path>

                                    </svg>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                 TABLE HEADER
            ================================================== -->
                    <div class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-3">

                            <h2 class="text-base font-bold text-gray-800">
                                {{ $employees->total() }}
                                Data Slip Gaji
                            </h2>

                            <span class="hidden h-4 w-px bg-gray-200 sm:block"></span>

                            <p class="text-sm text-gray-400">
                                Menampilkan
                                <span class="font-semibold text-gray-700">{{ $employees->firstItem() ?? 0 }} -
                                    {{ $employees->lastItem() ?? 0 }}</span>
                                dari
                                <span class="font-semibold text-gray-700">{{ $employees->total() }}</span>
                                data
                            </p>

                        </div>

                    </div>


                    <!-- =================================================
                 TABLE
            ================================================== -->
                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[1200px]">

                            <thead>

                                <tr class="border-y border-gray-100 bg-gray-50/70">

                                    <th
                                        class="px-5 py-3.5 text-left text-[11px]
                                       font-bold uppercase tracking-wider text-gray-400">
                                        No
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                       font-bold uppercase tracking-wider text-gray-400">
                                        NIK
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                       font-bold uppercase tracking-wider text-gray-400">
                                        Nama Karyawan
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                       font-bold uppercase tracking-wider text-gray-400">
                                        Plant
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                       font-bold uppercase tracking-wider text-gray-400">
                                        Departemen
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                       font-bold uppercase tracking-wider text-gray-400">
                                        Periode
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                       font-bold uppercase tracking-wider text-gray-400">
                                        Status
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                       font-bold uppercase tracking-wider text-gray-400">
                                        Upload Oleh
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                       font-bold uppercase tracking-wider text-gray-400">
                                        Tanggal Upload
                                    </th>

                                    <th
                                        class="px-5 py-3.5 text-center text-[11px]
                                       font-bold uppercase tracking-wider text-gray-400">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse($employees as $index => $emp)
                                    <tr class="table-row">

                                        <td class="px-5 py-3.5 text-sm text-gray-500">
                                            {{ $employees->firstItem() + $index }}
                                        </td>

                                        <td class="px-4 py-3.5 text-sm font-semibold text-gray-700">
                                            {{ $emp->nik }}
                                        </td>

                                        <td class="px-4 py-3.5">

                                            <div class="flex items-center gap-3">

                                                <div
                                                    class="flex h-8 w-8 shrink-0
                                                       items-center justify-center
                                                       rounded-lg bg-gray-100
                                                       text-xs font-bold text-gray-500">

                                                    {{ Str::substr($emp->nama, 0, 1) }}

                                                </div>

                                                <span class="text-sm font-medium text-gray-800">
                                                    {{ $emp->nama }}
                                                </span>

                                            </div>

                                        </td>

                                        <td class="px-4 py-3.5 text-sm text-gray-600">
                                            {{ $emp->plant_name }}
                                        </td>

                                        <td class="px-4 py-3.5 text-sm text-gray-600">
                                            {{ $emp->dept }}
                                        </td>

                                        <td class="px-4 py-3.5 text-sm text-gray-600">
                                            {{ $emp->period->period_name ?? '' }}
                                        </td>

                                        <td class="px-4 py-3.5">

                                            @if ($emp->slip)
                                                <span
                                                    class="inline-flex items-center gap-2
                                                       rounded-full bg-emerald-50
                                                       px-3 py-1.5 text-xs
                                                       font-semibold text-emerald-600">

                                                    <span
                                                        class="h-1.5 w-1.5 rounded-full
                                                           bg-emerald-500">
                                                    </span>

                                                    Uploaded

                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-2
                                                       rounded-full bg-orange-50
                                                       px-3 py-1.5 text-xs
                                                       font-semibold text-orange-600">

                                                    <span
                                                        class="h-1.5 w-1.5 rounded-full
                                                           bg-orange-500">
                                                    </span>

                                                    Belum Upload

                                                </span>
                                            @endif

                                        </td>

                                        <td class="px-4 py-3.5 text-sm text-gray-500">
                                            {{ $emp->slip->uploaded_by ?? '-' }}
                                        </td>

                                        <td class="px-4 py-3.5 text-sm text-gray-500">
                                            {{ $emp->slip && $emp->slip->uploaded_at ? $emp->slip->uploaded_at->format('d M Y H:i') : '-' }}
                                        </td>

                                        <td class="px-5 py-3.5">

                                            <div class="flex justify-center">

                                                @if ($emp->slip)
                                                    <div class="flex items-center gap-1">

                                                        <button type="button"
                                                            onclick="openSlipVerification('{{ $emp->slip->id }}', '{{ $emp->nik }}', '{{ addslashes($emp->nama) }}', '{{ $emp->period->period_name ?? '' }}', '{{ $emp->slip && $emp->slip->uploaded_at ? $emp->slip->uploaded_at->format('d M Y H:i') : '-' }}', '{{ addslashes($emp->slip->file_name ?? 'slip-gaji.pdf') }}')"
                                                            title="Lihat Slip"
                                                            class="flex h-8 w-8
                                                               items-center justify-center
                                                               rounded-lg border
                                                               border-gray-200
                                                               text-red-500
                                                               hover:bg-red-50">

                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                                stroke-width="1.8" viewBox="0 0 24 24">

                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />

                                                            </svg>

                                                        </button>


                                                        <button type="button"
                                                            onclick="toggleRowMenu(event, this, '{{ $emp->slip->id }}', '{{ $emp->id }}', '{{ addslashes($emp->nama) }}')"
                                                            title="More"
                                                            class="flex h-8 w-8
                                                               items-center justify-center
                                                               rounded-lg border
                                                               border-gray-200
                                                               text-gray-500
                                                               hover:bg-gray-50">

                                                            <svg class="h-4 w-4" fill="currentColor"
                                                                viewBox="0 0 24 24">

                                                                <circle cx="5" cy="12" r="1.5" />
                                                                <circle cx="12" cy="12" r="1.5" />
                                                                <circle cx="19" cy="12" r="1.5" />

                                                            </svg>

                                                        </button>

                                                    </div>
                                                @else
                                                    <div class="flex items-center justify-center gap-1">
                                                        <button type="button"
                                                            onclick="uploadForEmployee('{{ $emp->id }}', '{{ $emp->nik }}', '{{ addslashes($emp->nama) }}', '{{ $emp->payroll_period_id }}', '{{ $emp->period->period_name ?? '' }}')"
                                                            class="rounded-lg border
                                                               border-red-200
                                                               bg-white px-4 py-1.5
                                                               text-xs font-semibold
                                                               text-red-600
                                                               transition hover:bg-red-50">

                                                            Upload

                                                        </button>

                                                        <button type="button"
                                                            onclick="deleteEmployee('{{ $emp->id }}', '{{ addslashes($emp->nama) }}', false)"
                                                            title="Hapus"
                                                            class="flex h-8 w-8
                                                               items-center justify-center
                                                               rounded-lg border
                                                               border-gray-200
                                                               text-gray-500
                                                               hover:bg-red-50 hover:text-red-600 hover:border-red-200">

                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                                stroke-width="1.8" viewBox="0 0 24 24">

                                                                <path stroke-linecap="round"
                                                                    d="M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-9 0l1 13a1 1 0 001 1h8a1 1 0 001-1l1-13">
                                                                </path>

                                                            </svg>

                                                        </button>
                                                    </div>
                                                @endif

                                            </div>

                                        </td>

                                    </tr>
                                @empty
                                @endforelse
                            </tbody>

                        </table>


                        <!-- Empty -->
                        @if ($employees->isEmpty())
                            <div class="px-6 py-20 text-center">

                                <div
                                    class="mx-auto flex h-16 w-16 items-center
                               justify-center rounded-2xl bg-gray-100">

                                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor"
                                        stroke-width="1.8" viewBox="0 0 24 24">

                                        <circle cx="11" cy="11" r="7"></circle>

                                        <path stroke-linecap="round" d="M20 20l-4-4">
                                        </path>

                                    </svg>

                                </div>

                                <h3 class="mt-4 text-sm font-semibold text-gray-700">
                                    Data tidak ditemukan
                                </h3>

                                <p class="mt-1 text-sm text-gray-400">
                                    Silakan ubah filter atau kata pencarian.
                                </p>

                            </div>
                        @endif

                    </div>


                    <!-- =================================================
                 PAGINATION
            ================================================== -->
                    <div
                        class="flex flex-col gap-4 border-t border-gray-100
                       px-5 py-4 lg:flex-row lg:items-center
                       lg:justify-between">

                        <p class="text-sm text-gray-400">

                            Menampilkan

                            <span class="font-semibold text-gray-700">
                                {{ $employees->firstItem() ?? 0 }}
                            </span>

                            -

                            <span class="font-semibold text-gray-700">
                                {{ $employees->lastItem() ?? 0 }}
                            </span>

                            dari

                            <span class="font-semibold text-gray-700">
                                {{ $employees->total() }}
                            </span>

                            data

                        </p>


                        <div class="flex items-center gap-3">

                            <div class="flex items-center gap-2 text-sm text-gray-500">

                                Tampil

                                <select name="per_page" onchange="this.form.submit()"
                                    class="h-9 rounded-lg border border-gray-200
                                   bg-white px-2 text-sm outline-none
                                   focus:border-red-400">

                                    <option value="10" {{ ($filters['per_page'] ?? 10) == 10 ? 'selected' : '' }}>
                                        10</option>
                                    <option value="25" {{ ($filters['per_page'] ?? 10) == 25 ? 'selected' : '' }}>
                                        25</option>
                                    <option value="50" {{ ($filters['per_page'] ?? 10) == 50 ? 'selected' : '' }}>
                                        50</option>

                                </select>

                                data

                            </div>


                            @if ($employees->lastPage() > 1)
                                <div class="flex items-center gap-1">

                                    @php
                                        $current = $employees->currentPage();
                                        $last = $employees->lastPage();

                                        $pages = [1];
                                        $rangeStart = max(2, $current - 1);
                                        $rangeEnd = min($last - 1, $current + 1);
                                        if ($current <= 3) {
                                            $rangeStart = 2;
                                            $rangeEnd = min(4, $last - 1);
                                        }
                                        if ($current >= $last - 2) {
                                            $rangeStart = max(2, $last - 3);
                                            $rangeEnd = $last - 1;
                                        }
                                        for ($i = $rangeStart; $i <= $rangeEnd; $i++) {
                                            $pages[] = $i;
                                        }
                                        if ($last > 1) {
                                            $pages[] = $last;
                                        }
                                        $pages = array_unique($pages);
                                        sort($pages);
                                    @endphp

                                    <a href="{{ $current > 1 ? $employees->url($current - 1) : '#' }}"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 {{ $current === 1 ? 'opacity-30 pointer-events-none' : '' }}">
                                        &#8249;
                                    </a>

                                    @foreach ($pages as $i => $page)
                                        @if ($i > 0 && $page - $pages[$i - 1] > 1)
                                            <span
                                                class="flex h-9 w-9 items-center justify-center text-sm text-gray-400">...</span>
                                        @endif

                                        <a href="{{ $employees->url($page) }}"
                                            class="flex h-9 min-w-9 items-center justify-center rounded-lg px-2 text-sm font-medium {{ $page === $current ? 'bg-[#d71920] text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                            {{ $page }}
                                        </a>
                                    @endforeach

                                    <a href="{{ $current < $last ? $employees->url($current + 1) : '#' }}"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 {{ $current === $last ? 'opacity-30 pointer-events-none' : '' }}">
                                        &#8250;
                                    </a>

                                </div>
                            @endif

                        </div>

                    </div>

                </form>

                </div><!-- /tabSlip -->


                <!-- =================================================
                 TAB: DAFTAR PERIODE
            ================================================== -->
                <div id="tabPeriod" class="hidden">

                    <div class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-3">

                            <h2 class="text-base font-bold text-gray-800">
                                {{ $periods->count() }}
                                Periode Slip Gaji
                            </h2>

                        </div>

                    </div>


                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[1000px]">

                            <thead>

                                <tr class="border-y border-gray-100 bg-gray-50/70">

                                    <th
                                        class="px-5 py-3.5 text-left text-[11px]
                                           font-bold uppercase tracking-wider text-gray-400">
                                        No
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                           font-bold uppercase tracking-wider text-gray-400">
                                        Periode
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                           font-bold uppercase tracking-wider text-gray-400">
                                        Total Karyawan
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                           font-bold uppercase tracking-wider text-gray-400">
                                        Sudah Upload
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                           font-bold uppercase tracking-wider text-gray-400">
                                        Status
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                           font-bold uppercase tracking-wider text-gray-400">
                                        Dibuat Oleh
                                    </th>

                                    <th
                                        class="px-4 py-3.5 text-left text-[11px]
                                           font-bold uppercase tracking-wider text-gray-400">
                                        Tanggal Dibuat
                                    </th>

                                    <th
                                        class="px-5 py-3.5 text-center text-[11px]
                                           font-bold uppercase tracking-wider text-gray-400">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse($periods as $index => $period)
                                    <tr class="table-row">

                                        <td class="px-5 py-3.5 text-sm text-gray-500">
                                            {{ $index + 1 }}
                                        </td>

                                        <td class="px-4 py-3.5 text-sm font-semibold text-gray-700">
                                            {{ $period->period_name }}
                                        </td>

                                        <td class="px-4 py-3.5 text-sm text-gray-600">
                                            {{ $period->total_employees }} karyawan
                                        </td>

                                        <td class="px-4 py-3.5 text-sm text-gray-600">
                                            {{ $period->total_uploaded }} karyawan
                                        </td>

                                        <td class="px-4 py-3.5">

                                            @if ($period->status === 'COMPLETED')
                                                <span
                                                    class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                    Completed
                                                </span>
                                            @elseif($period->status === 'ACTIVE')
                                                <span
                                                    class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                    Active
                                                </span>
                                            @elseif($period->status === 'CANCELLED')
                                                <span
                                                    class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                                    Cancelled
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-500">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                                    Draft
                                                </span>
                                            @endif

                                        </td>

                                        <td class="px-4 py-3.5 text-sm text-gray-500">
                                            {{ $period->created_by ?? '-' }}
                                        </td>

                                        <td class="px-4 py-3.5 text-sm text-gray-500">
                                            {{ $period->created_at ? $period->created_at->format('d M Y H:i') : '-' }}
                                        </td>

                                        <td class="px-5 py-3.5">

                                            <div class="flex justify-center">

                                                <button type="button"
                                                    onclick="deletePeriod('{{ $period->id }}', '{{ addslashes($period->period_name) }}', '{{ $period->total_employees }}')"
                                                    title="Hapus Periode"
                                                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">

                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                        stroke-width="1.8" viewBox="0 0 24 24">

                                                        <path stroke-linecap="round"
                                                            d="M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-9 0l1 13a1 1 0 001 1h8a1 1 0 001-1l1-13">
                                                        </path>

                                                    </svg>

                                                </button>

                                            </div>

                                        </td>

                                    </tr>
                                @empty
                                @endforelse
                            </tbody>

                        </table>


                        @if ($periods->isEmpty())
                            <div class="px-6 py-20 text-center">

                                <div
                                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100">

                                    <svg class="h-7 w-7 text-gray-400" fill="none" stroke="currentColor"
                                        stroke-width="1.8" viewBox="0 0 24 24">

                                        <rect x="3" y="4" width="18" height="17" rx="2">
                                        </rect>

                                        <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18">
                                        </path>

                                    </svg>

                                </div>

                                <h3 class="mt-4 text-sm font-semibold text-gray-700">
                                    Belum ada periode
                                </h3>

                                <p class="mt-1 text-sm text-gray-400">
                                    Buat periode slip gaji terlebih dahulu.
                                </p>

                            </div>
                        @endif

                    </div>

                </div><!-- /tabPeriod -->

            </div>



        </div>
    </div>


    <!-- =====================================================
     ROW ACTION DROPDOWN
====================================================== -->
    <div id="rowActionMenu"
        class="fixed z-[70] hidden w-64 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">

        <div class="border-b border-gray-100 px-4 py-2.5">

            <p class="truncate text-xs font-semibold text-gray-700" id="rowActionMenuName">
                -
            </p>

        </div>

        <div class="p-1.5">

            <button type="button" id="rowActionDeleteEmployee"
                class="flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left transition hover:bg-red-50">

                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600">

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                            d="M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-9 0l1 13a1 1 0 001 1h8a1 1 0 001-1l1-13">
                        </path>

                    </svg>

                </span>

                <span>

                    <span class="block text-sm font-semibold text-gray-800">
                        Hapus Karyawan
                    </span>

                    <span class="mt-0.5 block text-xs leading-4 text-gray-400">
                        Data karyawan dihapus, file PDF ikut terhapus otomatis.
                    </span>

                </span>

            </button>


            <button type="button" id="rowActionDeleteSlip"
                class="mt-1 flex w-full items-start gap-3 rounded-lg px-3 py-2.5 text-left transition hover:bg-red-50">

                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600">

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />

                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6" />

                        <path stroke-linecap="round" d="M9 15l6 6M15 15l-6 6" />

                    </svg>

                </span>

                <span>

                    <span class="block text-sm font-semibold text-gray-800">
                        Hapus File PDF
                    </span>

                    <span class="mt-0.5 block text-xs leading-4 text-gray-400">
                        Hanya file yang dihapus, data karyawan tetap ada.
                    </span>

                </span>

            </button>

        </div>

    </div>


    <!-- =====================================================
     UPLOAD MODAL
====================================================== -->
    <div id="uploadModal"
        class="fixed inset-0 z-50 hidden items-center
           justify-center bg-black/50 px-4 backdrop-blur-sm">

        <div class="w-full max-w-2xl overflow-hidden rounded-2xl
               bg-white shadow-2xl">

            <!-- Modal Header -->
            <div class="flex items-start justify-between
                   border-b border-gray-100 px-6 py-5">

                <div class="flex items-start gap-4">

                    <!-- Icon -->
                    <div
                        class="flex h-11 w-11 shrink-0 items-center
                           justify-center rounded-xl
                           bg-red-50 text-red-500">

                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">

                            <path stroke-linecap="round" d="M6 3h8l4 4v14H6a2 2 0 01-2-2V5a2 2 0 012-2z">
                            </path>

                            <path stroke-linecap="round" d="M14 3v5h5">
                            </path>

                            <path stroke-linecap="round" d="M9 13h6M9 17h4">
                            </path>

                        </svg>

                    </div>

                    <div>

                        <h3 class="text-lg font-bold text-gray-900">
                            Upload Slip Gaji
                        </h3>

                        <p class="mt-0.5 text-sm text-gray-400">
                            Upload slip gaji untuk satu karyawan pada periode tertentu.
                        </p>

                    </div>

                </div>

                <button onclick="closeUploadModal()"
                    class="flex h-9 w-9 shrink-0 items-center justify-center
                       rounded-xl text-gray-400 transition
                       hover:bg-gray-100 hover:text-gray-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                        <path d="M6 6l12 12M18 6L6 18"></path>

                    </svg>

                </button>

            </div>


            <!-- Modal Body -->
            <div class="space-y-6 px-6 py-6">

                <!-- Periode -->
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">

                    <label
                        class="w-full text-xs font-bold uppercase
                              tracking-wider text-gray-500 sm:w-40 sm:shrink-0">
                        Periode
                    </label>

                    <div class="relative flex-1">

                        <div
                            class="pointer-events-none absolute
                               left-3.5 top-1/2
                               -translate-y-1/2 text-gray-400">

                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">

                                <rect x="3" y="4" width="18" height="17" rx="2">
                                </rect>

                                <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18">
                                </path>

                            </svg>

                        </div>

                        <input type="hidden" id="uploadPeriodId" value="">

                        <input id="uploadPeriod" type="text" readonly
                            placeholder="Periode"
                            class="h-11 w-full rounded-xl
                               border border-gray-200 bg-gray-50
                               pl-10 pr-4 text-sm font-medium text-gray-700
                               outline-none">

                    </div>

                </div>


                <!-- Karyawan -->
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start">

                    <label
                        class="w-full pt-2.5 text-xs font-bold
                              uppercase tracking-wider text-gray-500
                              sm:w-40 sm:shrink-0">
                        Karyawan
                    </label>

                    <div class="flex-1">

                        <div class="relative">

                            <div
                                class="pointer-events-none absolute
                                   left-3.5 top-1/2
                                   -translate-y-1/2 text-gray-400">

                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"
                                    viewBox="0 0 24 24">

                                    <circle cx="12" cy="8" r="4"></circle>

                                    <path stroke-linecap="round" d="M4 21v-1a6 6 0 016-6h4a6 6 0 016 6v1">
                                    </path>

                                </svg>

                            </div>

                            <input type="hidden" id="uploadEmployeeId" value="">

                            <input id="uploadEmployee" type="text" readonly
                                placeholder="NIK - Nama Karyawan"
                                class="h-11 w-full rounded-xl
                                   border border-gray-200 bg-gray-50
                                   pl-10 pr-4 text-sm font-medium text-gray-700
                                   outline-none">

                        </div>

                        <p class="mt-1.5 text-xs text-gray-400">
                            Karyawan yang akan diberikan slip gaji.
                        </p>

                    </div>

                </div>


                <!-- Kode Enkripsi -->
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start">

                    <label
                        class="w-full pt-2.5 text-xs font-bold
                              uppercase tracking-wider text-gray-500
                              sm:w-40 sm:shrink-0">
                        Kode Enkripsi
                    </label>

                    <div class="flex-1">

                        <div class="relative">

                            <div
                                class="pointer-events-none absolute
                                   left-3.5 top-1/2
                                   -translate-y-1/2 text-gray-400">

                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"
                                    viewBox="0 0 24 24">

                                    <rect x="4" y="10" width="16" height="10" rx="2">
                                    </rect>

                                    <path stroke-linecap="round" d="M8 10V7a4 4 0 118 0v3">
                                    </path>

                                </svg>

                            </div>

                            <input id="uploadEncryptionCode" type="text" placeholder="Masukkan kode enkripsi"
                                class="h-11 w-full rounded-xl
                                   border border-gray-200 bg-white
                                   pl-10 pr-4 text-sm text-gray-700
                                   outline-none transition
                                   placeholder:text-gray-400
                                   focus:border-red-400
                                   focus:ring-4 focus:ring-red-50">

                        </div>

                        <p class="mt-1.5 text-xs text-gray-400">
                            Kode ini akan digunakan karyawan untuk membuka file slip gaji.
                        </p>

                    </div>

                </div>


                <!-- File Slip Gaji -->
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start">

                    <label
                        class="w-full pt-2.5 text-xs font-bold
                              uppercase tracking-wider text-gray-500
                              sm:w-40 sm:shrink-0">
                        File Slip Gaji (PDF)
                    </label>

                    <div class="flex-1">

                        <label for="payslipFile"
                            class="flex cursor-pointer flex-col
                               items-center justify-center rounded-2xl
                               border-2 border-dashed border-gray-200
                               bg-gray-50/50 px-6 py-10 text-center
                               transition hover:border-red-300
                               hover:bg-red-50/30">

                            <div
                                class="flex h-14 w-14 items-center
                                   justify-center rounded-2xl
                                   bg-red-50 text-red-500">

                                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8"
                                    viewBox="0 0 24 24">

                                    <path stroke-linecap="round" d="M6 3h8l4 4v14H6a2 2 0 01-2-2V5a2 2 0 012-2z">
                                    </path>

                                    <path stroke-linecap="round" d="M14 3v5h5">
                                    </path>

                                    <path stroke-linecap="round" d="M12 11v6m0 0l-2.5-2.5M12 17l2.5-2.5">
                                    </path>

                                </svg>

                            </div>

                            <p class="mt-4 text-sm font-semibold text-gray-700">
                                Drag & drop file PDF di sini
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                atau klik untuk memilih file
                            </p>

                            <span
                                class="mt-4 inline-flex items-center gap-2
                                     rounded-xl border border-gray-200
                                     bg-white px-4 py-2 text-sm
                                     font-semibold text-gray-600
                                     transition hover:bg-gray-50">

                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"
                                    viewBox="0 0 24 24">

                                    <path stroke-linecap="round"
                                        d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z">
                                    </path>

                                </svg>

                                Pilih File PDF

                            </span>

                            <div id="selectedFilePanel" class="mt-3 hidden rounded-xl border border-red-100 bg-red-50/60 p-3">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-500">

                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                                            <path stroke-linecap="round" d="M6 3h8l4 4v14H6a2 2 0 01-2-2V5a2 2 0 012-2z">
                                            </path>

                                            <path stroke-linecap="round" d="M14 3v5h5">
                                            </path>

                                        </svg>

                                    </div>

                                    <div class="min-w-0 flex-1 text-left">

                                        <p id="selectedFileName" class="truncate text-xs font-semibold text-gray-700">
                                        </p>

                                        <p id="selectedFileSize" class="mt-0.5 text-xs text-gray-400">
                                        </p>

                                    </div>

                                </div>

                                <div class="mt-3 flex gap-2">

                                    <button type="button" onclick="changeSelectedFile()"
                                        class="flex-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-50">

                                        Ganti

                                    </button>

                                    <button type="button" onclick="clearSelectedFile()"
                                        class="flex-1 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">

                                        Hapus

                                    </button>

                                </div>

                            </div>

                        </label>

                        <input type="file" id="payslipFile" accept=".pdf" class="hidden"
                            onchange="showSelectedFile(this)">

                        <div class="mt-2 flex items-center gap-3 text-xs text-gray-400">

                            <span>Format file: PDF</span>

                            <span class="h-3 w-px bg-gray-300"></span>

                            <span>Maksimal ukuran: 5 MB</span>

                        </div>

                    </div>

                </div>


                <!-- Info Alert -->
                <div
                    class="flex items-start gap-3 rounded-xl
                       border border-red-100 bg-red-50/60 px-4 py-3.5">

                    <div class="mt-0.5 shrink-0 text-red-500">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">

                            <circle cx="12" cy="12" r="9"></circle>

                            <path stroke-linecap="round" d="M12 8v5">
                            </path>

                            <path stroke-linecap="round" d="M12 16h.01">
                            </path>

                        </svg>

                    </div>

                    <p class="text-sm text-red-600">
                        Pastikan file yang diupload adalah slip gaji yang sesuai untuk karyawan dan periode yang
                        dipilih.
                    </p>

                </div>

            </div>


            <!-- Footer -->
            <div class="flex justify-end gap-3 border-t
                   border-gray-100 bg-gray-50/50 px-6 py-4">

                <button onclick="closeUploadModal()"
                    class="rounded-xl border border-gray-200
                       bg-white px-6 py-2.5 text-sm
                       font-semibold text-gray-600
                       transition hover:bg-gray-100">

                    Batal

                </button>

                <button onclick="submitUpload()"
                    class="inline-flex items-center gap-2
                       rounded-xl bg-[#d71920] px-6 py-2.5
                       text-sm font-semibold text-white
                       shadow-sm transition hover:bg-[#b90f15]">

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                        <path stroke-linecap="round" d="M12 16V4m0 0L8 8m4-4l4 4">
                        </path>

                        <path stroke-linecap="round" d="M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3">
                        </path>

                    </svg>

                    Upload Slip Gaji

                </button>

            </div>

        </div>

    </div>


    <!-- =============================================================
         MODAL VERIFIKASI SLIP GAJI
    ============================================================== -->
    <div id="slipVerifyModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4">

        <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">

                <div class="flex items-center gap-3">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50">

                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l5 5v11a2 2 0 01-2 2z" />

                        </svg>

                    </div>

                    <div>

                        <h3 class="text-lg font-bold text-slate-900">
                            Verifikasi Slip Gaji
                        </h3>

                        <p class="text-xs text-slate-500">
                            Masukkan kode enkripsi untuk membuka slip gaji.
                        </p>

                    </div>

                </div>


                <button type="button" onclick="closeSlipVerifyModal()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </button>

            </div>


            <!-- Modal Body -->
            <div class="p-6">

                <!-- Information -->
                <div class="rounded-xl border border-red-100 bg-red-50/60 p-4">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100">

                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />

                            </svg>

                        </div>

                        <div class="min-w-0 flex-1">

                            <p class="text-xs text-slate-500">
                                Karyawan
                            </p>

                            <p id="slipVerifyEmployee" class="mt-1 truncate text-base font-bold text-slate-900">
                                -
                            </p>

                            <p class="mt-3 text-xs text-slate-500">
                                Periode Slip Gaji
                            </p>

                            <p id="slipVerifyPeriod" class="mt-1 text-sm font-semibold text-slate-700">
                                -
                            </p>


                            <div class="mt-4 grid grid-cols-2 gap-4">

                                <div>

                                    <p class="text-xs text-slate-500">
                                        Tanggal Upload
                                    </p>

                                    <p id="slipVerifyDate" class="mt-1 text-sm font-semibold text-slate-700">
                                        -
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs text-slate-500">
                                        Status
                                    </p>

                                    <span
                                        class="mt-1 inline-flex items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">

                                        <span class="h-2 w-2 rounded-full bg-green-500"></span>

                                        Tersedia

                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Encryption Code -->
                <div class="mt-5">

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Kode Enkripsi
                    </label>

                    <div class="relative">

                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">

                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v2h8z" />

                            </svg>

                        </div>


                        <input id="slipVerifyCode" type="password" autocomplete="off"
                            placeholder="Masukkan kode enkripsi..."
                            class="h-12 w-full rounded-xl border border-slate-200 bg-white pl-11 pr-12 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-red-400 focus:ring-2 focus:ring-red-100">


                        <button type="button" onclick="toggleSlipVerifyPassword()"
                            class="absolute right-0 top-0 flex h-12 w-12 items-center justify-center text-slate-400 hover:text-slate-700">

                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />

                            </svg>

                        </button>

                    </div>

                    <p class="mt-2 text-xs text-slate-400">
                        Kode ini diberikan kepada karyawan untuk membuka file slip gaji.
                    </p>

                </div>

            </div>


            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">

                <button onclick="closeSlipVerifyModal()" type="button"
                    class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                    Batal

                </button>


                <button onclick="submitSlipVerification()" type="button"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />

                    </svg>

                    Buka Slip

                </button>

            </div>

        </div>

    </div>


    <!-- =============================================================
         MODAL VIEW SLIP
    ============================================================== -->
    <div id="slipViewModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/70 p-4">

        <div class="flex max-h-[94vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

            <!-- Header -->
            <div class="flex shrink-0 items-center justify-between border-b border-slate-200 px-5 py-4">

                <div class="flex items-center gap-3">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50">

                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l5 5v11a2 2 0 01-2 2z" />

                        </svg>

                    </div>

                    <div>

                        <h3 id="slipViewTitle" class="text-lg font-bold text-slate-900">
                            Slip Gaji
                        </h3>

                        <p class="text-xs text-slate-500">
                            Berikut adalah slip gaji karyawan untuk periode yang dipilih.
                        </p>

                    </div>

                </div>


                <button type="button" onclick="closeSlipViewModal()"
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>

                </button>

            </div>


            <!-- Info -->
            <div class="shrink-0 px-5 pt-4">

                <div class="grid grid-cols-1 gap-3 rounded-xl border border-red-100 bg-red-50/50 p-3 sm:grid-cols-3">

                    <div class="flex items-center gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-100">

                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />

                            </svg>

                        </div>

                        <div class="min-w-0">

                            <p class="text-[11px] text-slate-500">
                                Periode
                            </p>

                            <p id="slipViewPeriod" class="truncate text-sm font-bold text-slate-900">
                                -
                            </p>

                        </div>

                    </div>


                    <div class="flex items-center gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-100">

                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />

                            </svg>

                        </div>

                        <div class="min-w-0">

                            <p class="text-[11px] text-slate-500">
                                Karyawan
                            </p>

                            <p id="slipViewEmployee" class="truncate text-sm font-bold text-slate-900">
                                -
                            </p>

                        </div>

                    </div>


                    <div class="flex items-center gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-100">

                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />

                            </svg>

                        </div>

                        <div class="min-w-0">

                            <p class="text-[11px] text-slate-500">
                                Tanggal Upload
                            </p>

                            <p id="slipViewDate" class="truncate text-sm font-bold text-slate-900">
                                -
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- PDF Preview -->
            <div class="min-h-0 flex-1 px-5 py-4">

                <div class="flex h-[min(58vh,560px)] flex-col overflow-hidden rounded-xl border border-slate-200">

                    <!-- Toolbar -->
                    <div class="flex h-11 shrink-0 items-center justify-between bg-slate-800 px-4 text-white">

                        <div class="flex min-w-0 items-center gap-3">

                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l5 5v11a2 2 0 01-2 2z" />

                            </svg>

                            <span id="slipViewFileName" class="truncate text-xs">
                                slip-gaji.pdf
                            </span>

                        </div>


                        <button type="button" onclick="downloadSlipFile()"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-red-500">

                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v12m0 0l-4-4m4 4l4-4" />

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 20h14" />

                            </svg>

                            Download

                        </button>

                    </div>


                    <!-- PDF -->
                    <div class="min-h-0 flex-1 bg-slate-700">

                        <iframe id="slipPdfFrame" title="Preview Slip Gaji" class="h-full w-full border-0"></iframe>

                    </div>

                </div>

            </div>


            <!-- Footer -->
            <div class="flex shrink-0 justify-end gap-3 border-t border-slate-200 px-5 py-3">

                <button onclick="closeSlipViewModal()" type="button"
                    class="rounded-lg border border-slate-200 bg-white px-5 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                    Tutup

                </button>


                <button onclick="downloadSlipFile()" type="button"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v12m0 0l-4-4m4 4l4-4" />

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 20h14" />

                    </svg>

                    Download Slip

                </button>

            </div>

        </div>

    </div>


    <!-- =====================================================
         JAVASCRIPT
    ====================================================== -->
    <script>
        function openUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        function resetUploadForm() {
            document.getElementById('uploadPeriodId').value = '';
            document.getElementById('uploadPeriod').value = '';
            document.getElementById('uploadEmployeeId').value = '';
            document.getElementById('uploadEmployee').value = '';
            document.getElementById('uploadEncryptionCode').value = '';
            clearSelectedFile();
        }

        function uploadForEmployee(id, nik, nama, periodId, periodName) {
            resetUploadForm();
            document.getElementById('uploadPeriodId').value = periodId;
            document.getElementById('uploadPeriod').value = periodName;
            document.getElementById('uploadEmployeeId').value = id;
            document.getElementById('uploadEmployee').value = nik + ' - ' + nama;
            openUploadModal();
        }

        function showSelectedFile(input) {
            const panel = document.getElementById('selectedFilePanel');
            if (!input.files.length) {
                panel.classList.add('hidden');
                return;
            }
            const file = input.files[0];
            const isPdf = file.type === 'application/pdf' || /\.pdf$/i.test(file.name);
            if (!isPdf) {
                Swal.fire({
                    icon: 'error',
                    title: 'File tidak valid',
                    text: 'Silakan pilih file PDF.',
                    confirmButtonColor: '#d71920'
                });
                input.value = '';
                panel.classList.add('hidden');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File terlalu besar',
                    text: 'Ukuran file maksimal 5 MB.',
                    confirmButtonColor: '#d71920'
                });
                input.value = '';
                panel.classList.add('hidden');
                return;
            }
            document.getElementById('selectedFileName').textContent = file.name;
            document.getElementById('selectedFileSize').textContent = formatFileSize(file.size);
            panel.classList.remove('hidden');
        }

        function changeSelectedFile() {
            document.getElementById('payslipFile').click();
        }

        function clearSelectedFile() {
            document.getElementById('payslipFile').value = '';
            document.getElementById('selectedFilePanel').classList.add('hidden');
            document.getElementById('selectedFileName').textContent = '';
            document.getElementById('selectedFileSize').textContent = '';
        }

        async function submitUpload() {
            const periodId = document.getElementById('uploadPeriodId').value;
            const periodName = document.getElementById('uploadPeriod').value;
            const employeeId = document.getElementById('uploadEmployeeId').value;
            const employeeName = document.getElementById('uploadEmployee').value;
            const encryptionCode = document.getElementById('uploadEncryptionCode').value.trim();
            const fileInput = document.getElementById('payslipFile');
            const file = fileInput.files[0];

            if (!periodId || !employeeId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data belum lengkap',
                    text: 'Periode dan karyawan belum dipilih.',
                    confirmButtonColor: '#d71920'
                });
                return;
            }
            if (!file) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File belum dipilih',
                    text: 'Silakan pilih file PDF slip gaji terlebih dahulu.',
                    confirmButtonColor: '#d71920'
                });
                return;
            }
            if (!encryptionCode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Kode enkripsi kosong',
                    text: 'Silakan masukkan kode enkripsi terlebih dahulu.',
                    confirmButtonColor: '#d71920'
                });
                return;
            }
            if (encryptionCode.length < 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Kode enkripsi terlalu pendek',
                    text: 'Kode enkripsi minimal 6 karakter.',
                    confirmButtonColor: '#d71920'
                });
                return;
            }

            const confirm = await Swal.fire({
                title: 'Upload Slip Gaji?',
                html: '<div class="space-y-3 text-left">' +
                    '<div class="rounded-xl bg-gray-50 p-4"><p class="text-xs text-gray-400">Periode</p><p class="mt-1 font-semibold">' +
                    periodName + '</p></div>' +
                    '<div class="rounded-xl bg-gray-50 p-4"><p class="text-xs text-gray-400">Karyawan</p><p class="mt-1 font-semibold">' +
                    employeeName + '</p></div>' +
                    '<div class="rounded-xl bg-gray-50 p-4"><p class="text-xs text-gray-400">File</p><p class="mt-1 font-semibold">' +
                    file.name + ' (' + formatFileSize(file.size) + ')</p></div>' +
                    '</div>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Upload',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d71920'
            });
            if (!confirm.isConfirmed) return;

            Swal.fire({
                title: 'Mengupload & mengenkripsi...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const formData = new FormData();
                formData.append('period_id', periodId);
                formData.append('employee_id', employeeId);
                formData.append('encryption_code', encryptionCode);
                formData.append('file', file);

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch('{{ route('payslip.upload') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    closeUploadModal();
                    resetUploadForm();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        confirmButtonColor: '#d71920'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Upload',
                        text: data.message || 'Terjadi kesalahan saat mengupload.',
                        confirmButtonColor: '#d71920'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat terhubung ke server.',
                    confirmButtonColor: '#d71920'
                });
            }
        }

        let rowMenuAnchor = null;

        function toggleRowMenu(event, btn, slipId, empId, nama) {
            event.stopPropagation();

            const menu = document.getElementById('rowActionMenu');

            if (rowMenuAnchor === btn && !menu.classList.contains('hidden')) {
                closeRowMenu();
                return;
            }

            document.getElementById('rowActionMenuName').textContent = nama;
            document.getElementById('rowActionDeleteEmployee').onclick = function() {
                closeRowMenu();
                deleteEmployee(empId, nama, true);
            };
            document.getElementById('rowActionDeleteSlip').onclick = function() {
                closeRowMenu();
                deleteSlipFile(slipId, nama);
            };

            menu.classList.remove('hidden');
            rowMenuAnchor = btn;

            const rect = btn.getBoundingClientRect();
            const menuWidth = 256;
            const menuHeight = menu.offsetHeight || 190;

            let left = rect.right - menuWidth;
            if (left < 8) left = 8;

            let top = rect.bottom + 6;
            if (top + menuHeight > window.innerHeight - 8) {
                top = rect.top - menuHeight - 6;
            }
            if (top < 8) top = 8;

            menu.style.left = left + 'px';
            menu.style.top = top + 'px';
        }

        function closeRowMenu() {
            document.getElementById('rowActionMenu').classList.add('hidden');
            rowMenuAnchor = null;
        }

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('rowActionMenu');
            if (!menu.classList.contains('hidden') && !menu.contains(event.target)) {
                closeRowMenu();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') closeRowMenu();
        });

        window.addEventListener('scroll', function() {
            closeRowMenu();
        }, true);

        async function deleteSlipFile(slipId, nama) {
            const confirm = await Swal.fire({
                title: 'Hapus File PDF?',
                text: 'File PDF slip gaji ' + nama + ' akan dihapus. Data karyawan tetap ada.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                reverseButtons: true
            });
            if (!confirm.isConfirmed) return;

            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch('/payslip/slip/' + slipId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            confirmButtonColor: '#d71920'
                        })
                        .then(() => {
                            window.location.reload();
                        });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal menghapus file.',
                        confirmButtonColor: '#d71920'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat terhubung ke server.',
                    confirmButtonColor: '#d71920'
                });
            }
        }

        let verifyingSlipId = null;
        let slipBlobUrl = null;
        let slipDownloadName = 'slip-gaji.pdf';

        function openSlipVerification(slipId, nik, nama, period, uploadDate, fileName) {
            verifyingSlipId = slipId;
            slipDownloadName = fileName || 'slip-gaji.pdf';
            document.getElementById('slipVerifyEmployee').textContent = nik + ' - ' + nama;
            document.getElementById('slipVerifyPeriod').textContent = period;
            document.getElementById('slipVerifyDate').textContent = uploadDate;
            document.getElementById('slipVerifyCode').value = '';
            const modal = document.getElementById('slipVerifyModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                document.getElementById('slipVerifyCode').focus();
            }, 100);
        }

        function closeSlipVerifyModal() {
            const modal = document.getElementById('slipVerifyModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('slipVerifyCode').value = '';
            verifyingSlipId = null;
        }

        function toggleSlipVerifyPassword() {
            const input = document.getElementById('slipVerifyCode');
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        async function submitSlipVerification() {
            const code = document.getElementById('slipVerifyCode').value.trim();
            if (!code) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Kode Enkripsi Diperlukan',
                    text: 'Silakan masukkan kode enkripsi untuk membuka slip gaji.',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Mengerti'
                });
                return;
            }
            if (!verifyingSlipId) return;

            Swal.fire({
                title: 'Membuka slip...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch('{{ route('payslip.slip.preview') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        slip_id: verifyingSlipId,
                        encryption_code: code
                    })
                });
                const data = await response.json();

                if (data.success) {
                    const bytes = Uint8Array.from(atob(data.pdf_base64), c => c.charCodeAt(0));
                    const blob = new Blob([bytes], {
                        type: data.mime_type || 'application/pdf'
                    });
                    if (slipBlobUrl) URL.revokeObjectURL(slipBlobUrl);
                    slipBlobUrl = URL.createObjectURL(blob);
                    slipDownloadName = data.file_name || slipDownloadName;
                    const viewInfo = {
                        employee: document.getElementById('slipVerifyEmployee').textContent,
                        period: document.getElementById('slipVerifyPeriod').textContent,
                        date: document.getElementById('slipVerifyDate').textContent,
                        fileName: data.file_name || slipDownloadName
                    };
                    Swal.close();
                    closeSlipVerifyModal();
                    openSlipViewModal(viewInfo);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Membuka',
                        text: data.message || 'Kode enkripsi salah. Silakan coba lagi.',
                        confirmButtonColor: '#d71920'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat terhubung ke server.',
                    confirmButtonColor: '#d71920'
                });
            }
        }

        function openSlipViewModal(info) {
            document.getElementById('slipViewTitle').textContent = 'Slip Gaji - ' + info.employee;
            document.getElementById('slipViewPeriod').textContent = info.period;
            document.getElementById('slipViewEmployee').textContent = info.employee;
            document.getElementById('slipViewDate').textContent = info.date;
            document.getElementById('slipViewFileName').textContent = info.fileName;
            document.getElementById('slipPdfFrame').src = slipBlobUrl;
            const modal = document.getElementById('slipViewModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeSlipViewModal() {
            const modal = document.getElementById('slipViewModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('slipPdfFrame').src = '';
            if (slipBlobUrl) {
                URL.revokeObjectURL(slipBlobUrl);
                slipBlobUrl = null;
            }
        }

        function downloadSlipFile() {
            if (!slipBlobUrl) return;
            const a = document.createElement('a');
            a.href = slipBlobUrl;
            a.download = slipDownloadName;
            document.body.appendChild(a);
            a.click();
            a.remove();
        }

        function switchTab(tab) {
            const isSlip = tab === 'slip';

            document.getElementById('tabSlip').classList.toggle('hidden', !isSlip);
            document.getElementById('tabPeriod').classList.toggle('hidden', isSlip);

            const btnSlip = document.getElementById('tabBtnSlip');
            const btnPeriod = document.getElementById('tabBtnPeriod');

            btnSlip.className = 'flex items-center gap-2 whitespace-nowrap border-b-[3px] px-4 py-4 text-sm transition ' +
                (isSlip ? 'border-red-600 font-semibold text-red-600' : 'border-transparent font-medium text-gray-500 hover:text-red-600');
            btnPeriod.className = 'flex items-center gap-2 whitespace-nowrap border-b-[3px] px-4 py-4 text-sm transition ' +
                (!isSlip ? 'border-red-600 font-semibold text-red-600' : 'border-transparent font-medium text-gray-500 hover:text-red-600');

            try {
                localStorage.setItem('payslipActiveTab', tab);
            } catch (e) {}
        }

        (function restorePayslipTab() {
            let tab = 'slip';
            try {
                tab = localStorage.getItem('payslipActiveTab') || 'slip';
            } catch (e) {}
            if (tab !== 'slip' && tab !== 'period') tab = 'slip';
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    switchTab(tab);
                });
            } else {
                switchTab(tab);
            }
        })();

        async function deletePeriod(id, name, totalEmployees) {
            const confirm = await Swal.fire({
                title: 'Hapus Periode?',
                html: '<div class="text-left">' +
                    '<div class="mb-3 rounded-lg bg-slate-50 p-3">' +
                    '<p class="text-xs text-slate-500">Periode</p>' +
                    '<p class="mt-1 text-sm font-semibold text-slate-800">' + name + '</p>' +
                    '</div>' +
                    '<div class="rounded-lg border border-red-100 bg-red-50 px-3 py-2.5">' +
                    '<p class="text-xs leading-5 text-red-700">Seluruh <strong>' + totalEmployees + ' karyawan</strong> dalam periode ini ikut <strong>terhapus permanen</strong>, termasuk file PDF slip gajinya.</p>' +
                    '</div>' +
                    '</div>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                reverseButtons: true
            });
            if (!confirm.isConfirmed) return;

            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch('/payslip/period/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            confirmButtonColor: '#d71920'
                        })
                        .then(() => {
                            window.location.reload();
                        });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal menghapus periode.',
                        confirmButtonColor: '#d71920'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat terhubung ke server.',
                    confirmButtonColor: '#d71920'
                });
            }
        }

        async function deleteEmployee(id, nama, hasSlip = true) {
            const confirm = await Swal.fire({
                title: 'Hapus Data?',
                html: '<p class="text-sm text-slate-600">Data <strong>' + nama +
                    '</strong> akan dihapus dari periode ini.</p>' +
                    (hasSlip ?
                        '<div class="mt-3 rounded-lg border border-red-100 bg-red-50 px-3 py-2.5 text-left">' +
                        '<p class="text-xs leading-5 text-red-700">File PDF slip gajinya ikut <strong>terhapus otomatis</strong>.</p>' +
                        '</div>' : ''),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                reverseButtons: true
            });
            if (!confirm.isConfirmed) return;

            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch('/payslip/employee/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            confirmButtonColor: '#d71920'
                        })
                        .then(() => {
                            window.location.reload();
                        });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal menghapus data.',
                        confirmButtonColor: '#d71920'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Tidak dapat terhubung ke server.',
                    confirmButtonColor: '#d71920'
                });
            }
        }

        document.getElementById('uploadModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeUploadModal();
            }
        });

        document.getElementById('slipVerifyModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeSlipVerifyModal();
            }
        });
    </script>

</x-app-layout>
