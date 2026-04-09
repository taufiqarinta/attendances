<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Absences & Attendance') }}
            </h2>
            <div class="flex space-x-2">
                <button onclick="modalAdd()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New
                </button>
                <button onclick="refreshData()" 
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center gap-2">
                    <svg class="w-4 h-4 refresh-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </x-slot>

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

            <!-- Info Sisa Cuti -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">
                                Sisa Cuti Anda: <span id="sisaCuti" class="font-bold text-lg">0</span> hari
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Absences & Attendance -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    
                    <!-- Filter Section -->
                    <div class="mb-6">
                        <form id="filterForm" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                            <!-- Filter Tanggal Mulai -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input type="date" id="startDate" name="start_date" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Filter Tanggal Akhir -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input type="date" id="endDate" name="end_date" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="flex items-end gap-2">
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 flex-1">
                                    Filter
                                </button>
                                <button type="button" onclick="resetFilters()" 
                                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Container untuk tabel -->
                    <div class="table-container">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="tblabsences">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Input Date</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Time Off</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center py-8">
                                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                                </svg>
                                                <span>Memuat data...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add New -->
    <div class="fixed inset-0 hidden items-center justify-center z-50" id="exampleModal">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
        <div class="bg-white rounded-lg w-11/12 md:max-w-2xl max-h-[90vh] overflow-y-auto z-10">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Form New Absence & Attendance</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="add_absence_form" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date <span class="text-red-600">*</span></label>
                                <input type="text" class="form-control datepicker w-full border rounded-md p-2" name="startdate" id="startdate" placeholder="Pilih Tanggal Mulai" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date <span class="text-red-600">*</span></label>
                                <input type="text" class="form-control datepicker w-full border rounded-md p-2" name="enddate" id="enddate" placeholder="Pilih Tanggal Akhir" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe <span class="text-red-600">*</span></label>
                            <select class="w-full border rounded-md p-2" name="absencestype" id="absencestype" required>
                                <option value="" selected disabled>--Pilih Tipe--</option>
                                <optgroup label="Absence Types" id="absenceTypes"></optgroup>
                                <optgroup label="Attendance Types" id="attendanceTypes"></optgroup>
                            </select>
                        </div>

                        <div id="suratcontainer" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-1"><span id="suratlabel">Surat Dokter</span> <span class="text-red-600">*</span></label>
                            <input type="file" class="w-full border rounded-md p-2" name="buktiupload" id="buktiupload">
                        </div>

                        <div id="cuticontainer" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sisa Cuti</label>
                            <input type="number" readonly class="w-full border rounded-md p-2 bg-gray-100" name="sisacuti" id="sisacuti" value="0">
                        </div>

                        <div id="dispencontainer" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Dispen</label>
                            <div id="dispenOptions" class="space-y-2"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-red-600">*</span></label>
                            <textarea class="w-full border rounded-md p-2" id="keterangan" rows="4" name="keterangan" required></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Close</button>
                        <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" id="btnSubmitAbsence">Submit</button>
                    </div>
                </form>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> <!-- Toastr setelah jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    
    <script>
        // API Base URL
        const API_BASE_URL = 'https://web.kobin.co.id/api/hris/izin/get_izin.php';
        const API_POST_URL = 'https://web.kobin.co.id/api/hris/izin/post_izin.php';
        
        // Ambil NIK dari session Laravel
        const USER_NIK = '{{ session('nik') }}';
        const USER_PLANT = '{{ session('plant', 'DEFAULT') }}';
        
        // State management
        let allData = [];
        let referenceData = {
            tipe_absen: [],
            tipe_ijin: [],
            tipe_dispen: [],
            sisa_cuti: 0,
            valid_from: ''
        };

        // DOM Elements
        const loadingOverlay = document.getElementById('loadingOverlay');
        const tableBody = document.getElementById('tableBody');
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const typeFilter = document.getElementById('typeFilter');
        const alertSuccess = document.getElementById('alertSuccess');
        const alertError = document.getElementById('alertError');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');
        const sisaCutiSpan = document.getElementById('sisaCuti');

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            if (!USER_NIK) {
                showAlert('error', 'NIK tidak ditemukan dalam session');
                return;
            }
            
            setDefaultDates();
            loadReferenceData();
            initializeDatepickers();
            
            // Event listeners
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                filterData();
            });

            typeFilter.addEventListener('change', filterData);
        });

        $(document).ready(function() {
            // Re-initialize datepicker ketika modal dibuka
            $('#exampleModal').on('shown.bs.modal', function() {
                if (window.startDatePicker) {
                    window.startDatePicker.setDate(new Date());
                }
                if (window.endDatePicker) {
                    window.endDatePicker.setDate(new Date());
                }
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

        // Initialize datepickers
        function initializeDatepickers() {
            // Flatpickr untuk start date
            window.startDatePicker = flatpickr("#startdate", {
                locale: "id",
                dateFormat: "d/m/Y",
                allowInput: true,
                // minDate: "today",
                onChange: function(selectedDates, dateStr, instance) {
                    // Update end date jika tipe attendance
                    const tipe = $('#absencestype').val();
                    if (tipe && isAttendanceType(tipe)) {
                        $('#enddate').val(dateStr);
                    }
                    
                    // Update minDate end date jika end date aktif
                    if (window.endDatePicker && !window.endDatePicker.isDisabled) {
                        if (selectedDates.length > 0) {
                            window.endDatePicker.set('minDate', selectedDates[0]);
                        }
                    }
                }
            });

            // Flatpickr untuk end date
            window.endDatePicker = flatpickr("#enddate", {
                locale: "id",
                dateFormat: "d/m/Y",
                allowInput: true,
                // minDate: "today"
            });
            
            // Tambahkan property untuk menandai status disabled
            window.endDatePicker.isDisabled = false;
        }

        // Update fungsi modalAdd untuk menggunakan Flatpickr
        function modalAdd() {
            $('#exampleModal').removeClass('hidden').addClass('flex');
            
            // Set tanggal menggunakan Flatpickr
            const today = new Date();
            const todayFormatted = formatDateToDMY(today);
            
            if (window.startDatePicker) {
                window.startDatePicker.setDate(today);
            }
            
            // Reset end date ke normal
            if (window.endDatePicker && window.endDatePicker.isDisabled) {
                // Re-initialize Flatpickr untuk end date
                window.endDatePicker = flatpickr("#enddate", {
                    locale: "id",
                    dateFormat: "d/m/Y",
                    allowInput: true,
                    // minDate: "today"
                });
                window.endDatePicker.isDisabled = false;
            } else if (!window.endDatePicker) {
                window.endDatePicker = flatpickr("#enddate", {
                    locale: "id",
                    dateFormat: "d/m/Y",
                    allowInput: true,
                    // minDate: "today"
                });
                window.endDatePicker.isDisabled = false;
            }
            
            if (window.endDatePicker) {
                window.endDatePicker.setDate(today);
                window.endDatePicker.set('minDate', today);
            }
            
            $('#absencestype').val('');
            $('#suratcontainer').hide();
            $('#cuticontainer').hide();
            $('#dispencontainer').hide();
            $('#keterangan').val('');
            $('#buktiupload').val('');
            
            // Reset end date ke normal (tidak readonly)
            $('#enddate').prop('readonly', false);
            $('#enddate').removeClass('bg-gray-100');
        }

        // Fungsi bantuan untuk format dd/mm/yyyy
        function formatDateToDMY(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
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

        // Populate type options
        function populateTypeOptions() {
            const absenceSelect = $('#absencestype');
            absenceSelect.empty().append('<option value="" selected disabled>--Pilih Tipe--</option>');
            
            // Add absence types
            if (referenceData.tipe_absen && referenceData.tipe_absen.length > 0) {
                const absenceGroup = $('<optgroup label="Absence Types"></optgroup>');
                referenceData.tipe_absen.forEach(item => {
                    let nama = item.nama || item.Nama;
                    if (item.Code == '1200') nama = 'SAKIT';
                    if (item.Code == '1300') nama = 'CUTI KHUSUS';
                    absenceGroup.append(`<option value="${item.Code}">${nama}</option>`);
                });
                absenceSelect.append(absenceGroup);
            }
            
            // Add attendance types
            if (referenceData.tipe_ijin && referenceData.tipe_ijin.length > 0) {
                const attendanceGroup = $('<optgroup label="Attendance Types"></optgroup>');
                referenceData.tipe_ijin.forEach(item => {
                    attendanceGroup.append(`<option value="${item.Code}">${item.Nama || item.nama}</option>`);
                });
                absenceSelect.append(attendanceGroup);
            }
            
            // DEBUG: Cek data dispen
            console.log('Data Dispen dari API:', referenceData.tipe_dispen);
            
            // Populate dispen options
            if (referenceData.tipe_dispen && referenceData.tipe_dispen.length > 0) {
                let dispenHtml = '';
                referenceData.tipe_dispen.forEach((item, i) => {
                    console.log('Item Dispen:', item); // Debug
                    const hariText = item.Hari >= 28 ? `(${Math.round(item.Hari/30)} bulan)` : `(${item.Hari} hari)`;
                    dispenHtml += `
                        <div class="flex items-center mb-2">
                            <input type="radio" class="mr-2" name="jenisdispen" id="dispen-${i}" value="${item.Code}" required>
                            <label for="dispen-${i}">${item.Keterangan || item.Nama} ${hariText}</label>
                        </div>
                    `;
                });
                $('#dispenOptions').html(dispenHtml);
                console.log('Dispen HTML:', dispenHtml); // Debug
            } else {
                console.warn('Data dispen kosong atau tidak ditemukan');
                $('#dispenOptions').html('<p class="text-red-500">Data dispen tidak tersedia</p>');
            }
        }

        // Filter data
        function filterData() {
            const start = startDateInput.value;
            const end = endDateInput.value;
            const type = typeFilter.value;
            
            let filtered = [...allData];
            
            // Filter by date range
            if (start && end) {
                filtered = filtered.filter(item => {
                    const itemDate = new Date(item.created || item.ValidFrom || 0);
                    const startDate = new Date(start);
                    const endDate = new Date(end);
                    return itemDate >= startDate && itemDate <= endDate;
                });
            }
            
            // Filter by type
            if (type) {
                filtered = filtered.filter(item => item.usage === type);
            }
            
            renderTable(filtered);
        }

        // Render table
        function renderTable(data) {
            if (!data || data.length === 0) {
                renderEmptyTable();
                return;
            }

            let html = '';
            
            data.sort((a, b) => {
                const dateA = new Date(a.created || a.ValidFrom || 0);
                const dateB = new Date(b.created || b.ValidFrom || 0);
                return dateB - dateA;
            });
            
            data.forEach((item, index) => {
                // Determine badge color and status text
                let badgeClass = 'bg-gray-500';
                let statusText = 'Unknown';
                
                if (item.status === 0) {
                    if (item.status === "") {
                        badgeClass = 'bg-gray-500';
                        statusText = 'Cancel';
                    } else {
                        badgeClass = 'bg-red-500';
                        statusText = 'Rejected';
                    }
                } else {
                    if (item.approval === 0) {
                        badgeClass = 'bg-yellow-500';
                        statusText = 'Need Approve 1';
                    } else if (item.approval === 1 && item.approval2 === 0) {
                        badgeClass = 'bg-yellow-500';
                        statusText = 'Need Approve 2';
                    } else if (item.approval === 1 && item.approval2 === 1) {
                        badgeClass = 'bg-green-500';
                        statusText = 'Approved';
                    }
                }
                
                // File upload link
                let fileHtml = '--';
                if (item.fileupload && item.fileupload !== '') {
                    const fileName = item.fileupload.split('/').pop(); // Ambil nama file
                    
                    // Deteksi tipe dari prefix nama file
                    let folder = 'cuti-khusus'; // default
                    if (fileName.startsWith('1200_')) {
                        folder = 'sakit';
                    } else if (fileName.startsWith('1300_')) {
                        folder = 'cuti-khusus';
                    }
                    
                    // Ekstrak tahun dan bulan dari nama file atau gunakan current
                    // Format: 1200_20425679_2026-03-17_1773728744.pdf
                    const dateMatch = fileName.match(/(\d{4})-(\d{2})-\d{2}/);
                    const tahun = dateMatch ? dateMatch[1] : '2026';
                    const bulan = dateMatch ? dateMatch[2] : '03';
                    
                    const laravelFileUrl = `/storage/izin/${tahun}/${bulan}/${folder}/${fileName}`;
                    
                    fileHtml = `<a href="${laravelFileUrl}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">View</a>`;
                }
                
                // Action buttons
                let actionHtml = '';
                if (item.approval2 === 1 || item.status === 0) {
                    actionHtml = `<button class="bg-gray-400 text-white px-3 py-1 rounded text-sm cursor-not-allowed" disabled>Cancel</button>`;
                } else {
                    actionHtml = `<button class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm cancel-btn" 
                        data-personnelno="${item.personnelno}" 
                        data-startdate="${item.sd || item.ValidFrom}" 
                        data-enddate="${item.ed || item.EndDate}">
                        Cancel
                    </button>`;
                }
                
                // Print button for approved dinas luar
                if (item.approval2 === 1 && item.absencetype === '1400') {
                    const printData = btoa(JSON.stringify({
                        nik: item.personnelno,
                        v_date: item.sd || item.ValidFrom,
                        e_date: item.ed || item.EndDate
                    }));
                    actionHtml += `<button class="bg-yellow-500 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm ml-1 print-btn" onclick="doPrint('${printData}')">
                        Print
                    </button>`;
                }
                
                html += `
                    <tr>
                        <td class="px-6 py-4 text-center">${item.tipe || '-'}</td>
                        <td class="px-6 py-4 text-center">${formatAPIDate(item.created || item.Crtd)}</td>
                        <td class="px-6 py-4 text-center">${formatAPIDate(item.validfrom || item.ValidFrom)}</td>
                        <td class="px-6 py-4 text-center">${formatAPIDate(item.enddate || item.EndDate)}</td>
                        <td class="px-6 py-4 text-center">${item.timeoff || 0}</td>
                        <td class="px-6 py-4 text-center">${fileHtml}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="${badgeClass} text-white px-2 py-1 rounded-full text-xs">${statusText}</span>
                            ${item.reason_cancel ? `<br><span class="text-red-600 text-xs"><i>Reason: ${item.reason_cancel}</i></span>` : ''}
                        </td>
                        <td class="px-6 py-4 text-center">${actionHtml}</td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;
            
            // Initialize DataTable after table is populated
            setTimeout(() => {
                if ($.fn.DataTable.isDataTable('#tblabsences')) {
                    $('#tblabsences').DataTable().destroy();
                }
                
                $('#tblabsences').DataTable({
                    order: [[1, 'desc']],
                    pageLength: 25
                    // Hapus bagian language
                });
            }, 100);
        }

        // Render empty table
        function renderEmptyTable() {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <span>Tidak ada data ditemukan</span>
                        </div>
                    </td>
                </tr>
            `;
        }

        // Reset filters
        function resetFilters() {
            setDefaultDates();
            typeFilter.value = '';
            renderTable(allData);
        }

        // Refresh data
        function refreshData() {
            const refreshIcon = document.querySelector('.refresh-icon');
            refreshIcon.classList.add('animate-spin');
            
            // Force reload dengan menghapus cache
            setTimeout(() => {
                window.location.reload();
            }, 3000); // Delay 3 detik sebelum reload
        }

        // Modifikasi loadReferenceData dengan parameter force refresh
        async function loadReferenceData(forceRefresh = false) {
            try {
                showLoading();
                
                // Tambahkan timestamp untuk menghindari cache
                const timestamp = forceRefresh ? `&_=${Date.now()}` : '';
                const url = `${API_BASE_URL}?endpoint=absence&nik=${USER_NIK}&tahun=${new Date().getFullYear()}&plant=${USER_PLANT}${timestamp}`;
                
                console.log('Fetching:', url);
                
                // HAPUS header yang bermasalah, cukup gunakan timestamp di URL
                const response = await fetch(url);
                
                const result = await response.json();
                
                console.log('API Response:', result);
                
                if (result.success) {
                    allData = result.data || [];
                    referenceData = result.reference_data || {};
                    
                    // Update sisa cuti
                    if (referenceData.sisa_cuti && referenceData.sisa_cuti.length > 0) {
                        const sisaCuti = referenceData.sisa_cuti[0]?.SisaCuti || 0;
                        sisaCutiSpan.textContent = sisaCuti;
                        $('#sisacuti').val(sisaCuti);
                    }
                    
                    // Populate select options
                    populateTypeOptions();
                    
                    // Render table
                    renderTable(allData);
                    
                    showAlert('success', 'Data berhasil dimuat');
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

        function closeModal() {
            $('#exampleModal').removeClass('flex').addClass('hidden');
        }

        // Cancel button handler
        $(document).on('click', '.cancel-btn', function() {
            const personnelno = $(this).data('personnelno');
            const startdate = $(this).data('startdate');
            const enddate = $(this).data('enddate');
            
            Swal.fire({
                title: "Cancel?",
                text: "Apakah Anda Yakin untuk Cancel pengajuan ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Hapus!',
                input: 'textarea',
                inputLabel: 'Alasan Cancel',
                inputPlaceholder: 'Masukkan Alasan Cancel...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Anda harus mengisi Alasan Cancel!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // TODO: Implement cancel API call
                    showAlert('success', 'Pengajuan berhasil di Cancel');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            });
        });

        // Type change handler
        $('#absencestype').change(function() {
            const val = $(this).val();
            
            // Handle container visibility
            if (val === '1200' || val === '1300') {
                $('#suratcontainer').show();
                $('#buktiupload').prop('required', true);
            } else {
                $('#suratcontainer').hide();
                $('#buktiupload').prop('required', false);
            }

            if (val === '1200') {
                $('#suratlabel').text('Surat Dokter');
            }

            if (val === '1100') {
                $('#cuticontainer').show();
            } else {
                $('#cuticontainer').hide();
            }

            if (val === '1300') {
                $('#dispencontainer').show();
                $('input[name=jenisdispen]').prop('required', true);
                $('#suratlabel').text('Bukti Upload');
            } else {
                $('#dispencontainer').hide();
                $('input[name=jenisdispen]').prop('checked', false).prop('required', false);
            }
            
            // CEK APAKAH INI ATTENDANCE
            if (isAttendanceType(val)) {
                console.log('Tipe attendance terpilih:', val);
                
                // Set end date = start date
                const startDateValue = $('#startdate').val();
                $('#enddate').val(startDateValue);
                
                // Nonaktifkan end date Flatpickr sepenuhnya
                if (window.endDatePicker) {
                    window.endDatePicker.destroy(); // Hancurkan instance Flatpickr
                    window.endDatePicker = null; // Set ke null
                    window.endDatePicker = {
                        isDisabled: true,
                        setDate: function() {}, // Dummy function
                        set: function() {} // Dummy function
                    };
                }
                
                // Buat input biasa (non-flatpickr)
                $('#enddate').prop('readonly', true);
                $('#enddate').addClass('bg-gray-100');
                $('#enddate').off('click'); // Hapus event click flatpickr
                
            } else {
                // Untuk absence, aktifkan kembali end date
                if (window.endDatePicker && window.endDatePicker.isDisabled) {
                    // Re-initialize Flatpickr untuk end date
                    window.endDatePicker = flatpickr("#enddate", {
                        locale: "id",
                        dateFormat: "d/m/Y",
                        allowInput: true,
                        // minDate: "today"
                    });
                    window.endDatePicker.isDisabled = false;
                } else if (!window.endDatePicker) {
                    // Buat baru jika belum ada
                    window.endDatePicker = flatpickr("#enddate", {
                        locale: "id",
                        dateFormat: "d/m/Y",
                        allowInput: true,
                        // minDate: "today"
                    });
                    window.endDatePicker.isDisabled = false;
                }
                
                $('#enddate').prop('readonly', false);
                $('#enddate').removeClass('bg-gray-100');
                
                // Set minDate end date berdasarkan start date
                if (window.startDatePicker) {
                    const startDate = window.startDatePicker.selectedDates[0];
                    if (startDate && window.endDatePicker) {
                        window.endDatePicker.set('minDate', startDate);
                    }
                }
            }
        });

        // Submit button handler - MODIFIED TO USE FETCH API
        $('#btnSubmitAbsence').click(async function() {
            const tipe = $('#absencestype').val();
            
            if (!tipe) {
                Swal.fire('Error', 'Pilih tipe terlebih dahulu', 'error');
                return;
            }
            
            // CEK APAKAH INI ATTENDANCE
            const isAttendance = isAttendanceType(tipe);
            
            // Validasi khusus untuk attendance
            if (isAttendance) {
                if (!$('#keterangan').val()) {
                    Swal.fire('Error', 'Keterangan Harus Diisi!', 'error');
                    return;
                }
                
                // Pastikan end date sama dengan start date untuk attendance
                const startDate = $('#startdate').val();
                $('#enddate').val(startDate);
                
                // Submit ke endpoint attendance
                await submitAttendance(tipe);
            } else {
                // Validasi untuk absence (seperti biasa)
                if (tipe === '1200' || tipe === '1300') {
                    if (!$('#buktiupload')[0].files.length) {
                        Swal.fire('Error', 'Upload Bukti File masih kosong!', 'error');
                        return;
                    }
                }
                
                if (tipe === '1100') {
                    const sisaCuti = parseInt($('#sisacuti').val());
                    if (sisaCuti === 0) {
                        Swal.fire('Gagal', 'Sisa Cuti Anda sudah habis!', 'error');
                        return;
                    }
                }
                
                if (!$('#keterangan').val()) {
                    Swal.fire('Error', 'Keterangan Harus Diisi!', 'error');
                    return;
                }
                
                // Submit ke endpoint absence
                await submitAbsence(tipe);
            }
        });

        // Update submitAttendance function - pastikan enddate dikirim
        async function submitAttendance(tipe) {
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            try {
                const formData = new FormData();
                formData.append('nik', USER_NIK);
                formData.append('plant', USER_PLANT);
                formData.append('startdate', $('#startdate').val());
                formData.append('enddate', $('#enddate').val()); // Pastikan enddate ikut terkirim
                formData.append('absencestype', tipe);
                formData.append('keterangan', $('#keterangan').val());
                
                // Kirim ke endpoint attendance
                const response = await fetch(`${API_POST_URL}?endpoint=attendance`, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                Swal.close();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    closeModal();
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: result.message || 'Terjadi kesalahan'
                    });
                }
                
            } catch (error) {
                console.error('Error:', error);
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menghubungi server'
                });
            }
        }

        // Fungsi untuk submit absence (TAMBAHKAN INI!)
        async function submitAbsence(tipe) {
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            try {
                // Prepare form data untuk PHP backend
                const formData = new FormData();
                formData.append('nik', USER_NIK);
                formData.append('plant', USER_PLANT);
                formData.append('startdate', $('#startdate').val());
                formData.append('enddate', $('#enddate').val());
                formData.append('absencestype', tipe);
                formData.append('keterangan', $('#keterangan').val());
                formData.append('sisacuti', $('#sisacuti').val());
                
                // Add jenisdispen if type is 1300
                if (tipe === '1300') {
                    const jenisDispen = $('input[name=jenisdispen]:checked').val();
                    if (!jenisDispen) {
                        Swal.fire('Error', 'Pilih jenis dispen terlebih dahulu', 'error');
                        return;
                    }
                    formData.append('jenisdispen', jenisDispen);
                }
                
                // Add file if type is 1200 or 1300
                if (tipe === '1200' || tipe === '1300') {
                    const file = $('#buktiupload')[0].files[0];
                    
                    // Upload file ke Laravel
                    const laravelFormData = new FormData();
                    laravelFormData.append('file', file);
                    laravelFormData.append('nik', USER_NIK);
                    laravelFormData.append('tipe', tipe);
                    laravelFormData.append('startdate', $('#startdate').val());
                    laravelFormData.append('enddate', $('#enddate').val());
                    
                    const laravelResponse = await fetch('/izin/upload-file', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: laravelFormData
                    });

                    const laravelResult = await laravelResponse.json();

                    if (laravelResult.success) {
                        // Kirim path file ke backend PHP
                        formData.append('laravel_file_path', laravelResult.data.file_path);
                    } else {
                        Swal.fire('Error', 'Gagal upload file ke server', 'error');
                        return;
                    }
                }
                
                // Send to PHP API
                const response = await fetch(API_POST_URL, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                Swal.close();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    closeModal();
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: result.message || 'Terjadi kesalahan'
                    });
                }
                
            } catch (error) {
                console.error('Error:', error);
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menghubungi server'
                });
            }
        }

        function isAttendanceType(tipe) {
            const attendanceTypes = ['2100', '2200', '2300', '2400', '2500'];
            return attendanceTypes.includes(tipe);
        }

        // Fungsi untuk mengatur end date berdasarkan tipe
        function handleEndDateByType(tipe) {
            const startDateValue = $('#startdate').val();
            
            if (isAttendanceType(tipe)) {
                // Untuk attendance, end date = start date dan di-lock
                $('#enddate').val(startDateValue);
                $('#enddate').prop('readonly', true);
                $('#enddate').addClass('bg-gray-100'); // Tambah style untuk menunjukkan readonly
            } else {
                // Untuk absence, end date bisa diubah
                $('#enddate').prop('readonly', false);
                $('#enddate').removeClass('bg-gray-100');
                
                // Set minDate end date berdasarkan start date
                if (window.startDatePicker && window.endDatePicker) {
                    const startDate = window.startDatePicker.selectedDates[0];
                    if (startDate) {
                        window.endDatePicker.set('minDate', startDate);
                    }
                }
            }
        }

        // Print function
        function doPrint(encodedData) {
            window.open(`/print/${encodedData}`, '_blank');
        }

        // Foto modal
        function openFotoModal(url) {
            $('#modalFoto').attr('src', url);
            $('#fotoModal').removeClass('hidden').addClass('flex');
        }

        function closeFotoModal() {
            $('#fotoModal').removeClass('flex').addClass('hidden');
        }
    </script>

    <style>
        input[readonly].bg-gray-100 {
            background-color: #f3f4f6;
            cursor: not-allowed;
        }

        input[readonly] {
            cursor: not-allowed;
        }

        /* Custom Flatpickr */
        .flatpickr-calendar {
            font-family: inherit;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        
        .flatpickr-day.selected {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
        
        .flatpickr-day.selected:hover {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        
        .flatpickr-input[readonly] {
            background-color: white;
            cursor: pointer;
        }
        
        /* Memastikan datepicker muncul di atas modal */
        .flatpickr-calendar.open {
            z-index: 9999 !important;
        }
        
        /* Custom styles */
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
        
        #tblabsences {
            width: 100%;
            border-collapse: collapse;
        }
        
        #tblabsences th {
            background-color: #f9fafb;
            font-weight: 600;
        }
        
        #tblabsences tbody tr:hover {
            background-color: #f9fafb;
        }
        
        .bg-yellow-500 { background-color: #f59e0b; }
        .bg-green-500 { background-color: #10b981; }
        .bg-red-500 { background-color: #ef4444; }
        .bg-gray-500 { background-color: #6b7280; }
        
        /* Modal styles */
        #exampleModal.flex {
            display: flex;
        }
        
        #fotoModal.flex {
            display: flex;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>
</x-app-layout>