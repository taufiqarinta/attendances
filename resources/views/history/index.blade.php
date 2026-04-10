<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('History Approval') }}
            </h2>
            <div class="flex space-x-2">
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
                    <svg class="animate-spin h-10 w-10 text-red-500 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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

            <!-- History Section -->
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
                            <div class="flex gap-3">
                                <button type="submit"
                                    class="px-4 h-10 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl text-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                                    </svg>
                                    Filter
                                </button>
                                <button type="button" onclick="resetFilters()" 
                                    class="px-4 h-10 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Container untuk tabel -->
                    <div class="table-container w-full overflow-hidden">
                        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300">
                            <table class="w-full divide-y divide-gray-200" id="historyTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Input Date</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Approve 1</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Approve 2</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <script>
        // API Base URL
        const API_BASE_URL = 'https://web.kobin.co.id/api/hris/history/get_history.php';
        
        // Ambil NIK dari session Laravel
        const USER_NIK = '{{ session('nik') }}';
        const USER_PLANT = '{{ session('plant', 'DEFAULT') }}';
        
        // State management
        let allData = [];

        // DOM Elements
        const loadingOverlay = document.getElementById('loadingOverlay');
        const tableBody = document.getElementById('tableBody');
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const alertSuccess = document.getElementById('alertSuccess');
        const alertError = document.getElementById('alertError');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            if (!USER_NIK) {
                showAlert('error', 'NIK tidak ditemukan dalam session');
                return;
            }
            
            setDefaultDates();
            loadHistoryData();
            
            // Event listeners
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                filterData();
            });
        });

        // Set default dates (bulan ini)
        function setDefaultDates() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            
            startDateInput.value = formatDateForInput(firstDay);
            endDateInput.value = formatDateForInput(lastDay);
        }

        // Format date untuk input date
        function formatDateForInput(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Format tanggal dari API
        function formatAPIDate(dateStr) {
            if (!dateStr) return '-';
            try {
                const date = new Date(dateStr);
                if (isNaN(date.getTime())) return dateStr;
                
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            } catch (e) {
                return dateStr;
            }
        }

        // Show/Hide Loading
        function showLoading() {
            loadingOverlay.classList.remove('hidden');
        }

        function hideLoading() {
            loadingOverlay.classList.add('hidden');
        }

        // Show Alert
        function showAlert(type, message) {
            if (type === 'success') {
                successMessage.textContent = message;
                alertSuccess.classList.remove('hidden');
                setTimeout(() => {
                    alertSuccess.classList.add('hidden');
                }, 3000);
            } else {
                errorMessage.textContent = message;
                alertError.classList.remove('hidden');
                setTimeout(() => {
                    alertError.classList.add('hidden');
                }, 5000);
            }
            
            // Also show toastr
            if (type === 'success') {
                toastr.success(message);
            } else {
                toastr.error(message);
            }
        }

        // Load history data from API
        async function loadHistoryData() {
            try {
                showLoading();
                
                const url = `${API_BASE_URL}?nik=${USER_NIK}&plant=${USER_PLANT}&_=${Date.now()}`;
                
                console.log('Fetching:', url);
                
                const response = await fetch(url);
                const result = await response.json();
                
                console.log('API Response:', result);
                
                if (result.success) {
                    allData = result.data || [];
                    renderTable(allData);
                    showAlert('success', 'Data history berhasil dimuat');
                } else {
                    throw new Error(result.message || 'Gagal memuat data');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Gagal memuat data: ' + error.message);
                renderEmptyTable();
            } finally {
                hideLoading();
            }
        }

        // Filter data
        function filterData() {
            const start = startDateInput.value;
            const end = endDateInput.value;
            
            if (!start || !end) {
                renderTable(allData);
                return;
            }
            
            const startDateObj = new Date(start);
            const endDateObj = new Date(end);
            endDateObj.setHours(23, 59, 59, 999);
            
            const filtered = allData.filter(item => {
                const itemDateStr = item.crtd;
                if (!itemDateStr) return false;
                
                const itemDate = new Date(itemDateStr);
                return itemDate >= startDateObj && itemDate <= endDateObj;
            });
            
            renderTable(filtered);
        }

        // Reset filters
        function resetFilters() {
            setDefaultDates();
            renderTable(allData);
        }

        // Refresh data
        function refreshData() {
            const refreshIcon = document.querySelector('.refresh-icon');
            refreshIcon.classList.add('animate-spin');
            
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }

        // Get badge class based on status
        function getBadgeClass(status) {
            if (status === 1) {
                return 'bg-green-500';
            } else if (status === 0) {
                return 'bg-red-500';
            } else {
                return 'bg-yellow-500';
            }
        }

        function getStatusText(status, approval, approval2) {
            if (status === 0) {
                return 'Rejected';
            } else {
                if (approval === 0) {
                    return 'Need Approve 1';
                } else if (approval === 1 && (approval2 === 0 || approval2 === null)) {
                    return 'Need Approve 2';
                } else if (approval === 1 && approval2 === 1) {
                    return 'Approved';
                }
            }
            return 'Unknown';
        }

        // Render table
        function renderTable(data) {
            if (!data || data.length === 0) {
                renderEmptyTable();
                return;
            }

            let html = '';
            
            data.forEach((item, index) => {
                // LANGSUNG ambil dari response API
                const badgeClass = item.badge; // 'success', 'warning', 'danger'
                const statusText = item.status_text; // 'Approved', 'Need Approve 1', 'Need Approve 2', 'Rejected'
                
                // File upload link
                let fileHtml = '--';
                if (item.fileupload && item.fileupload !== '') {
                    const fileName = item.fileupload.split('/').pop();
                    
                    // Deteksi tipe dari prefix nama file
                    let folder = 'cuti-khusus';
                    if (fileName.startsWith('1200_')) {
                        folder = 'sakit';
                    } else if (fileName.startsWith('1300_')) {
                        folder = 'cuti-khusus';
                    }
                    
                    const dateMatch = fileName.match(/(\d{4})-(\d{2})-\d{2}/);
                    const tahun = dateMatch ? dateMatch[1] : new Date().getFullYear();
                    const bulan = dateMatch ? dateMatch[2] : String(new Date().getMonth() + 1).padStart(2, '0');
                    
                    const laravelFileUrl = `/storage/izin/${tahun}/${bulan}/${folder}/${fileName}`;
                    
                    fileHtml = `<a href="${laravelFileUrl}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">View</a>`;
                }
                
                html += `
                    <tr class="hover:bg-gray-50 transition-colors duration-100">
                        <td class="px-4 py-3 text-center">${item.firstname || '-'}</td>
                        <td class="px-4 py-3 text-center">${item.tipe || '-'}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap" data-order="${toSortableTimestamp(item.crtd)}">${formatAPIDate(item.crtd)}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap" data-order="${toSortableTimestamp(item.validfrom)}">${formatAPIDate(item.validfrom)}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap" data-order="${toSortableTimestamp(item.enddate)}">${formatAPIDate(item.enddate)}</td>
                        <td class="px-4 py-3 text-left max-w-xs break-words">${item.ShortText || '-'}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">${item.approval1by || '—'}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">${item.approval2by || '—'}</td>
                        <td class="px-4 py-3 text-center">${fileHtml}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge-${badgeClass} text-white px-2 py-1 rounded-full text-xs whitespace-nowrap">${statusText}</span>
                        </td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;
            
            // Initialize DataTable
            setTimeout(() => {
                if ($.fn.DataTable.isDataTable('#historyTable')) {
                    $('#historyTable').DataTable().destroy();
                }
                
                $('#historyTable').DataTable({
                    order: [[2, 'desc']], // Sort by Input Date
                    pageLength: 25,
                    columnDefs: [
                        { orderable: false, targets: [6, 7, 8, 9] }
                    ],
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Tidak ada data",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        zeroRecords: "Tidak ada data yang ditemukan",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "→",
                            previous: "←"
                        }
                    }
                });
            }, 100);
        }

        function toSortableTimestamp(dateStr) {
            if (!dateStr) return '0';
            try {
                const date = new Date(dateStr);
                if (isNaN(date.getTime())) return '0';
                return date.getTime().toString(); // pakai milliseconds, pasti akurat
            } catch(e) {
                return '0';
            }
        }

        // Render empty table
        function renderEmptyTable() {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <span>Tidak ada data ditemukan</span>
                        </div>
                    </td>
                </tr>
            `;
            
            // Initialize DataTable with empty data
            setTimeout(() => {
                if ($.fn.DataTable.isDataTable('#historyTable')) {
                    $('#historyTable').DataTable().destroy();
                }
                
                $('#historyTable').DataTable({
                    pageLength: 25,
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        zeroRecords: "Tidak ada data"
                    }
                });
            }, 100);
        }

        // Foto modal functions
        function openFotoModal(url) {
            document.getElementById('modalFoto').src = url;
            document.getElementById('fotoModal').classList.remove('hidden');
            document.getElementById('fotoModal').classList.add('flex');
        }

        function closeFotoModal() {
            document.getElementById('fotoModal').classList.remove('flex');
            document.getElementById('fotoModal').classList.add('hidden');
        }
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
            border-color: #3b82f6 !important;
            ring: 2px solid #3b82f6 !important;
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
            border-radius: 0.375rem !important;
            background: white !important;
            color: #374151 !important;
            cursor: pointer !important;
            font-size: 0.875rem !important;
            transition: all 0.2s !important;
        }

        .dataTables_paginate .paginate_button:hover {
            background: #f3f4f6 !important;
            border-color: #9ca3af !important;
        }

        .dataTables_paginate .paginate_button.current {
            background: #3b82f6 !important;
            border-color: #3b82f6 !important;
            color: white !important;
        }

        .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        /* Table Styling */
        #historyTable {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        #historyTable thead th {
            background-color: #f9fafb !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            color: #6b7280 !important;
            border-bottom: 2px solid #e5e7eb !important;
            white-space: nowrap !important;
        }

        #historyTable tbody td {
            padding: 0.75rem 0.5rem !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #e5e7eb !important;
            font-size: 0.875rem !important;
        }

        #historyTable tbody tr:hover {
            background-color: #f9fafb !important;
        }

        /* Status Badge Styling */
        .badge-success {
            background-color: #10b981;
        }

        .badge-warning {
            background-color: #f59e0b;
        }

        .badge-danger {
            background-color: #ef4444;
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
            
            #historyTable tbody td {
                padding: 0.5rem 0.25rem !important;
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

        /* Animation */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }
        
        /* Modal styles */
        #fotoModal.flex {
            display: flex;
        }
    </style>
</x-app-layout>