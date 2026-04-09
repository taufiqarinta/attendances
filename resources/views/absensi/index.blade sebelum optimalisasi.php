<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Data Absensi Karyawan') }}
            </h2>
            <div class="flex space-x-2">
                <!-- TOMBOL TAMBAH ABSENSI BARU -->
                <a href="{{ route('absensi.create') }}" 
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded flex items-center gap-2 transition duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Absensi
                </a>
                
                <button onclick="refreshData()" 
                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded flex items-center gap-2 transition duration-200">
                    <svg class="w-4 h-4 refresh-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
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
                    <svg class="animate-spin h-10 w-10 text-blue-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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

            <!-- Header Section dengan Filter -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 p-2">
                <div class="w-full">
                    <form id="filterForm" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                        <!-- Search by NIK or Nama -->
                        <div class="col-span-1 sm:col-span-2">
                            <input type="text" id="searchInput" name="search" 
                                placeholder="Cari NIK atau Nama Karyawan..." 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Filter Tanggal Mulai -->
                        <div>
                            <input type="date" id="startDate" name="start_date" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Filter Tanggal Akhir -->
                        <div>
                            <input type="date" id="endDate" name="end_date" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-500 flex-1">
                                Filter
                            </button>
                            <button type="button" onclick="resetFilters()" 
                                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info dan Limit Selector -->
            <div class="flex justify-between items-center mb-4 p-2">
                <div class="text-sm text-gray-600" id="paginationInfo">
                    Menampilkan data
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Limit:</label>
                    <select id="limitSelect"
                        class="w-20 px-2 py-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    
                    <!-- Container untuk tabel dengan scroll horizontal -->
                    <div class="table-container">
                        <div class="overflow-x-scroll">
                            <table class="responsive-table">
                                <thead>
                                    <tr>
                                        <th class="column-nik">NIK</th>
                                        <th class="column-nama">Nama Karyawan</th>
                                        <th class="column-datetime">Tanggal & Jam</th>
                                        <th class="column-checktype">In/Out</th>
                                        <th class="column-foto">Foto</th>
                                        <th class="column-lokasi">Lokasi</th>
                                        <!-- END -->
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <tr>
                                        <td colspan="5" class="text-center py-8">
                                            <div class="flex flex-col items-center justify-center text-gray-400">
                                                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                                </svg>
                                                <span>Belum ada data. Silakan lakukan filter atau refresh.</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination Links -->
                    <div class="mt-6" id="paginationContainer">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="text-sm text-gray-600" id="paginationDetail"></div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination" id="paginationLinks">
                                <!-- Pagination akan diisi oleh JavaScript -->
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // State management
        let currentPage = 1;
        let currentLimit = 25;
        let currentSearch = '';
        let currentStartDate = '';
        let currentEndDate = '';
        let totalData = 0;
        let totalPages = 1;
        let isFetching = false;
        let refreshInterval = null;

        // API Base URL
        const API_BASE_URL = 'https://web.kobin.co.id/api/hris/absensi/get_absensi.php';

        // DOM Elements
        const loadingOverlay = document.getElementById('loadingOverlay');
        const tableBody = document.getElementById('tableBody');
        const searchInput = document.getElementById('searchInput');
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const limitSelect = document.getElementById('limitSelect');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationDetail = document.getElementById('paginationDetail');
        const paginationLinks = document.getElementById('paginationLinks');
        const alertSuccess = document.getElementById('alertSuccess');
        const alertError = document.getElementById('alertError');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');

        // Set default dates (bulan ini)
        function setDefaultDates() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            
            currentStartDate = formatDateForInput(firstDay);
            currentEndDate = formatDateForInput(lastDay);
            
            startDateInput.value = currentStartDate;
            endDateInput.value = currentEndDate;
        }

        // Format date untuk input date
        function formatDateForInput(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Format date untuk display
        function formatDisplayDate(dateTimeStr) {
            if (!dateTimeStr) return '-';
            const date = new Date(dateTimeStr);
            return date.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
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
                }, 3000);
            }
        }

        // Fetch data dari API
        let currentController = null;

        async function fetchAttendanceData() {
            // Batalkan request sebelumnya jika masih berjalan
            if (currentController) {
                currentController.abort();
            }
            currentController = new AbortController();
            
            try {
                isFetching = true;
                showLoading();

                const params = new URLSearchParams({
                    page: currentPage,
                    limit: currentLimit,
                    user_nik: '{{ session('nik') }}'
                });

                if (currentSearch) params.append('search', currentSearch);
                if (currentStartDate) params.append('start_date', currentStartDate);
                if (currentEndDate) params.append('end_date', currentEndDate);

                const response = await fetch(`${API_BASE_URL}?${params.toString()}`, {
                    signal: currentController.signal  // ← tambahkan ini
                });
                const result = await response.json();

                if (result.success) {
                    const data = result.data || [];

                    if (result.pagination) {
                        totalData = result.pagination.total || 0;
                        totalPages = result.pagination.total_pages || 1;
                        currentPage = result.pagination.current_page || currentPage;
                        // currentLimit TIDAK diambil dari API
                    } else {
                        totalData = data.length;
                        totalPages = 1;
                        currentPage = 1;
                    }

                    renderTable(data);
                    updatePaginationInfo();
                    renderPaginationLinks();

                    if (data.length > 0) {
                        await verifyBatchData(data);
                    }
                } else {
                    throw new Error(result.message || 'Gagal mengambil data');
                }

            } catch (error) {
                if (error.name === 'AbortError') return; // Request sengaja dibatalkan, abaikan
                console.error('❌ Error fetching data:', error);
                showAlert('error', 'Gagal memuat data: ' + error.message);
                renderEmptyTable();
            } finally {
                isFetching = false;
                hideLoading();
            }
        }

        // Update fungsi renderPaginationLinks untuk handle kasus khusus
        function renderPaginationLinks() {
            // Jika totalPages tidak valid atau tidak ada data
            if (totalPages <= 1 || totalData === 0) {
                paginationLinks.innerHTML = '';
                return;
            }

            let html = '';

            // Previous button
            html += `
                <button 
                    onclick="changePage(${currentPage - 1})" 
                    ${currentPage === 1 ? 'disabled' : ''}
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium ${currentPage === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-500 hover:bg-gray-50'}">
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;

            // Batasi jumlah halaman yang ditampilkan
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            // Adjust startPage jika endPage melebihi batas
            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            // First page with ellipsis
            if (startPage > 1) {
                html += `
                    <button onclick="changePage(1)" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        1
                    </button>
                `;
                if (startPage > 2) {
                    html += `
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                            ...
                        </span>
                    `;
                }
            }

            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                html += `
                    <button 
                        onclick="changePage(${i})" 
                        class="relative inline-flex items-center px-4 py-2 border ${i === currentPage ? 'border-indigo-500 bg-indigo-50 text-indigo-600 z-10' : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'} text-sm font-medium">
                        ${i}
                    </button>
                `;
            }

            // Last page with ellipsis
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += `
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                            ...
                        </span>
                    `;
                }
                html += `
                    <button onclick="changePage(${totalPages})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        ${totalPages}
                    </button>
                `;
            }

            // Next button
            html += `
                <button 
                    onclick="changePage(${currentPage + 1})" 
                    ${currentPage === totalPages ? 'disabled' : ''}
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium ${currentPage === totalPages ? 'text-gray-300 cursor-not-allowed' : 'text-gray-500 hover:bg-gray-50'}">
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            `;

            paginationLinks.innerHTML = html;
        }

        // Update fungsi updatePaginationInfo
        function updatePaginationInfo() {
            if (totalData === 0) {
                paginationInfo.innerHTML = 'Tidak ada data';
                paginationDetail.innerHTML = '';
                return;
            }

            const start = ((currentPage - 1) * currentLimit) + 1;
            const end = Math.min(currentPage * currentLimit, totalData);
            
            paginationInfo.innerHTML = `Menampilkan data ke ${start} - ${end} dari ${totalData} data`;
            paginationDetail.innerHTML = `Halaman ${currentPage} dari ${totalPages}`;
        }


        // Verifikasi batch data ke API
        async function verifyBatchData(data) {
            if (!data || data.length === 0) return;

            try {
                const dataToVerify = data.map(item => ({
                    PersonnelNo: item.PersonnelNo,
                    CurrentDateTime: item.CurrentDateTime,
                    CheckType: item.CheckType
                }));

                const response = await fetch(API_BASE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ data: dataToVerify })
                });

                const result = await response.json();
                // Bisa ditambahkan logic untuk update status verified jika diperlukan
                console.log('✅ Verifikasi selesai:', result);

            } catch (error) {
                console.error('❌ Error verifying data:', error);
            }
        }

        // Render tabel
        function renderTable(data) {
            if (!data || data.length === 0) {
                renderEmptyTable();
                return;
            }

            let html = '';
            
            data.forEach((item, index) => {
                const displayDateTime = formatDisplayDate(item.CurrentDateTime);
                const rowKey = `${item.PersonnelNo}-${item.CurrentDateTime}-${index}`;
                
                // HTML untuk Foto (seperti di Filament)
                let fotoHtml = '<span class="text-gray-400">-</span>';
                if (item.file_foto) {
                    // Bersihkan path file_foto dari "public/" jika ada
                    const fotoPath = item.file_foto.replace('public/', '');
                    const fotoUrl = `/storage/${fotoPath}`;
                    
                    // Cek dulu apakah file benar-benar ada (opsional, bisa via AJAX)
                    fotoHtml = `
                        <div class="flex justify-center">
                            <img src="${fotoUrl}" 
                                alt="Foto ${item.PersonnelNo}" 
                                class="foto-thumbnail"
                                onerror="this.onerror=null; this.parentElement.innerHTML = '<span class=\'text-red-500 text-xs\'>Foto tidak ditemukan</span>';"
                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 1px solid #e5e7eb;"
                                onclick="openFotoModal('${fotoUrl}', '${item.PersonnelNo}')">
                        </div>
                    `;
                }
                
                // HTML untuk Lokasi (seperti di Filament)
                let lokasiHtml = '<span class="text-gray-400">-</span>';
                if (item.Latitude && item.Longitude && 
                    item.Latitude !== '0' && item.Longitude !== '0' && 
                    item.Latitude !== null && item.Longitude !== null) {
                    const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${item.Latitude},${item.Longitude}`;
                    lokasiHtml = `
                        <a href="${mapsUrl}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Lihat Lokasi
                        </a>
                    `;
                }
                
                html += `
                    <tr data-key="${rowKey}">
                        <td class="column-nik" data-column="personnelno">${item.PersonnelNo || '-'}</td>
                        <td class="column-nama">${item.nama_karyawan || '-'}</td>
                        <td class="column-datetime" data-column="currentdatetime">${displayDateTime}</td>
                        <td class="column-checktype" data-column="checktype">
                            <span class="px-2 py-1 text-xs font-medium ${
                                (item.CheckType || '').toLowerCase().trim() === 'in'
                                ? 'bg-green-500 text-white'
                                : 'bg-red-100 text-red-800'
                            } rounded-full">
                                ${item.CheckType || '-'}
                            </span>
                        </td>
                        <td class="column-foto text-center">${fotoHtml}</td>
                        <td class="column-lokasi">${lokasiHtml}</td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;
        }

        // Render empty table
        function renderEmptyTable() {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <span>Tidak ada data ditemukan</span>
                        </div>
                    </td>
                </tr>
            `;
        }

        // Modal untuk melihat foto ukuran penuh
        function openFotoModal(fotoUrl, nik = '') {
            // Hapus modal yang sudah ada jika ada
            const existingModal = document.getElementById('fotoModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Buat modal element
            const modal = document.createElement('div');
            modal.id = 'fotoModal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4';
            modal.onclick = function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            };
            
            modal.innerHTML = `
                <div style="
                    position: relative;
                    max-width: 900px;
                    max-height: 100%;
                    background: white;
                    border-radius: 10px;
                    overflow: hidden;
                ">
                    <div style="position: relative;">
                        <img src="${fotoUrl}" 
                            style="
                                max-width: 100%;
                                max-height: 90vh;
                                object-fit: contain;
                                display: block;
                            "
                            alt="Foto Absensi ${nik}"
                            onerror="this.onerror=null; this.parentElement.innerHTML = '<div style=\'padding: 50px; text-align: center; background: #f3f4f6;\'><svg class=\'w-16 h-16 text-gray-400 mx-auto mb-3\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg><p class=\'text-gray-600\'>Foto tidak ditemukan atau gagal dimuat</p></div>';">

                        <button 
                            onclick="this.closest('#fotoModal').remove()"
                            style="
                                position: absolute;
                                top: 15px;
                                right: 15px;
                                background: white;
                                border-radius: 50%;
                                padding: 8px;
                                border: none;
                                cursor: pointer;
                                box-shadow: 0 4px 10px rgba(0,0,0,0.2);
                                z-index: 60;
                            "
                            onmouseover="this.style.background='#f3f4f6'"
                            onmouseout="this.style.background='white'"
                        >
                            <svg 
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="black"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                style="display:block;"
                            >
                                <line x1="6" y1="18" x2="18" y2="6"></line>
                                <line x1="6" y1="6" x2="18" y2="12"></line>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // Change page
        function changePage(page) {
            if (page < 1 || page > totalPages || page === currentPage) return;
            
            console.log(`📄 Changing page from ${currentPage} to ${page}`);
            currentPage = page;
            
            // Scroll ke atas tabel
            document.querySelector('.table-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            fetchAttendanceData();
        }

        // Reset filters
        function resetFilters() {
            currentSearch = '';
            currentPage = 1;
            setDefaultDates();
            
            searchInput.value = '';
            startDateInput.value = currentStartDate;
            endDateInput.value = currentEndDate;
            
            fetchAttendanceData();
        }

        // Refresh data
        function refreshData() {
            const refreshIcon = document.querySelector('.refresh-icon');
            refreshIcon.classList.add('animate-spin');
            fetchAttendanceData().finally(() => {
                setTimeout(() => {
                    refreshIcon.classList.remove('animate-spin');
                }, 500);
            });
        }

        // Auto refresh setiap 30 detik (opsional, bisa di-disable jika tidak diperlukan)
        function startAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
            refreshInterval = setInterval(() => {
                if (!isFetching && !document.hidden) {
                    console.log('🔄 Auto-refreshing data...');
                    fetchAttendanceData();
                }
            }, 30000); // 30 detik
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            setDefaultDates();
            fetchAttendanceData();
            // startAutoRefresh();

            // Event listeners
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                currentSearch = searchInput.value;
                currentStartDate = startDateInput.value;
                currentEndDate = endDateInput.value;
                currentPage = 1;
                isFetching = false; // Reset agar tidak ter-block
                fetchAttendanceData();
            });

            limitSelect.addEventListener('change', function() {
                currentLimit = parseInt(this.value);
                currentPage = 1; // Reset ke halaman pertama saat ganti limit
                fetchAttendanceData();
            });

            // Search dengan debounce
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentSearch = this.value;
                    currentPage = 1; // Reset ke halaman pertama saat search
                    fetchAttendanceData();
                }, 500);
            });
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });

        // Handle when page becomes visible again (untuk auto refresh)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && !isFetching) {
                // Optional: refresh when page becomes visible
                // fetchAttendanceData();
            }
        });

    </script>

    <style>
        .table-container {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: white;
            overflow: hidden;
        }

        .overflow-x-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .overflow-x-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-scroll::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 4px;
        }

        .overflow-x-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        .overflow-x-scroll::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .responsive-table {
            width: 100%;
            min-width: 1000px;
            border-collapse: collapse;
        }

        .responsive-table th,
        .responsive-table td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .responsive-table th {
            background-color: #f9fafb;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .responsive-table td {
            font-size: 0.875rem;
            color: #374151;
        }

        .responsive-table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Lebar kolom spesifik */
        .column-nik { width: 120px; min-width: 120px; }
        .column-nama { width: 200px; min-width: 200px; }
        .column-datetime { width: 180px; min-width: 180px; }
        .column-checktype { width: 100px; min-width: 100px; }
        .column-foto { 
            width: 100px; 
            min-width: 100px; 
            text-align: center; 
        }

        .column-lokasi { 
            width: 120px; 
            min-width: 120px; 
        }

        .foto-thumbnail {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .foto-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .table-container {
                margin: 0;
                padding: 0 1rem;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
            
            .responsive-table {
                min-width: 800px;
            }
        }

        /* Animasi */
        .animate-spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        /* Loading Overlay */
        #loadingOverlay {
            transition: opacity 0.3s ease;
        }

        #loadingOverlay.hidden {
            opacity: 0;
            pointer-events: none;
        }

        /* Pagination button disabled state */
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        button:disabled:hover {
            background-color: white;
        }
    </style>
</x-app-layout>