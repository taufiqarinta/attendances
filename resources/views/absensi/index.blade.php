<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Data Absensi Karyawan') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('absensi.create') }}"
                    class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Absensi
                </a>
                <button onclick="refreshData()"
                    class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm border border-gray-200 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 refresh-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>

    <div class="py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Alert Messages -->
            <div id="alertSuccess" class="hidden mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span id="successMessage"></span>
            </div>
            <div id="alertError" class="hidden mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span id="errorMessage"></span>
            </div>

            <!-- Filter Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Filter Data</p>
                </div>
                <div class="p-5">
                    <form id="filterForm">
                        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">

                            <!-- Tanggal Mulai -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Tanggal Mulai</label>
                                <input type="date" id="startDate" name="start_date"
                                    class="w-full rounded-xl border-gray-200 shadow-sm text-sm h-10">
                            </div>

                            <!-- Tanggal Akhir -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Tanggal Akhir</label>
                                <input type="date" id="endDate" name="end_date"
                                    class="w-full rounded-xl border-gray-200 shadow-sm text-sm h-10">
                            </div>

                            <!-- Search -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Cari Karyawan</label>
                                <div class="relative">
                                    <svg class="w-4 h-4 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                    </svg>
                                    <input type="text" id="searchInput" name="search"
                                        placeholder="NIK atau nama karyawan..."
                                        class="w-full pl-9 rounded-xl border-gray-200 shadow-sm text-sm h-10">
                                </div>
                            </div>

                            <!-- Tombol Filter -->
                            <div class="flex items-end">
                                <button type="submit"
                                    class="w-full h-10 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl text-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                                    </svg>
                                    Filter
                                </button>
                            </div>

                            <!-- Tombol Reset -->
                            <div class="flex items-end">
                                <button type="button" onclick="resetFilters()"
                                    class="w-full h-10 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reset
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                <!-- Table Toolbar -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center px-5 py-4 border-b border-gray-50 gap-3">
                    <div class="flex items-center gap-2">
                        <!-- Loading spinner -->
                        <div id="tableSpinner" class="hidden">
                            <svg class="animate-spin w-4 h-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-500" id="paginationInfo">Memuat data...</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-gray-400 font-medium">Tampilkan</label>
                        <select id="limitSelect"
                            class="w-20 px-2 py-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="text-xs text-gray-400 font-medium">per halaman</label>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto p-2">
                    <table class="w-full min-w-[900px] border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider w-32">NIK</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider w-52">Nama Karyawan</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider w-48">Tanggal & Jam</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider w-28">In / Out</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider w-28">Foto</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider w-36">Lokasi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="6" class="text-center py-16 text-gray-400 text-sm">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        Memuat data...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="flex flex-col sm:flex-row justify-between items-center px-5 py-4 border-t border-gray-50 gap-3" id="paginationContainer">
                    <div class="text-sm text-gray-400" id="paginationDetail"></div>
                    <nav class="flex items-center gap-1" id="paginationLinks"></nav>
                </div>
            </div>

        </div>
    </div>

    <script>
    // ===== STATE =====
    let allData = [];          // Semua data dari server (sekali load)
    let filteredData = [];     // Data setelah filter client-side search (jika diperlukan)
    let currentPage = 1;
    let currentLimit = 25;
    let isFetching = false;
    let currentController = null;
    let searchDebounce = null;

    // Filter state (dikirim ke server saat fetch)
    let serverSearch = '';
    let serverStartDate = '';
    let serverEndDate = '';

    const API_BASE_URL = 'https://web.kobin.co.id/api/hris/absensi/get_absensi.php';

    // ===== DOM =====
    const tableBody = document.getElementById('tableBody');
    const searchInput = document.getElementById('searchInput');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const limitSelect = document.getElementById('limitSelect');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationDetail = document.getElementById('paginationDetail');
    const paginationLinks = document.getElementById('paginationLinks');
    const tableSpinner = document.getElementById('tableSpinner');

    // ===== INIT =====
    function setDefaultDates() {
        const today = new Date();
        const y = today.getFullYear();
        const m = String(today.getMonth() + 1).padStart(2, '0');
        const lastDay = new Date(y, today.getMonth() + 1, 0).getDate();
        serverStartDate = `${y}-${m}-01`;
        serverEndDate = `${y}-${m}-${String(lastDay).padStart(2, '0')}`;
        startDateInput.value = serverStartDate;
        endDateInput.value = serverEndDate;
    }

    function formatDisplayDate(str) {
        if (!str) return '-';
        const d = new Date(str);
        return d.toLocaleString('id-ID', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }

    // ===== FETCH — ambil SEMUA data sekaligus (tidak ada pagination server) =====
    async function fetchAttendanceData() {
        if (currentController) currentController.abort();
        currentController = new AbortController();

        try {
            isFetching = true;
            tableSpinner.classList.remove('hidden');
            paginationInfo.textContent = 'Memuat data...';

            const params = new URLSearchParams({
                page: 1,
                limit: 9999,           // ambil semua data sekaligus
                user_nik: '{{ session('nik') }}'
            });
            if (serverSearch)    params.append('search', serverSearch);
            if (serverStartDate) params.append('start_date', serverStartDate);
            if (serverEndDate)   params.append('end_date', serverEndDate);

            const response = await fetch(`${API_BASE_URL}?${params}`, {
                signal: currentController.signal
            });
            const result = await response.json();

            if (result.success) {
                allData = result.data || [];
                filteredData = allData;
                currentPage = 1;
                renderPage();

                if (allData.length > 0) {
                    verifyBatchData(allData);
                }
            } else {
                throw new Error(result.message || 'Gagal mengambil data');
            }
        } catch (err) {
            if (err.name === 'AbortError') return;
            showAlert('error', 'Gagal memuat data: ' + err.message);
            allData = [];
            filteredData = [];
            renderPage();
        } finally {
            isFetching = false;
            tableSpinner.classList.add('hidden');
        }
    }

    // ===== CLIENT-SIDE PAGINATION =====
    function renderPage() {
        const total = filteredData.length;
        const totalPages = total === 0 ? 1 : Math.ceil(total / currentLimit);
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * currentLimit;
        const end = Math.min(start + currentLimit, total);
        const pageData = filteredData.slice(start, end);

        renderTable(pageData);
        updatePaginationInfo(total, start + 1, end);
        renderPaginationLinks(totalPages);
    }

    function updatePaginationInfo(total, start, end) {
        if (total === 0) {
            paginationInfo.textContent = 'Tidak ada data';
            paginationDetail.textContent = '';
            return;
        }
        paginationInfo.textContent = `Menampilkan ${start}–${end} dari ${total} data`;
        const totalPages = Math.ceil(total / currentLimit);
        paginationDetail.textContent = `Halaman ${currentPage} dari ${totalPages}`;
    }

    function renderPaginationLinks(totalPages) {
        if (totalPages <= 1) { paginationLinks.innerHTML = ''; return; }

        const btnClass = (active) => `inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium transition-all duration-150 ${
            active
            ? 'bg-indigo-600 text-white shadow-sm'
            : 'text-gray-500 hover:bg-gray-100 border border-gray-200'
        }`;

        let html = '';

        // Prev
        html += `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}
            class="${btnClass(false)} ${currentPage === 1 ? 'opacity-30 cursor-not-allowed' : ''}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>`;

        // Pages
        const maxVis = 5;
        let startP = Math.max(1, currentPage - Math.floor(maxVis / 2));
        let endP = Math.min(totalPages, startP + maxVis - 1);
        if (endP - startP + 1 < maxVis) startP = Math.max(1, endP - maxVis + 1);

        if (startP > 1) {
            html += `<button onclick="changePage(1)" class="${btnClass(false)}">1</button>`;
            if (startP > 2) html += `<span class="text-gray-300 text-sm px-1">···</span>`;
        }
        for (let i = startP; i <= endP; i++) {
            html += `<button onclick="changePage(${i})" class="${btnClass(i === currentPage)}">${i}</button>`;
        }
        if (endP < totalPages) {
            if (endP < totalPages - 1) html += `<span class="text-gray-300 text-sm px-1">···</span>`;
            html += `<button onclick="changePage(${totalPages})" class="${btnClass(false)}">${totalPages}</button>`;
        }

        // Next
        html += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}
            class="${btnClass(false)} ${currentPage === totalPages ? 'opacity-30 cursor-not-allowed' : ''}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>`;

        paginationLinks.innerHTML = html;
    }

    function changePage(page) {
        const totalPages = Math.ceil(filteredData.length / currentLimit);
        if (page < 1 || page > totalPages || page === currentPage) return;
        currentPage = page;
        document.querySelector('.overflow-x-auto')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        renderPage();
    }

    // ===== RENDER TABLE =====
    function renderTable(data) {
        if (!data || data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-16 text-gray-400 text-sm">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Tidak ada data ditemukan
                        </div>
                    </td>
                </tr>`;
            return;
        }

        let html = '';
        data.forEach((item) => {
            const checkType = (item.CheckType || '').toLowerCase().trim();
            const isIn = checkType === 'in';

            const badgeClass = isIn
                ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
                : 'bg-red-100 text-red-700 border border-red-200';

            let fotoHtml = '<span class="text-gray-300 text-xs">—</span>';
            if (item.file_foto) {
                const fotoPath = item.file_foto.replace('public/', '');
                const fotoUrl = `/storage/${fotoPath}`;
                fotoHtml = `
                    <a href="${fotoUrl}" target="_blank" 
                        class="group relative w-14 h-14 mx-auto block overflow-hidden rounded-xl border border-gray-200 hover:border-indigo-300 transition-all duration-200 hover:shadow-md cursor-pointer">
                        <img src="${fotoUrl}" alt="Foto ${item.PersonnelNo}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                            onerror="this.onerror=null; this.closest('a').outerHTML='<span class=\'text-red-400 text-xs\'>Tidak ada</span>'">
                        <div class="absolute inset-0 bg-indigo-600/0 group-hover:bg-indigo-600/10 transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </div>
                    </a>`;
            }

            let lokasiHtml = '<span class="text-gray-300 text-xs">—</span>';
            if (item.Latitude && item.Longitude && item.Latitude !== '0' && item.Longitude !== '0') {
                const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${item.Latitude},${item.Longitude}`;
                lokasiHtml = `
                    <a href="${mapsUrl}" target="_blank"
                        class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800 text-sm font-medium hover:underline transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Lihat
                    </a>`;
            }

            html += `
                <tr class="border-b border-gray-50 hover:bg-indigo-50/30 transition-colors duration-100">
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">${item.PersonnelNo || '-'}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <p class="text-sm font-medium text-gray-800">${item.nama_karyawan || '-'}</p>
                    </td>
                    <td class="px-5 py-3.5">
                        <p class="text-sm text-gray-600">${formatDisplayDate(item.CurrentDateTime)}</p>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold ${badgeClass}">
                            <span class="w-1.5 h-1.5 rounded-full ${isIn ? 'bg-emerald-500' : 'bg-red-500'}"></span>
                            ${item.CheckType || '-'}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-center">${fotoHtml}</td>
                    <td class="px-5 py-3.5">${lokasiHtml}</td>
                </tr>`;
        });

        tableBody.innerHTML = html;
    }

    // ===== VERIFY =====
    async function verifyBatchData(data) {
        if (!data || data.length === 0) return;
        try {
            await fetch(API_BASE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ data: data.map(i => ({
                    PersonnelNo: i.PersonnelNo,
                    CurrentDateTime: i.CurrentDateTime,
                    CheckType: i.CheckType
                })) })
            });
        } catch (e) { /* silent */ }
    }

    // ===== ALERT =====
    function showAlert(type, msg) {
        const el = document.getElementById(type === 'success' ? 'alertSuccess' : 'alertError');
        const span = document.getElementById(type === 'success' ? 'successMessage' : 'errorMessage');
        span.textContent = msg;
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 4000);
    }

    // ===== REFRESH =====
    function refreshData() {
        const icon = document.querySelector('.refresh-icon');
        icon.classList.add('animate-spin');
        fetchAttendanceData().finally(() => {
            setTimeout(() => icon.classList.remove('animate-spin'), 600);
        });
    }

    // ===== RESET =====
    function resetFilters() {
        serverSearch = '';
        searchInput.value = '';
        setDefaultDates();
        currentPage = 1;
        fetchAttendanceData();
    }

    // ===== INIT EVENTS =====
    document.addEventListener('DOMContentLoaded', function () {
        setDefaultDates();
        fetchAttendanceData();

        // Submit filter → fetch ulang dari server
        document.getElementById('filterForm').addEventListener('submit', function (e) {
            e.preventDefault();
            serverSearch = searchInput.value.trim();
            serverStartDate = startDateInput.value;
            serverEndDate = endDateInput.value;
            currentPage = 1;
            fetchAttendanceData();
        });

        // Ganti limit → client-side saja, tidak perlu fetch ulang
        limitSelect.addEventListener('change', function () {
            currentLimit = parseInt(this.value);
            currentPage = 1;
            renderPage();   // hanya re-render dari allData
        });

        // Search dengan debounce — fetch ulang ke server
        searchInput.addEventListener('input', function () {
            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(() => {
                serverSearch = this.value.trim();
                currentPage = 1;
                fetchAttendanceData();
            }, 500);
        });
    });
    </script>

    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin { animation: spin 1s linear infinite; }

        /* Scrollbar halus */
        .overflow-x-auto::-webkit-scrollbar { height: 5px; }
        .overflow-x-auto::-webkit-scrollbar-track { background: #f8fafc; }
        .overflow-x-auto::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }
        .overflow-x-auto::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

        /* Modal backdrop blur */
        #fotoModal:not(.hidden) { display: flex; }

        /* Row hover smooth */
        #tableBody tr { transition: background-color 0.1s ease; }
    </style>
</x-app-layout>