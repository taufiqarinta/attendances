<x-app-layout>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="min-h-screen bg-slate-50 py-6">

        <div class="max-w-[1600px] mx-auto px-6">

            <!-- =========================================================
                 HEADER
            ========================================================== -->
            <div
                class="relative mb-5 overflow-hidden rounded-xl bg-gradient-to-r from-red-800 via-red-700 to-red-600 px-6 py-5 shadow-sm">

                <!-- Decorative -->
                <div class="pointer-events-none absolute -right-8 -top-14 h-36 w-36 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute right-12 -top-10 h-36 w-36 rounded-full bg-white/10"></div>

                <div class="relative flex items-center justify-between gap-4">

                    <div class="flex items-center gap-4">

                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm">

                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" stroke-width="1.8"
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
                                Generate Periode Slip Gaji
                            </h1>

                            <p class="mt-1 text-sm text-red-100">
                                Buat periode slip gaji dan tentukan karyawan yang akan menerima slip.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =========================================================
                 MAIN CONTENT
            ========================================================== -->
            <div class="grid grid-cols-1 gap-5 xl:grid-cols-12">


                <!-- =====================================================
                     LEFT - INFORMASI PERIODE
                ====================================================== -->
                <div class="xl:col-span-4">

                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">

                        <div class="p-6">

                            <!-- Title -->
                            <div class="mb-5">

                                <h2 class="text-lg font-bold text-slate-900">
                                    Informasi Periode
                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    Tentukan periode slip gaji yang akan dibuat.
                                </p>

                            </div>

                            <div class="mb-6 h-px bg-slate-100"></div>


                            <!-- Periode -->
                            <div class="mb-5">

                                <label class="mb-2 block text-sm font-semibold text-slate-800">
                                    Periode Gaji
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="relative">

                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">

                                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor"
                                            stroke-width="1.7" viewBox="0 0 24 24">

                                            <rect x="3" y="4" width="18" height="17" rx="2"></rect>
                                            <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"></path>

                                        </svg>

                                    </div>

                                    <input id="salary_period" type="month" value="2026-09"
                                        class="h-11 w-full rounded-lg border border-slate-200 bg-white pl-11 pr-4 text-sm font-medium text-slate-700 outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">

                                </div>

                            </div>


                            <div class="mb-6 h-px bg-slate-100"></div>


                            <!-- Additional Setting -->
                            <div class="mb-5">

                                <h2 class="text-lg font-bold text-slate-900">
                                    Pengaturan Tambahan
                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    Atur parameter slip gaji yang akan digenerate.
                                </p>

                            </div>


                            <!-- Plant -->
                            <div class="mb-5">

                                <label class="mb-2 block text-sm font-semibold text-slate-800">
                                    Plant
                                </label>

                                <select id="plantFilter"
                                    class="h-11 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100">

                                    <option value="">Semua Plant</option>
                                    <option value="1000">1000 - HO - Manager & Director</option>
                                    <option value="1001">1001 - KOBIN</option>
                                    <option value="1002">1002 - CAKK</option>
                                    <option value="1003">1003 - PK-2</option>
                                    <option value="1004">1004 - MISS</option>

                                </select>

                            </div>


                            <!-- Information -->
                            <div class="rounded-lg border border-blue-100 bg-blue-50 p-4">

                                <div class="flex gap-3">

                                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-600" fill="none"
                                        stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path stroke-linecap="round" d="M12 11v5"></path>
                                        <path stroke-linecap="round" d="M12 8h.01"></path>

                                    </svg>

                                    <p class="text-xs leading-5 text-blue-700">
                                        Jika tidak memilih filter, maka slip gaji akan dibuat
                                        untuk seluruh karyawan yang dipilih.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =====================================================
                     RIGHT - PILIH KARYAWAN
                ====================================================== -->
                <div class="xl:col-span-8">

                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">

                        <div class="p-5">

                            <!-- Header -->
                            <div class="mb-5 flex flex-col justify-between gap-4 lg:flex-row lg:items-center">

                                <div>

                                    <h2 class="text-lg font-bold text-slate-900">
                                        Pilih Karyawan
                                    </h2>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Pilih karyawan yang akan menerima slip gaji untuk periode ini.
                                    </p>

                                </div>


                                <!-- Search -->
                                <div class="relative w-full lg:w-80">

                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">

                                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor"
                                            stroke-width="1.7" viewBox="0 0 24 24">

                                            <circle cx="11" cy="11" r="7"></circle>
                                            <path stroke-linecap="round" d="m20 20-4-4"></path>

                                        </svg>

                                    </div>

                                    <input id="searchEmployee" type="text" placeholder="Cari NIK / Nama karyawan..."
                                        class="h-10 w-full rounded-lg border border-slate-200 pl-10 pr-4 text-sm outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100">

                                </div>

                            </div>


                            <!-- Summary -->
                            <div class="mb-5 rounded-lg border border-red-100 bg-red-50 p-4">

                                <div
                                    class="grid grid-cols-1 divide-y divide-red-200 md:grid-cols-3 md:divide-x md:divide-y-0">

                                    <div class="flex items-center gap-3 pb-3 md:pb-0 md:pr-5">

                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-red-100">

                                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor"
                                                stroke-width="1.7" viewBox="0 0 24 24">

                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2">
                                                </path>

                                                <circle cx="9" cy="7" r="4"></circle>

                                                <path stroke-linecap="round"
                                                    d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75">
                                                </path>

                                            </svg>

                                        </div>

                                        <div>

                                            <p class="text-xs text-slate-500">
                                                Total Karyawan
                                            </p>

                                            <p id="totalEmployee" class="text-xl font-bold text-slate-900">
                                                0
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                karyawan
                                            </p>

                                        </div>

                                    </div>


                                    <div class="flex items-center gap-3 py-3 md:px-5 md:py-0">

                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-red-100">

                                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor"
                                                stroke-width="1.7" viewBox="0 0 24 24">

                                                <circle cx="9" cy="7" r="4"></circle>

                                                <path stroke-linecap="round" d="M3 21v-2a6 6 0 0 1 12 0v2">
                                                </path>

                                                <path stroke-linecap="round" d="M16 11l2 2 4-4">
                                                </path>

                                            </svg>

                                        </div>

                                        <div>

                                            <p class="text-xs text-slate-500">
                                                Karyawan Dipilih
                                            </p>

                                            <p id="selectedEmployee" class="text-xl font-bold text-slate-900">
                                                0
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                karyawan
                                            </p>

                                        </div>

                                    </div>


                                    <div class="flex items-center gap-3 pt-3 md:pl-5 md:pt-0">

                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-emerald-100">

                                            <svg class="h-6 w-6 text-emerald-600" fill="none"
                                                stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">

                                                <circle cx="12" cy="12" r="9"></circle>

                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m8 12 2.5 2.5L16 9">
                                                </path>

                                            </svg>

                                        </div>

                                        <div>

                                            <p class="text-sm font-semibold text-emerald-700">
                                                Siap Digenerate
                                            </p>

                                            <p class="mt-0.5 text-xs text-emerald-600">
                                                Pilih karyawan untuk membuat slip gaji.
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- Select All -->
                            <div
                                class="mb-0 flex items-center justify-between rounded-t-lg border border-slate-200 bg-slate-50 px-4 py-3">

                                <label class="flex cursor-pointer items-center gap-3">

                                    <input id="selectAll" type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">

                                    <span class="text-sm font-semibold text-slate-700">
                                        Pilih Semua
                                        (<span id="visibleCount">0</span> karyawan)
                                    </span>

                                </label>


                                <div class="hidden items-center gap-2 text-sm text-slate-500 md:flex">

                                    <span>Tampilkan</span>

                                    <select id="perPage"
                                        class="h-8 rounded-md border border-slate-200 bg-white px-2 text-sm outline-none">

                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>

                                    </select>

                                    <span>data per halaman</span>

                                </div>

                            </div>


                            <!-- Table -->
                            <div class="overflow-x-auto rounded-b-lg border-x border-b border-slate-200">

                                <table class="min-w-full text-left">

                                    <thead class="bg-slate-50">

                                        <tr class="border-b border-slate-200">

                                            <th
                                                class="w-12 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                No
                                            </th>

                                            <th class="w-12 px-3 py-3">
                                                <span class="sr-only">Select</span>
                                            </th>

                                            <th
                                                class="px-3 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                NIK
                                            </th>

                                            <th
                                                class="px-3 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Nama Karyawan
                                            </th>

                                            <th
                                                class="px-3 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Departemen
                                            </th>

                                            <th
                                                class="px-3 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Jabatan
                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody id="employeeTable" class="divide-y divide-slate-100 bg-white">
                                    </tbody>

                                </table>

                            </div>


                            <!-- Empty -->
                            <div id="emptyState"
                                class="hidden rounded-b-lg border-x border-b border-slate-200 bg-white px-6 py-10 text-center">

                                <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">

                                    <circle cx="11" cy="11" r="7"></circle>
                                    <path stroke-linecap="round" d="m20 20-4-4"></path>

                                </svg>

                                <p class="mt-3 text-sm font-semibold text-slate-700">
                                    Data karyawan tidak ditemukan
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Coba ubah pencarian atau filter.
                                </p>

                            </div>


                            <!-- Footer Table -->
                            <div class="mt-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">

                                <p id="paginationInfo" class="text-sm text-slate-500">
                                    Menampilkan 0 data
                                </p>

                                <div id="pagination" class="flex items-center gap-1">
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =========================================================
            ACTION FOOTER (STICKY FULL WIDTH)
            ========================================================== -->
            <div class="sticky bottom-0 z-40 mt-5 border-t border-slate-200 bg-white/95 py-4 shadow-[0_-4px_12px_rgba(0,0,0,0.04)] backdrop-blur-sm"
                style="margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); width: 100vw;">

                <div
                    class="mx-auto flex max-w-[1600px] flex-col-reverse justify-between gap-3 px-6 sm:flex-row sm:items-center">

                    <button type="button" onclick="cancelGenerate()"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">

                            <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18">
                            </path>

                        </svg>

                        Batal

                    </button>


                    <button type="button" onclick="generateSalarySlip()"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 hover:shadow-md active:scale-[0.98]">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                            </path>

                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6">
                            </path>

                            <path stroke-linecap="round" d="M8 13h8M8 17h5">
                            </path>

                        </svg>

                        Generate Periode Slip Gaji

                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-5-5 5 5-5 5">
                            </path>

                        </svg>

                    </button>

                </div>

            </div>

        </div>

    </div>


    <!-- =============================================================
         JAVASCRIPT
    ============================================================== -->
    <script>
        let employees = [];

        let selectedEmployees = new Set();

        let currentPage = 1;

        let perPage = 10;

        let isLoading = false;


        /* =========================================================
           ELEMENTS
        ========================================================== */

        const table = document.getElementById('employeeTable');

        const emptyState = document.getElementById('emptyState');

        const searchInput = document.getElementById('searchEmployee');

        const selectAll = document.getElementById('selectAll');

        const perPageSelect = document.getElementById('perPage');

        const plantFilter = document.getElementById('plantFilter');


        /* =========================================================
           FETCH EMPLOYEES FROM API
        ========================================================== */

        async function fetchEmployees() {

            isLoading = true;

            renderLoadingState();

            try {

                const response = await fetch('https://web.kobin.co.id/api/attendance/live/api_get_users.php');

                const result = await response.json();

                if (result.success && Array.isArray(result.data)) {

                    employees = result.data.map(user => ({
                        nik: user.nik,
                        nama: user.nama,
                        email: user.email,
                        plant: user.plant,
                        divisi: user.divisi,
                        dept: user.dept,
                        jabatan: user.jabatan,
                    }));

                } else {

                    employees = [];

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: result.message || 'Terjadi kesalahan saat mengambil data karyawan.',
                        confirmButtonColor: '#dc2626'
                    });

                }

            } catch (error) {

                employees = [];

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Tidak dapat terhubung ke server. Silakan coba lagi.',
                    confirmButtonColor: '#dc2626'
                });

            }

            isLoading = false;

            currentPage = 1;

            renderTable();

        }


        /* =========================================================
           LOADING STATE
        ========================================================== */

        function renderLoadingState() {

            table.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="h-8 w-8 animate-spin text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <p class="text-sm text-slate-500">Memuat data karyawan...</p>
                        </div>
                    </td>
                </tr>
            `;

            emptyState.classList.add('hidden');

        }


        /* =========================================================
           FILTER DATA
        ========================================================== */

        function getFilteredEmployees() {

            const search = searchInput.value.toLowerCase().trim();

            const selectedPlant = plantFilter.value;

            return employees.filter(employee => {

                const matchSearch = !search ||
                    employee.nik.toLowerCase().includes(search) ||
                    employee.nama.toLowerCase().includes(search);

                const matchPlant = !selectedPlant ||
                    employee.plant === selectedPlant;

                return matchSearch && matchPlant;

            });

        }


        /* =========================================================
           RENDER TABLE
        ========================================================== */

        function renderTable() {

            if (isLoading) return;

            const filtered = getFilteredEmployees();

            const total = filtered.length;

            const totalPages = Math.ceil(total / perPage) || 1;

            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            const start = (currentPage - 1) * perPage;

            const end = Math.min(start + perPage, total);

            const pageData = filtered.slice(start, end);


            document.getElementById('totalEmployee').textContent =
                total;

            document.getElementById('selectedEmployee').textContent =
                selectedEmployees.size;

            document.getElementById('visibleCount').textContent =
                total;


            table.innerHTML = '';


            if (!pageData.length) {

                emptyState.classList.remove('hidden');

            } else {

                emptyState.classList.add('hidden');


                pageData.forEach((employee, index) => {

                    const checked =
                        selectedEmployees.has(employee.nik);


                    const row = document.createElement('tr');

                    row.className =
                        'transition hover:bg-slate-50';


                    row.innerHTML = `

                        <td class="px-4 py-3 text-sm text-slate-500">
                            ${start + index + 1}
                        </td>

                        <td class="px-3 py-3">

                            <input
                                type="checkbox"
                                ${checked ? 'checked' : ''}
                                onchange="toggleEmployee('${employee.nik}', this.checked)"
                                class="employee-checkbox h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">

                        </td>

                        <td class="whitespace-nowrap px-3 py-3 text-sm font-medium text-slate-700">
                            ${employee.nik}
                        </td>

                        <td class="whitespace-nowrap px-3 py-3 text-sm font-semibold text-slate-800">
                            ${employee.nama}
                        </td>

                        <td class="whitespace-nowrap px-3 py-3 text-sm text-slate-600">
                            ${employee.dept}
                        </td>

                        <td class="whitespace-nowrap px-3 py-3 text-sm text-slate-600">
                            ${employee.jabatan}
                        </td>

                    `;

                    table.appendChild(row);

                });

            }


            document.getElementById('paginationInfo').innerHTML =
                total > 0 ?
                `Menampilkan <strong class="text-slate-700">${start + 1} - ${end}</strong> dari <strong class="text-slate-700">${total}</strong> data` :
                'Menampilkan 0 data';


            renderPagination(totalPages);

            updateSelectAllState();

        }


        /* =========================================================
           PAGINATION
        ========================================================== */

        function renderPagination(totalPages) {

            const container =
                document.getElementById('pagination');

            container.innerHTML = '';

            if (totalPages <= 1) return;

            function createPageButton(page) {
                const btn = document.createElement('button');
                btn.textContent = page;
                btn.className =
                    `flex h-9 min-w-9 items-center justify-center rounded-lg border px-3 text-sm font-medium transition ${
                        page === currentPage
                            ? 'border-red-600 bg-red-600 text-white'
                            : 'border-slate-200 text-slate-600 hover:bg-slate-50'
                    }`;
                btn.onclick = () => {
                    currentPage = page;
                    renderTable();
                };
                return btn;
            }

            function createDots() {
                const dots = document.createElement('span');
                dots.className =
                    'flex h-9 w-9 items-center justify-center text-sm text-slate-400';
                dots.textContent = '...';
                return dots;
            }

            const prev = document.createElement('button');
            prev.innerHTML = `
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
                </svg>
            `;
            prev.disabled = currentPage === 1;
            prev.className =
                'flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40';
            prev.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            };
            container.appendChild(prev);

            const pages = [];

            pages.push(1);

            let rangeStart = Math.max(2, currentPage - 1);
            let rangeEnd = Math.min(totalPages - 1, currentPage + 1);

            if (currentPage <= 3) {
                rangeStart = 2;
                rangeEnd = Math.min(4, totalPages - 1);
            }

            if (currentPage >= totalPages - 2) {
                rangeStart = Math.max(2, totalPages - 3);
                rangeEnd = totalPages - 1;
            }

            for (let i = rangeStart; i <= rangeEnd; i++) {
                pages.push(i);
            }

            if (totalPages > 1) {
                pages.push(totalPages);
            }

            for (let i = 0; i < pages.length; i++) {
                if (i > 0 && pages[i] - pages[i - 1] > 1) {
                    container.appendChild(createDots());
                }
                container.appendChild(createPageButton(pages[i]));
            }

            const next = document.createElement('button');
            next.innerHTML = `
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                </svg>
            `;
            next.disabled = currentPage === totalPages;
            next.className =
                'flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40';
            next.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable();
                }
            };
            container.appendChild(next);

        }


        /* =========================================================
           SELECT EMPLOYEE
        ========================================================== */

        function toggleEmployee(nik, checked) {

            if (checked) {

                selectedEmployees.add(nik);

            } else {

                selectedEmployees.delete(nik);

            }

            updateSummary();

            updateSelectAllState();

        }


        /* =========================================================
           SELECT ALL
        ========================================================== */

        selectAll.addEventListener('change', function() {

            const filtered = getFilteredEmployees();

            filtered.forEach(employee => {

                if (this.checked) {

                    selectedEmployees.add(employee.nik);

                } else {

                    selectedEmployees.delete(employee.nik);

                }

            });

            renderTable();

        });


        function updateSelectAllState() {

            const filtered = getFilteredEmployees();

            if (!filtered.length) {

                selectAll.checked = false;

                selectAll.indeterminate = false;

                return;

            }

            const selectedCount =
                filtered.filter(employee =>
                    selectedEmployees.has(employee.nik)
                ).length;


            selectAll.checked =
                selectedCount === filtered.length;

            selectAll.indeterminate =
                selectedCount > 0 &&
                selectedCount < filtered.length;

        }


        /* =========================================================
           SUMMARY
        ========================================================== */

        function updateSummary() {

            document.getElementById('selectedEmployee').textContent =
                selectedEmployees.size;

        }


        /* =========================================================
           SEARCH & FILTER
        ========================================================== */

        searchInput.addEventListener('input', () => {

            currentPage = 1;

            renderTable();

        });


        plantFilter.addEventListener('change', () => {

            currentPage = 1;

            renderTable();

        });


        perPageSelect.addEventListener('change', function() {

            perPage = parseInt(this.value);

            currentPage = 1;

            renderTable();

        });


        /* =========================================================
           GENERATE SLIP
        ========================================================== */

        function generateSalarySlip() {

            const period =
                document.getElementById('salary_period').value;


            if (!period) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Periode Belum Dipilih',
                    text: 'Silakan pilih periode gaji terlebih dahulu.',
                    confirmButtonColor: '#dc2626'
                });

                return;

            }


            if (selectedEmployees.size === 0) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Karyawan Belum Dipilih',
                    text: 'Pilih minimal satu karyawan untuk membuat slip gaji.',
                    confirmButtonColor: '#dc2626'
                });

                return;

            }


            const selectedData = employees.filter(emp =>
                selectedEmployees.has(emp.nik)
            );

            const periodText =
                new Date(period + '-01')
                .toLocaleDateString('id-ID', {
                    month: 'long',
                    year: 'numeric'
                });


            Swal.fire({

                title: 'Generate Slip Gaji?',

                html: `
                    <div class="text-left">

                        <div class="mb-3 rounded-lg bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">
                                Periode
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                ${periodText}
                            </p>
                        </div>

                        <div class="rounded-lg bg-red-50 p-3">
                            <p class="text-xs text-red-500">
                                Karyawan Dipilih
                            </p>

                            <p class="mt-1 text-sm font-semibold text-red-700">
                                ${selectedEmployees.size} karyawan
                            </p>
                        </div>

                    </div>
                `,

                icon: 'question',

                showCancelButton: true,

                confirmButtonText: 'Ya, Generate',

                cancelButtonText: 'Batal',

                confirmButtonColor: '#dc2626',

                cancelButtonColor: '#64748b',

                reverseButtons: true

            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Menyimpan data...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    try {

                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                        const response = await fetch('{{ route("payslip.generate.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                period: period,
                                employees: selectedData,
                            }),
                        });

                        const data = await response.json();

                        if (data.success) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonColor: '#dc2626'
                            }).then(() => {
                                window.location.href = '{{ route("payslip.index") }}';
                            });

                        } else {

                            let errorHtml = `<p class="text-sm">${data.message}</p>`;

                            if (data.duplicate_names && data.duplicate_names.length > 0) {
                                errorHtml += `<div class="mt-3 max-h-40 overflow-y-auto rounded-lg bg-red-50 p-3 text-left">
                                    <p class="mb-2 text-xs font-semibold text-red-600">Karyawan duplikat:</p>
                                    <ul class="list-disc pl-4 text-xs text-red-700">`;
                                data.duplicate_names.forEach(name => {
                                    errorHtml += `<li>${name}</li>`;
                                });
                                errorHtml += `</ul></div>`;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Generate',
                                html: errorHtml,
                                confirmButtonColor: '#dc2626'
                            });

                        }

                    } catch (error) {

                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Tidak dapat terhubung ke server. Silakan coba lagi.',
                            confirmButtonColor: '#dc2626'
                        });

                    }

                }

            });

        }


        /* =========================================================
           CANCEL
        ========================================================== */

        function cancelGenerate() {

            Swal.fire({

                title: 'Batalkan Generate?',

                text: 'Data yang sudah dipilih akan di-reset.',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText: 'Ya, Batalkan',

                cancelButtonText: 'Kembali',

                confirmButtonColor: '#dc2626',

                cancelButtonColor: '#64748b',

                reverseButtons: true

            }).then((result) => {

                if (result.isConfirmed) {

                    selectedEmployees.clear();

                    document.getElementById('salary_period').value =
                        '2026-09';

                    plantFilter.value = '';

                    searchInput.value = '';

                    currentPage = 1;

                    renderTable();

                    Swal.fire({

                        icon: 'success',

                        title: 'Dibatalkan',

                        text: 'Form generate slip gaji telah direset.',

                        timer: 1500,

                        showConfirmButton: false

                    });

                }

            });

        }


        /* =========================================================
           INITIAL RENDER
        ========================================================== */

        fetchEmployees();
    </script>

</x-app-layout>
