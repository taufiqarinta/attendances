<x-app-layout>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="min-h-screen bg-slate-100 py-6">

        <!-- =========================================================
             MAIN CONTAINER
        ========================================================== -->
        <div class="max-w-[1600px] mx-auto px-6">

            <!-- =====================================================
                 HEADER
            ====================================================== -->
            <div
                class="relative overflow-hidden rounded-xl bg-gradient-to-r from-red-800 via-red-700 to-red-600 px-6 py-5 shadow-sm">

                <!-- Decorative Circle -->
                <div class="pointer-events-none absolute -right-8 -top-14 h-36 w-36 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute right-12 -top-10 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex items-center gap-4">

                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm">

                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l5 5v11a2 2 0 01-2 2z" />

                        </svg>

                    </div>

                    <div>

                        <h1 class="text-2xl font-bold tracking-tight text-white">
                            Slip Gaji Saya
                        </h1>

                        <p class="mt-1 text-sm text-red-100">
                            Lihat slip gaji Anda berdasarkan periode.
                        </p>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 SUMMARY
            ====================================================== -->
            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">

                <!-- Total Slip -->
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50">

                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l5 5v11a2 2 0 01-2 2z" />

                            </svg>

                        </div>

                        <div>

                            <p class="text-xs font-medium text-slate-500">
                                Total Slip Saya
                            </p>

                            <p id="totalSlip" class="mt-0.5 text-xl font-bold text-slate-900">
                                {{ $totalSlip }}
                            </p>

                            <p class="text-xs text-slate-400">
                                Dokumen
                            </p>

                        </div>

                    </div>

                </div>


                <!-- Slip Terakhir -->
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">

                    <div class="flex items-center gap-4">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50">

                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />

                            </svg>

                        </div>

                        <div>

                            <p class="text-xs font-medium text-slate-500">
                                Slip Terakhir
                            </p>

                            <p class="mt-0.5 text-xl font-bold text-slate-900">
                                {{ $latestPeriod }}
                            </p>

                            <p class="text-xs text-slate-400">
                                Periode
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 TABLE CARD
            ====================================================== -->
            <div class="mt-5 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                <!-- Table Header -->
                <div
                    class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h2 class="text-base font-bold text-slate-900">
                            Daftar Slip Gaji
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Daftar slip gaji yang tersedia untuk Anda.
                        </p>

                    </div>


                    <!-- Search -->
                    <div class="relative w-full sm:w-64">

                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />

                        </svg>

                        <input id="searchInput" type="text" placeholder="Cari periode..."
                            class="h-10 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-red-400 focus:ring-2 focus:ring-red-100">

                    </div>

                </div>


                <!-- Table -->
                <div class="overflow-x-auto">

                    <table class="w-full text-left">

                        <thead>

                            <tr class="border-b border-slate-200 bg-slate-50">

                                <th class="w-16 px-5 py-3 text-xs font-semibold text-slate-500">
                                    No
                                </th>

                                <th class="px-5 py-3 text-xs font-semibold text-slate-500">
                                    Periode
                                </th>

                                <th class="px-5 py-3 text-xs font-semibold text-slate-500">
                                    Status
                                </th>

                                <th class="px-5 py-3 text-xs font-semibold text-slate-500">
                                    Tanggal Upload
                                </th>

                                <th class="px-5 py-3 text-right text-xs font-semibold text-slate-500">
                                    Aksi
                                </th>

                            </tr>

                        </thead>

                        <tbody id="salaryTable" class="divide-y divide-slate-100">

                        </tbody>

                    </table>

                </div>


                <!-- Empty State -->
                <div id="emptyState" class="hidden px-6 py-12 text-center">

                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">

                        <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l5 5v11a2 2 0 01-2 2z" />

                        </svg>

                    </div>

                    <p class="mt-3 text-sm font-medium text-slate-700">
                        Slip gaji tidak ditemukan
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Coba gunakan kata pencarian lainnya.
                    </p>

                </div>


                <!-- =================================================
                     TABLE FOOTER
                ================================================== -->
                <div
                    class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                    <p id="tableInfo" class="text-xs text-slate-500">
                        Menampilkan 1 - 10 dari 12 data
                    </p>

                    <div class="flex items-center gap-1">

                        <button id="prevBtn" type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-400 transition hover:bg-slate-50">

                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />

                            </svg>

                        </button>


                        <div id="pagination" class="flex items-center gap-1">
                        </div>


                        <button id="nextBtn" type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-50">

                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />

                            </svg>

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =============================================================
         MODAL VERIFIKASI SLIP GAJI
    ============================================================== -->
    <div id="verificationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4">

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


                <button onclick="closeVerificationModal()"
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
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />

                            </svg>

                        </div>

                        <div class="flex-1">

                            <p class="text-xs text-slate-500">
                                Periode Slip Gaji
                            </p>

                            <p id="verificationPeriod" class="mt-1 text-base font-bold text-slate-900">
                                September 2026
                            </p>


                            <div class="mt-4 grid grid-cols-2 gap-4">

                                <div>

                                    <p class="text-xs text-slate-500">
                                        Tanggal Upload
                                    </p>

                                    <p id="verificationDate" class="mt-1 text-sm font-semibold text-slate-700">
                                        24 Sep 2026 10:30
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


                        <input id="encryptionCode" type="password" autocomplete="off"
                            placeholder="Masukkan kode enkripsi..."
                            class="h-12 w-full rounded-xl border border-slate-200 bg-white pl-11 pr-12 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-red-400 focus:ring-2 focus:ring-red-100">


                        <button type="button" onclick="togglePassword()"
                            class="absolute right-0 top-0 flex h-12 w-12 items-center justify-center text-slate-400 hover:text-slate-700">

                            <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />

                            </svg>

                        </button>

                    </div>

                    <p class="mt-2 text-xs text-slate-400">
                        Kode ini diberikan oleh admin untuk membuka file slip gaji.
                    </p>

                </div>

            </div>


            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">

                <button onclick="closeVerificationModal()" type="button"
                    class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                    Batal

                </button>


                <button onclick="verifySlip()" type="button"
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
    <div id="viewSlipModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/70 p-4">

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

                        <h3 id="viewTitle" class="text-lg font-bold text-slate-900">
                            Slip Gaji - September 2026
                        </h3>

                        <p class="text-xs text-slate-500">
                            Berikut adalah slip gaji Anda untuk periode yang dipilih.
                        </p>

                    </div>

                </div>


                <button onclick="closeViewModal()"
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

                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-100">

                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />

                            </svg>

                        </div>

                        <div>

                            <p class="text-[11px] text-slate-500">
                                Periode
                            </p>

                            <p id="viewPeriod" class="text-sm font-bold text-slate-900">
                                September 2026
                            </p>

                        </div>

                    </div>


                    <div class="flex items-center gap-3">

                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-100">

                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />

                            </svg>

                        </div>

                        <div>

                            <p class="text-[11px] text-slate-500">
                                Tanggal Upload
                            </p>

                            <p id="viewDate" class="text-sm font-bold text-slate-900">
                                24 Sep 2026 10:30
                            </p>

                        </div>

                    </div>


                    <div class="flex items-center gap-3">

                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-green-100">

                            <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>

                        </div>

                        <div>

                            <p class="text-[11px] text-slate-500">
                                Status
                            </p>

                            <span class="text-sm font-semibold text-green-600">
                                Tersedia
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- PDF Preview -->
            <div class="min-h-0 flex-1 px-5 py-4">

                <div class="flex h-[min(58vh,560px)] flex-col overflow-hidden rounded-xl border border-slate-200">

                    <!-- Toolbar -->
                    <div class="flex h-11 shrink-0 items-center justify-between bg-slate-800 px-4 text-white">

                        <div class="flex items-center gap-3">

                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />

                            </svg>

                            <span id="userPdfName" class="max-w-[240px] truncate text-xs">
                                slip-gaji.pdf
                            </span>

                        </div>


                        <button type="button" onclick="downloadUserSlip()"
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

                        <iframe id="userPdfFrame" title="Preview Slip Gaji" class="h-full w-full border-0"></iframe>

                    </div>

                </div>

            </div>


            <!-- Footer -->
            <div class="flex shrink-0 justify-end gap-3 border-t border-slate-200 px-5 py-3">

                <button onclick="closeViewModal()" type="button"
                    class="rounded-lg border border-slate-200 bg-white px-5 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                    Tutup

                </button>


                <button onclick="downloadUserSlip()" type="button"
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


    <!-- =============================================================
         JAVASCRIPT
    ============================================================== -->
    <script>
        /*
                |--------------------------------------------------------------------------
                | DATA SLIP GAJI
                |--------------------------------------------------------------------------
                | Untuk sementara menggunakan data UI.
                | Nantinya bagian ini bisa diganti dengan data dari Laravel.
                */

        const salaryData = @json($salaryData);


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        const perPage = 10;

        let currentPage = 1;

        let filteredData = [...salaryData];

        let selectedSlip = null;

        let userPdfUrl = null;

        let userPdfName = "slip-gaji.pdf";


        /*
        |--------------------------------------------------------------------------
        | RENDER TABLE
        |--------------------------------------------------------------------------
        */

        function renderTable() {

            const table = document.getElementById("salaryTable");

            const emptyState = document.getElementById("emptyState");

            const start = (currentPage - 1) * perPage;

            const end = start + perPage;

            const pageData = filteredData.slice(start, end);


            table.innerHTML = "";


            if (pageData.length === 0) {

                emptyState.classList.remove("hidden");

            } else {

                emptyState.classList.add("hidden");

            }


            pageData.forEach((item, index) => {

                const number = start + index + 1;

                const isAvailable = item.status === "available";

                const badge = isAvailable
                    ? '<span class="inline-flex items-center gap-2 rounded-md bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-600"><span class="h-2 w-2 rounded-full bg-green-500"></span>Tersedia</span>'
                    : '<span class="inline-flex items-center gap-2 rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-500"><span class="h-2 w-2 rounded-full bg-slate-400"></span>Belum Tersedia</span>';

                const eyeSvg = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';

                const actionBtn = isAvailable
                    ? '<button onclick="openVerificationModal(' + item.id + ')" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-red-700">' + eyeSvg + 'Lihat Slip</button>'
                    : '<button type="button" disabled class="inline-flex cursor-not-allowed items-center gap-2 rounded-lg bg-slate-200 px-4 py-2 text-xs font-semibold text-slate-400">' + eyeSvg + 'Lihat Slip</button>';


                const row = document.createElement("tr");

                row.className = "transition hover:bg-slate-50";


                row.innerHTML = `

                    <td class="px-5 py-3 text-xs text-slate-500">
                        ${number}
                    </td>

                    <td class="px-5 py-3">

                        <span class="text-sm font-semibold text-slate-800">
                            ${item.period}
                        </span>

                    </td>

                    <td class="px-5 py-3">
                        ${badge}
                    </td>

                    <td class="px-5 py-3 text-sm text-slate-500">
                        ${item.uploadDate}
                    </td>

                    <td class="px-5 py-3 text-right">
                        ${actionBtn}
                    </td>

                `;


                table.appendChild(row);

            });


            renderPagination();

        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        function renderPagination() {

            const pagination = document.getElementById("pagination");

            const totalPages = Math.ceil(filteredData.length / perPage);

            pagination.innerHTML = "";


            for (let page = 1; page <= totalPages; page++) {

                const button = document.createElement("button");

                button.type = "button";

                button.textContent = page;

                button.className = `
                    flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-xs font-semibold transition
                    ${
                        page === currentPage
                            ? "bg-red-600 text-white"
                            : "border border-transparent text-slate-600 hover:bg-slate-100"
                    }
                `;


                button.addEventListener("click", () => {

                    currentPage = page;

                    renderTable();

                });


                pagination.appendChild(button);

            }


            document.getElementById("prevBtn").disabled = currentPage === 1;

            document.getElementById("nextBtn").disabled = currentPage === totalPages;


            document.getElementById("prevBtn").classList.toggle(
                "opacity-40",
                currentPage === 1
            );


            document.getElementById("nextBtn").classList.toggle(
                "opacity-40",
                currentPage === totalPages
            );


            const total = filteredData.length;

            const start = total === 0 ?
                0 :
                ((currentPage - 1) * perPage) + 1;

            const end = Math.min(
                currentPage * perPage,
                total
            );


            document.getElementById("tableInfo").innerHTML =
                `Menampilkan <strong class="text-slate-700">${start} - ${end}</strong> dari <strong class="text-slate-700">${total}</strong> data`;

        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        document.getElementById("searchInput").addEventListener(
            "input",
            function() {

                const keyword = this.value
                    .toLowerCase()
                    .trim();


                filteredData = salaryData.filter(item =>
                    item.period.toLowerCase().includes(keyword)
                );


                currentPage = 1;

                renderTable();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | PREVIOUS PAGE
        |--------------------------------------------------------------------------
        */

        document.getElementById("prevBtn").addEventListener(
            "click",
            function() {

                if (currentPage > 1) {

                    currentPage--;

                    renderTable();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | NEXT PAGE
        |--------------------------------------------------------------------------
        */

        document.getElementById("nextBtn").addEventListener(
            "click",
            function() {

                const totalPages = Math.ceil(
                    filteredData.length / perPage
                );


                if (currentPage < totalPages) {

                    currentPage++;

                    renderTable();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | OPEN VERIFICATION MODAL
        |--------------------------------------------------------------------------
        */

        function openVerificationModal(id) {

            selectedSlip = salaryData.find(
                item => item.id === id
            );


            if (!selectedSlip) return;


            document.getElementById("verificationPeriod").textContent =
                selectedSlip.period;


            document.getElementById("verificationDate").textContent =
                selectedSlip.uploadDate;


            document.getElementById("encryptionCode").value = "";


            const modal = document.getElementById(
                "verificationModal"
            );


            modal.classList.remove("hidden");

            modal.classList.add("flex");


            setTimeout(() => {

                document.getElementById(
                    "encryptionCode"
                ).focus();

            }, 100);

        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE VERIFICATION MODAL
        |--------------------------------------------------------------------------
        */

        function closeVerificationModal() {

            const modal = document.getElementById(
                "verificationModal"
            );


            modal.classList.add("hidden");

            modal.classList.remove("flex");

            document.getElementById("encryptionCode").value = "";

        }


        /*
        |--------------------------------------------------------------------------
        | TOGGLE PASSWORD
        |--------------------------------------------------------------------------
        */

        function togglePassword() {

            const input =
                document.getElementById("encryptionCode");


            input.type =
                input.type === "password" ?
                "text" :
                "password";

        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY SLIP
        |--------------------------------------------------------------------------
        */

        function verifySlip() {

            const code =
                document.getElementById(
                    "encryptionCode"
                ).value.trim();


            if (!code) {

                Swal.fire({

                    icon: "warning",

                    title: "Kode Enkripsi Diperlukan",

                    text: "Silakan masukkan kode enkripsi untuk membuka slip gaji.",

                    confirmButtonColor: "#dc2626",

                    confirmButtonText: "Mengerti"

                });

                return;

            }


            if (!selectedSlip || !selectedSlip.id) {

                return;

            }


            Swal.fire({

                title: "Membuka slip...",

                allowOutsideClick: false,

                didOpen: () => {

                    Swal.showLoading();

                }

            });


            fetch("{{ route('payslip.slip.preview') }}", {

                method: "POST",

                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                },

                body: JSON.stringify({
                    slip_id: selectedSlip.id,
                    encryption_code: code
                })

            })
            .then(response => response.json())
            .then(data => {

                if (!data.success) {

                    Swal.fire({

                        icon: "error",

                        title: "Gagal Membuka",

                        text: data.message || "Kode enkripsi salah. Silakan coba lagi.",

                        confirmButtonColor: "#dc2626"

                    });

                    return;

                }


                const bytes = Uint8Array.from(atob(data.pdf_base64), c => c.charCodeAt(0));

                if (userPdfUrl) URL.revokeObjectURL(userPdfUrl);

                userPdfUrl = URL.createObjectURL(
                    new Blob([bytes], { type: data.mime_type || "application/pdf" })
                );

                userPdfName = data.file_name || "slip-gaji.pdf";

                Swal.close();

                closeVerificationModal();

                openViewModal();

            })
            .catch(() => {

                Swal.fire({

                    icon: "error",

                    title: "Terjadi Kesalahan",

                    text: "Tidak dapat terhubung ke server.",

                    confirmButtonColor: "#dc2626"

                });

            });

        }


        /*
        |--------------------------------------------------------------------------
        | OPEN VIEW MODAL
        |--------------------------------------------------------------------------
        */

        function openViewModal() {

            if (!selectedSlip) return;


            document.getElementById("viewTitle").textContent =
                `Slip Gaji - ${selectedSlip.period}`;


            document.getElementById("viewPeriod").textContent =
                selectedSlip.period;


            document.getElementById("viewDate").textContent =
                selectedSlip.uploadDate;


            document.getElementById("userPdfName").textContent =
                userPdfName;


            document.getElementById("userPdfFrame").src =
                userPdfUrl;


            const modal =
                document.getElementById("viewSlipModal");


            modal.classList.remove("hidden");

            modal.classList.add("flex");

        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE VIEW MODAL
        |--------------------------------------------------------------------------
        */

        function closeViewModal() {

            const modal =
                document.getElementById("viewSlipModal");


            modal.classList.add("hidden");

            modal.classList.remove("flex");


            document.getElementById("userPdfFrame").src = "";


            if (userPdfUrl) {

                URL.revokeObjectURL(userPdfUrl);

                userPdfUrl = null;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD SLIP
        |--------------------------------------------------------------------------
        */

        function downloadUserSlip() {

            if (!userPdfUrl) return;


            const a = document.createElement("a");

            a.href = userPdfUrl;

            a.download = userPdfName;

            document.body.appendChild(a);

            a.click();

            a.remove();

        }


        /*
        |--------------------------------------------------------------------------
        | CLICK OUTSIDE MODAL
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            "verificationModal"
        ).addEventListener(
            "click",
            function(event) {

                if (event.target === this) {

                    closeVerificationModal();

                }

            }
        );


        document.getElementById(
            "viewSlipModal"
        ).addEventListener(
            "click",
            function(event) {

                if (event.target === this) {

                    closeViewModal();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ESC KEY
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            "keydown",
            function(event) {

                if (event.key !== "Escape") return;


                const verification =
                    document.getElementById(
                        "verificationModal"
                    );


                const viewer =
                    document.getElementById(
                        "viewSlipModal"
                    );


                if (!verification.classList.contains("hidden")) {

                    closeVerificationModal();

                }


                if (!viewer.classList.contains("hidden")) {

                    closeViewModal();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | INITIALIZE
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            "DOMContentLoaded",
            function() {

                renderTable();

            }
        );
    </script>

</x-app-layout>
