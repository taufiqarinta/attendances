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

    <div class="py-12">
        <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">

            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg p-6 flex flex-col items-center">
                    <svg class="animate-spin h-10 w-10 text-red-700 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-700">Memuat data...</span>
                </div>
            </div>

            <!-- Alert Messages -->
            <div id="alertSuccess" class="mb-4 hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <span id="successMessage"></span>
            </div>
            <div id="alertError" class="mb-4 hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span id="errorMessage"></span>
            </div>

            <!-- Tabel Absensi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">

                    <!-- Filter Section -->
                    <div class="mb-6">
                        <form id="filterForm" class="flex items-end gap-3">
                            <!-- Filter Tanggal Mulai -->
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input type="date" id="startDate" name="start_date"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <!-- Filter Tanggal Akhir -->
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input type="date" id="endDate" name="end_date"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <!-- Tombol Aksi -->
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 whitespace-nowrap">
                                    Filter
                                </button>
                                <button type="button" onclick="resetFilters()"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 whitespace-nowrap">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Container untuk tabel -->
                    <div class="table-container w-full overflow-hidden">
                        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300">
                            <table class="w-full divide-y divide-gray-200" id="tblabsensi">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal & Jam</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">In / Out</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Verified</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
                                    <!-- Data akan diisi oleh JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Foto -->
    <div id="fotoModal" class="fixed inset-0 hidden bg-black bg-opacity-75 z-50 items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <img id="modalFoto" src="" alt="Foto" class="max-w-full max-h-[90vh] object-contain rounded">
            <button onclick="closeFotoModal()" class="absolute top-4 right-4 bg-white rounded-full p-2 hover:bg-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
    // ===== STATE =====
    let allData = [];
    let currentController = null;

    // Filter state (dikirim ke server saat fetch)
    let serverStartDate = '';
    let serverEndDate = '';

    // const API_BASE_URL = 'https://web.kobin.co.id/api/hris/test/absensi/get_self_absensimysql.php';
    const API_BASE_URL = '{{ App\Helpers\ApiHelper::getApiUrl('absensi/get_self_absensimysql.php') }}';

    console.log('📡 API URL:', API_BASE_URL);

    // ===== DOM =====
    const loadingOverlay = document.getElementById('loadingOverlay');
    const tableBody = document.getElementById('tableBody');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const alertSuccess = document.getElementById('alertSuccess');
    const alertError = document.getElementById('alertError');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

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
        let formatted = d.toLocaleString('id-ID', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
        formatted = formatted.replace(/(\d{2})\.(\d{2})\.(\d{2})$/, '$1:$2:$3');
        return formatted;
    }

    // ===== SHOW/HIDE LOADING =====
    function showLoading() {
        loadingOverlay.classList.remove('hidden');
    }
    function hideLoading() {
        loadingOverlay.classList.add('hidden');
    }

    // ===== ALERT =====
    function showAlert(type, msg) {
        if (type === 'success') {
            successMessage.textContent = msg;
            alertSuccess.classList.remove('hidden');
            setTimeout(() => alertSuccess.classList.add('hidden'), 3000);
        } else {
            errorMessage.textContent = msg;
            alertError.classList.remove('hidden');
            setTimeout(() => alertError.classList.add('hidden'), 5000);
        }
        if (typeof toastr !== 'undefined') {
            type === 'success' ? toastr.success(msg) : toastr.error(msg);
        }
    }

    // ===== FETCH — ambil SEMUA data sekaligus =====
    async function fetchAttendanceData() {
        if (currentController) currentController.abort();
        currentController = new AbortController();

        try {
            showLoading();

            const params = new URLSearchParams({
                page: 1,
                limit: 9999,
                user_nik: '{{ session('nik') }}'
            });
            if (serverStartDate) params.append('start_date', serverStartDate);
            if (serverEndDate)   params.append('end_date', serverEndDate);

            const response = await fetch(`${API_BASE_URL}?${params}`, {
                signal: currentController.signal
            });
            const result = await response.json();

            if (result.success) {
                allData = result.data || [];
                renderTable(allData);

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
            renderTable([]);
        } finally {
            hideLoading();
        }
    }

    // ===== RENDER TABLE + INIT DATATABLE =====
    function renderTable(data) {
        if (!data || data.length === 0) {
            // Biarkan kosong — pesan "tidak ada data" ditangani oleh DataTables (language.emptyTable)
            tableBody.innerHTML = '';
        } else {
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
                    <tr>
                        <td class="px-4 py-3 text-center">
                            <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">${item.PersonnelNo || '-'}</span>
                        </td>
                        <td class="px-4 py-3 text-center">${item.nama_karyawan || '-'}</td>
                        <td class="px-4 py-3 text-center" data-order="${item.CurrentDateTime || ''}">${formatDisplayDate(item.CurrentDateTime)}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold ${badgeClass}">
                                <span class="w-1.5 h-1.5 rounded-full ${isIn ? 'bg-emerald-500' : 'bg-red-500'}"></span>
                                ${item.CheckType || '-'}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">${fotoHtml}</td>
                        <td class="px-4 py-3 text-center">${lokasiHtml}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold ${
                                item.verified === 'success'
                                    ? 'bg-green-100 text-green-700 border border-green-200'
                                    : 'bg-yellow-100 text-yellow-700 border border-yellow-200'
                            }">
                                ${item.verified === 'success' ? '✓ Success' : '⏳ Pending'}
                            </span>
                        </td>
                    </tr>`;
            });
            tableBody.innerHTML = html;
        }

        // Init / re-init DataTable
        setTimeout(() => {
            if ($.fn.DataTable.isDataTable('#tblabsensi')) {
                $('#tblabsensi').DataTable().destroy();
            }
            $('#tblabsensi').DataTable({
                order: [[2, 'desc']], // Sort by Tanggal & Jam descending
                pageLength: 25,
                language: {
                    emptyTable: "Tidak ada data ditemukan",
                    zeroRecords: "Tidak ada data yang cocok"
                }
            });
        }, 100);
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
        setDefaultDates();
        fetchAttendanceData();
    }

    // ===== INIT EVENTS =====
    document.addEventListener('DOMContentLoaded', function () {
        setDefaultDates();
        fetchAttendanceData();

        document.getElementById('filterForm').addEventListener('submit', function (e) {
            e.preventDefault();
            serverStartDate = startDateInput.value;
            serverEndDate = endDateInput.value;
            fetchAttendanceData();
        });
    });
    </script>

    <style>
        /* DataTables Custom Styling */
        .dataTables_wrapper {
            padding: 1rem !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .dataTables_length,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate {
            margin: 0.75rem 0.5rem !important;
        }
        .dataTables_length label,
        .dataTables_filter label {
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            font-weight: 500 !important;
            color: #374151 !important;
        }
        .dataTables_length select {
            width: auto !important;
            min-width: 60px !important;
            padding: 0.375rem 1.5rem 0.375rem 0.5rem !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            background-color: white !important;
            cursor: pointer !important;
        }
        .dataTables_filter input {
            width: 200px !important;
            padding: 0.375rem 0.75rem !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            outline: none !important;
        }
        .dataTables_filter input:focus {
            border-color: #950000 !important;
            box-shadow: 0 0 0 1px #950000 !important;
        }
        .dataTables_length select:focus {
            border-color: #950000 !important;
            outline: none !important;
        }
        .dataTables_info {
            color: #6b7280 !important;
            font-size: 0.875rem !important;
        }
        .dataTables_paginate {
            display: flex !important;
            gap: 0.25rem !important;
        }
        .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.75rem !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.6rem !important;
            background: white !important;
            color: #374151 !important;
            cursor: pointer !important;
            font-size: 0.875rem !important;
            transition: all 0.2s !important;
        }
        .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
            background: #fef2f2 !important;
            border-color: #fca5a5 !important;
            color: #950000 !important;
        }
        .dataTables_paginate .paginate_button.current {
            background-color: #950000 !important;
            border-color: transparent !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(250, 0, 0, 0.35) !important;
        }
        .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        /* Table Styling */
        #tblabsensi {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        #tblabsensi thead th {
            background-color: #950000;
            color: #ffffff !important;
            font-size: 0.7rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.06em !important;
            text-transform: uppercase !important;
            padding: 14px 20px !important;
            border-bottom: none !important;
        }
        #tblabsensi thead th:first-child { border-top-left-radius: 12px; }
        #tblabsensi thead th:last-child { border-top-right-radius: 12px; }
        #tblabsensi thead th.sorting:after,
        #tblabsensi thead th.sorting_asc:after,
        #tblabsensi thead th.sorting_desc:after {
            color: #ffffff !important;
        }
        #tblabsensi thead th.sorting:after {
            opacity: 0.25;
        }
        #tblabsensi thead th.sorting_asc:after,
        #tblabsensi thead th.sorting_desc:after {
            opacity: 1;
        }
        #tblabsensi tbody td {
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 0.875rem 1.25rem !important;
        }
        #tblabsensi tbody tr:hover {
            background-color: #fef2f2 !important;
        }
        #tblabsensi tbody tr:last-child td {
            border-bottom: none !important;
        }
        #tblabsensi td span[class*="bg-"] {
            border-radius: 0.5rem !important;
            padding: 0.25rem 0.65rem !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
        }

        /* Scrollbar Styling */
        .scrollbar-thin::-webkit-scrollbar {
            height: 8px !important;
            width: 8px !important;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f1f1 !important;
            border-radius: 4px !important;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #c1c1c1 !important;
            border-radius: 4px !important;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1 !important;
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .dataTables_filter input {
                width: 150px !important;
            }
            #tblabsensi tbody td {
                padding: 0.5rem 0.5rem !important;
                font-size: 0.75rem !important;
            }
        }
        @media (max-width: 768px) {
            .dataTables_length {
                float: left !important;
                width: 100% !important;
                margin-bottom: 0.5rem !important;
            }
            .dataTables_filter {
                float: left !important;
                width: 100% !important;
                margin-bottom: 0.5rem !important;
            }
            .dataTables_filter input {
                width: 100% !important;
            }
        }

        /* Table container */
        .table-container {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: white;
            overflow: hidden;
        }
        .overflow-x-auto {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin { animation: spin 1s linear infinite; }
    </style>
</x-app-layout>