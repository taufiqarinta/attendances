<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Summary Report per Plant') }}
            </h2>
            <div class="flex space-x-2">
                <button onclick="refreshData()" 
                    class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm border border-gray-200 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 refresh-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </x-slot>

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
                <button onclick="this.parentElement.classList.add('hidden')" class="absolute top-0 right-0 px-3 py-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            <div id="alertError" class="mb-4 hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span id="errorMessage"></span>
                <button onclick="this.parentElement.classList.add('hidden')" class="absolute top-0 right-0 px-3 py-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            <!-- Filter Section -->
            <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Periode</label>
                        <select id="adminSelectPeriode" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option value="">Memuat data...</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button onclick="loadSummaryData()" 
                                class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-md transition duration-200">
                            Tampilkan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Report Table -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4 sm:p-6">
                    
                    <!-- Tambah header row dengan title + tombol export -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider">
                            Data Summary per Plant
                        </h3>
                        <button id="exportBtn" onclick="exportExcel()"
                            class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-md transition duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            </svg>
                            Export Excel
                        </button>
                    </div>
                    
                    <div class="table-container">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="reporttbl">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th onclick="sortTable(0)" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100">
                                            No <span class="sort-icon text-gray-400">⇅</span>
                                        </th>
                                        <th onclick="sortTable(1)" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100">
                                            Plant <span class="sort-icon text-gray-400">⇅</span>
                                        </th>
                                        <th onclick="sortTable(2)" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100">
                                            Telat (%) <span class="sort-icon text-gray-400">⇅</span>
                                        </th>
                                        <th onclick="sortTable(3)" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100">
                                            Mangkir (%) <span class="sort-icon text-gray-400">⇅</span>
                                        </th>
                                        <th onclick="sortTable(4)" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100">
                                            Ijin (%) <span class="sort-icon text-gray-400">⇅</span>
                                        </th>
                                        <th onclick="sortTable(5)" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100">
                                            Sakit (%) <span class="sort-icon text-gray-400">⇅</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="tableBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-8">
                                            <div class="flex flex-col items-center justify-center text-gray-400">
                                                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span>Belum ada data. Silakan pilih periode.</span>
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

    <!-- Modal Chart -->
    <div id="chartModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background-color: rgba(17,24,39,0.75);">
        <div style="background:white; border-radius:0.5rem; box-shadow:0 20px 60px rgba(0,0,0,0.3); max-width:600px; width:100%; max-height:90vh; overflow-y:auto;">
            
            <!-- Header -->
            <div style="display:flex; justify-content:space-between; align-items:center; padding:1rem; border-bottom:1px solid #e5e7eb;">
                <h3 style="font-size:1rem; font-weight:600; color:#111827;">
                    <i class="ti-bar-chart"></i> 📊 View Chart
                </h3>
                <button onclick="closeChartModal()" style="color:#9ca3af; background:none; border:none; cursor:pointer; font-size:1.5rem; line-height:1;">
                    &times;
                </button>
            </div>

            <!-- Chart -->
            <div style="padding:1rem;">
                <div id="plantChart" style="width:100%; height:400px;"></div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script> -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<!-- <script>
    const API_BASE_URL = 'https://web.kobin.co.id/api/hris/summary/get_summary.php';
    let summaryTable = null;
    let plantChart = null;
    let isFetching = false;
    let currentData = [];

    // DOM Elements
    const loadingOverlay = document.getElementById('loadingOverlay');
    const tableBody = document.getElementById('tableBody');
    const periodeSelect = document.getElementById('adminSelectPeriode');
    const alertSuccess = document.getElementById('alertSuccess');
    const alertError = document.getElementById('alertError');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

    let sortDir = {};

    function sortTable(colIndex) {
        const tbody = document.getElementById('tableBody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        sortDir[colIndex] = sortDir[colIndex] === 'asc' ? 'desc' : 'asc';
        const dir = sortDir[colIndex];

        rows.sort((a, b) => {
            const aText = a.cells[colIndex]?.textContent.trim() ?? '';
            const bText = b.cells[colIndex]?.textContent.trim() ?? '';

            const aNum = parseFloat(aText);
            const bNum = parseFloat(bText);
            const isNum = !isNaN(aNum) && !isNaN(bNum);

            let cmp = isNum ? aNum - bNum : aText.localeCompare(bText, 'id');
            return dir === 'asc' ? cmp : -cmp;
        });

        // Update semua icon
        document.querySelectorAll('.sort-icon').forEach((el, i) => {
            el.textContent = i === colIndex ? (dir === 'asc' ? '↑' : '↓') : '⇅';
        });

        rows.forEach(row => tbody.appendChild(row));
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

    // Render Table
    function renderTable(data) {
        // Destroy DataTable dulu jika ada
        if ($.fn.DataTable.isDataTable('#reporttbl')) {
            $('#reporttbl').DataTable().destroy();
            summaryTable = null;
        }

        if (!data || data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <span>Tidak ada data ditemukan</span>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        // Set HTML dulu, BARU init DataTable
        let html = '';
        data.forEach((item, index) => {
            html += `
                <tr class="hover:bg-gray-50 cursor-pointer" 
                    data-plant="${item.Plant}" 
                    data-nama="${item.Nama}" 
                    data-telat="${item.telat}" 
                    data-mangkir="${item.mangkir}" 
                    data-ijin="${item.ijin}" 
                    data-sakit="${item.sakit}">
                    <td class="px-4 py-3 text-center text-sm">${index + 1}</td>
                    <td class="px-4 py-3 text-center text-sm font-medium">${item.Nama || item.Plant}</td>
                    <td class="px-4 py-3 text-center text-sm">${parseFloat(item.telat).toFixed(2)}</td>
                    <td class="px-4 py-3 text-center text-sm">${parseFloat(item.mangkir).toFixed(2)}</td>
                    <td class="px-4 py-3 text-center text-sm">${parseFloat(item.ijin).toFixed(2)}</td>
                    <td class="px-4 py-3 text-center text-sm">${parseFloat(item.sakit).toFixed(2)}</td>
                </tr>
            `;
        });

        // Set innerHTML langsung ke tbody
        $('#tableBody').html(html);

        // Init DataTable SETELAH HTML ada di DOM
        summaryTable = $('#reporttbl').DataTable({
            dom: 'frt',
            paging: false,
            searching: false, // opsional, hide search box
            info: false,      // opsional, hide "Showing X entries"
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    className: 'btn btn-sm bg-green-500 text-white hover:bg-green-600',
                    filename: 'Rekap_Summary_Report_Plant'
                }
            ]
        });
    }

    function exportExcel() {
        const table = document.getElementById('reporttbl');
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.table_to_sheet(table);
        XLSX.utils.book_append_sheet(wb, ws, 'Summary');
        XLSX.writeFile(wb, 'Rekap_Summary_Report_Plant.xlsx');
    }

    // Load Summary Data - PERBAIKAN: Clear cache sebelum fetch
    async function loadSummaryData(periode = null) {
        if (isFetching) return;
        
        const selectedPeriode = periode || periodeSelect.value;
        if (!selectedPeriode || selectedPeriode === '') {
            showAlert('error', 'Silakan pilih periode terlebih dahulu');
            return;
        }
        
        try {
            isFetching = true;
            showLoading();
            
            // Tambahkan timestamp untuk mencegah cache
            const url = `${API_BASE_URL}?periode=${selectedPeriode}&_=${Date.now()}`;
            console.log('Fetching:', url);
            
            const response = await fetch(url);
            const result = await response.json();
            
            console.log('Summary Response for periode', selectedPeriode, ':', result);
            
            if (result.success) {
                currentData = result.data || [];
                renderTable(currentData);
                
                // Update judul atau info tambahan
                const periodeDisplay = selectedPeriode.substring(0,4) + '-' + selectedPeriode.substring(4,6);
                showAlert('success', `Berhasil memuat ${currentData.length} data plant untuk periode ${periodeDisplay}`);
            } else {
                throw new Error(result.message || 'Gagal mengambil data');
            }
        } catch (error) {
            console.error('Error loading summary:', error);
            showAlert('error', 'Gagal memuat data: ' + error.message);
            renderTable([]);
        } finally {
            isFetching = false;
            hideLoading();
        }
    }

    // Event listener untuk periode change - PASTIKAN INI BERJALAN
    $(periodeSelect).on('change', function() {
        const selectedPeriode = $(this).val();
        console.log('Periode changed to:', selectedPeriode);
        if (selectedPeriode && selectedPeriode !== '') {
            loadSummaryData(selectedPeriode);
        }
    });

    // Load List Periode - PERBAIKAN: Format periode dengan benar
    async function loadListPeriode() {
        try {
            showLoading();
            const response = await fetch(`${API_BASE_URL}?action=list_periode&_=${Date.now()}`);
            const result = await response.json();
            
            console.log('List Periode Response:', result);
            
            if (result.success && result.data && result.data.length > 0) {
                let options = '';
                let firstPeriode = null;
                
                result.data.forEach(item => {
                    // Cek berbagai kemungkinan nama kolom periode
                    let periodeValue = item.Periode || item.periode || item.CGroup;
                    let nameValue = item.Name || item.name || item.CGroup;
                    
                    if (periodeValue) {
                        options += `<option value="${periodeValue}">${nameValue || periodeValue}</option>`;
                        if (!firstPeriode) firstPeriode = periodeValue;
                    }
                });
                
                periodeSelect.innerHTML = options;
                
                // Load data untuk periode pertama
                if (firstPeriode) {
                    await loadSummaryData(firstPeriode);
                }
            } else {
                periodeSelect.innerHTML = '<option value="">Tidak ada periode</option>';
                showAlert('error', result.message || 'Gagal mengambil data periode');
            }
        } catch (error) {
            console.error('Error loading periode:', error);
            periodeSelect.innerHTML = '<option value="">Error loading data</option>';
            showAlert('error', 'Gagal memuat daftar periode: ' + error.message);
        } finally {
            hideLoading();
        }
    }

    // Refresh Data
    async function refreshData() {
        const refreshIcon = document.querySelector('.refresh-icon');
        refreshIcon.classList.remove('hidden');
        await loadListPeriode();
        setTimeout(() => {
            refreshIcon.classList.add('hidden');
        }, 500);
    }

    // Initialize Chart.js
    function initChart() {
        const ctx = document.getElementById('plantChart').getContext('2d');
        plantChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Telat', 'Mangkir', 'Ijin', 'Sakit'],
                datasets: [{
                    label: 'Persentase (%)',
                    data: [0, 0, 0, 0],
                    backgroundColor: [
                        '#F24141',
                        '#F2A541',
                        '#F3CA40',
                        '#40F99B'
                    ],
                    borderColor: [
                        '#d63030',
                        '#d48d30',
                        '#d4b530',
                        '#30d480'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Persentase (%)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Kategori'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + '%';
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    }

    // Update Chart
    function updateChart(plantData) {
        if (plantChart) {
            plantChart.data.datasets[0].data = [
                plantData.telat,
                plantData.mangkir,
                plantData.ijin,
                plantData.sakit
            ];
            plantChart.data.datasets[0].label = plantData.Nama;
            plantChart.update();
        }
    }

    // Show Chart Modal
    function showChartModal(plantData) {
        updateChart(plantData);
        document.getElementById('chartModal').classList.remove('hidden');
    }

    // Close Chart Modal
    function closeChartModal() {
        document.getElementById('chartModal').classList.add('hidden');
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeChartModal();
        }
    });

    // Close modal when clicking outside
    document.getElementById('chartModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeChartModal();
        }
    });

    // Handle row click for chart
    document.addEventListener('click', function(event) {
        const row = event.target.closest('#tableBody tr');
        if (row && row.cells && row.cells.length > 0) {
            const plantData = {
                Plant: row.dataset.plant,
                Nama: row.dataset.nama,
                telat: parseFloat(row.dataset.telat) || 0,
                mangkir: parseFloat(row.dataset.mangkir) || 0,
                ijin: parseFloat(row.dataset.ijin) || 0,
                sakit: parseFloat(row.dataset.sakit) || 0
            };
            showChartModal(plantData);
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing...');
        initChart();
        loadListPeriode();
    });
</script> -->

<script>
    const API_BASE_URL = 'https://web.kobin.co.id/api/hris/summary/get_summary.php';
    let summaryTable = null;
    let plantChart = null;
    let isFetching = false;
    let currentData = [];

    // DOM Elements
    const loadingOverlay = document.getElementById('loadingOverlay');
    const tableBody = document.getElementById('tableBody');
    const periodeSelect = document.getElementById('adminSelectPeriode');
    const alertSuccess = document.getElementById('alertSuccess');
    const alertError = document.getElementById('alertError');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

    let sortDir = {};

    function sortTable(colIndex) {
        const tbody = document.getElementById('tableBody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        sortDir[colIndex] = sortDir[colIndex] === 'asc' ? 'desc' : 'asc';
        const dir = sortDir[colIndex];

        rows.sort((a, b) => {
            const aText = a.cells[colIndex]?.textContent.trim() ?? '';
            const bText = b.cells[colIndex]?.textContent.trim() ?? '';

            const aNum = parseFloat(aText);
            const bNum = parseFloat(bText);
            const isNum = !isNaN(aNum) && !isNaN(bNum);

            let cmp = isNum ? aNum - bNum : aText.localeCompare(bText, 'id');
            return dir === 'asc' ? cmp : -cmp;
        });

        document.querySelectorAll('.sort-icon').forEach((el, i) => {
            el.textContent = i === colIndex ? (dir === 'asc' ? '↑' : '↓') : '⇅';
        });

        rows.forEach(row => tbody.appendChild(row));
    }

    function showLoading() {
        loadingOverlay.classList.remove('hidden');
    }

    function hideLoading() {
        loadingOverlay.classList.add('hidden');
    }

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

    function renderTable(data) {
        if ($.fn.DataTable.isDataTable('#reporttbl')) {
            $('#reporttbl').DataTable().destroy();
            summaryTable = null;
        }

        if (!data || data.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <span>Tidak ada data ditemukan</span>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        data.forEach((item, index) => {
            html += `
                <tr class="hover:bg-gray-50 cursor-pointer" 
                    data-plant="${item.Plant}" 
                    data-nama="${item.Nama || item.Plant}" 
                    data-telat="${parseFloat(item.telat).toFixed(2)}" 
                    data-mangkir="${parseFloat(item.mangkir).toFixed(2)}" 
                    data-ijin="${parseFloat(item.ijin).toFixed(2)}" 
                    data-sakit="${parseFloat(item.sakit).toFixed(2)}">
                    <td class="px-4 py-3 text-center text-sm">${index + 1}</td>
                    <td class="px-4 py-3 text-center text-sm font-medium">${item.Nama || item.Plant}</td>
                    <td class="px-4 py-3 text-center text-sm">${parseFloat(item.telat).toFixed(2)}</td>
                    <td class="px-4 py-3 text-center text-sm">${parseFloat(item.mangkir).toFixed(2)}</td>
                    <td class="px-4 py-3 text-center text-sm">${parseFloat(item.ijin).toFixed(2)}</td>
                    <td class="px-4 py-3 text-center text-sm">${parseFloat(item.sakit).toFixed(2)}</td>
                 </tr>
            `;
        });

        $('#tableBody').html(html);

        summaryTable = $('#reporttbl').DataTable({
            dom: 'frt',
            paging: false,
            searching: false,
            info: false,
        });
    }

    function exportExcel() {
        const table = document.getElementById('reporttbl');
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.table_to_sheet(table);
        XLSX.utils.book_append_sheet(wb, ws, 'Summary');
        XLSX.writeFile(wb, 'Rekap_Summary_Report_Plant.xlsx');
    }

    async function loadSummaryData(periode = null) {
        if (isFetching) return;
        
        const selectedPeriode = periode || periodeSelect.value;
        if (!selectedPeriode || selectedPeriode === '') {
            showAlert('error', 'Silakan pilih periode terlebih dahulu');
            return;
        }
        
        try {
            isFetching = true;
            showLoading();
            
            const url = `${API_BASE_URL}?periode=${selectedPeriode}&_=${Date.now()}`;
            console.log('Fetching:', url);
            
            const response = await fetch(url);
            const result = await response.json();
            
            console.log('Summary Response:', result);
            
            if (result.success) {
                currentData = result.data || [];
                renderTable(currentData);
                const periodeDisplay = selectedPeriode.substring(0,4) + '-' + selectedPeriode.substring(4,6);
                showAlert('success', `Berhasil memuat ${currentData.length} data plant untuk periode ${periodeDisplay}`);
            } else {
                throw new Error(result.message || 'Gagal mengambil data');
            }
        } catch (error) {
            console.error('Error loading summary:', error);
            showAlert('error', 'Gagal memuat data: ' + error.message);
            renderTable([]);
        } finally {
            isFetching = false;
            hideLoading();
        }
    }

    $(periodeSelect).on('change', function() {
        const selectedPeriode = $(this).val();
        console.log('Periode changed to:', selectedPeriode);
        if (selectedPeriode && selectedPeriode !== '') {
            loadSummaryData(selectedPeriode);
        }
    });

    async function loadListPeriode() {
        try {
            showLoading();
            const response = await fetch(`${API_BASE_URL}?action=list_periode&_=${Date.now()}`);
            const result = await response.json();
            
            console.log('List Periode Response:', result);
            
            if (result.success && result.data && result.data.length > 0) {
                let options = '';
                let firstPeriode = null;
                
                result.data.forEach(item => {
                    let periodeValue = item.Periode || item.periode || item.CGroup;
                    let nameValue = item.Name || item.name || item.CGroup;
                    
                    if (periodeValue) {
                        options += `<option value="${periodeValue}">${nameValue || periodeValue}</option>`;
                        if (!firstPeriode) firstPeriode = periodeValue;
                    }
                });
                
                periodeSelect.innerHTML = options;
                
                if (firstPeriode) {
                    await loadSummaryData(firstPeriode);
                }
            } else {
                periodeSelect.innerHTML = '<option value="">Tidak ada periode</option>';
                showAlert('error', result.message || 'Gagal mengambil data periode');
            }
        } catch (error) {
            console.error('Error loading periode:', error);
            periodeSelect.innerHTML = '<option value="">Error loading data</option>';
            showAlert('error', 'Gagal memuat daftar periode: ' + error.message);
        } finally {
            hideLoading();
        }
    }

    async function refreshData() {
        const refreshIcon = document.querySelector('.refresh-icon');
        refreshIcon.style.display = 'inline-block';
        await loadListPeriode();
        setTimeout(() => {
            refreshIcon.style.display = 'none';
        }, 500);
    }

    // Initialize Highcharts (bukan Chart.js)
    function initHighcharts() {
        plantChart = Highcharts.chart('plantChart', {
            chart: {
                type: 'column',
                options3d: {
                    enabled: true,
                    alpha: 10,
                    beta: 0,
                    depth: 30,
                    viewDistance: 25
                },
                backgroundColor: '#ffffff'
            },
            // Gunakan satu warna untuk semua bar
            colors: ['#F24141'],
            title: {
                text: 'Summary Data',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold'
                }
            },
            subtitle: {
                text: 'Klik pada baris tabel untuk melihat detail plant',
                style: {
                    fontSize: '12px',
                    color: '#666666'
                }
            },
            xAxis: {
                categories: ['Telat', 'Mangkir', 'Ijin', 'Sakit'],
                title: {
                    text: 'Kategori'
                },
                labels: {
                    style: {
                        fontSize: '11px'
                    }
                }
            },
            yAxis: {
                title: {
                    text: 'Persentase (%)'
                },
                labels: {
                    format: '{value}%'
                },
                min: 0
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br/>',
                pointFormat: 'Persentase: <b>{point.y:.2f}%</b>'
            },
            plotOptions: {
                column: {
                    depth: 25,
                    colorByPoint: false, // Semua bar warna sama
                    dataLabels: {
                        enabled: true,
                        format: '{y:.2f}%',
                        style: {
                            fontWeight: 'bold',
                            fontSize: '11px',
                            color: '#333333'
                        }
                    }
                }
            },
            series: [{
                name: 'Persentase',
                data: [0, 0, 0, 0],
                color: '#F24141'
            }]
        });
    }

    // Update Chart dengan data plant
    function updateHighcharts(plantData) {
        if (plantChart) {
            plantChart.series[0].update({
                data: [
                    parseFloat(plantData.telat) || 0,
                    parseFloat(plantData.mangkir) || 0,
                    parseFloat(plantData.ijin) || 0,
                    parseFloat(plantData.sakit) || 0
                ],
                name: plantData.Nama || plantData.Plant
            });
            
            plantChart.update({
                subtitle: {
                    text: 'Plant: ' + (plantData.Nama || plantData.Plant)
                }
            });
        }
    }

    // Show Chart Modal
    function showChartModal(plantData) {
        updateHighcharts(plantData);
        document.getElementById('chartModal').classList.remove('hidden');
    }

    // Close Chart Modal
    function closeChartModal() {
        document.getElementById('chartModal').classList.add('hidden');
    }

    // Event handlers
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeChartModal();
        }
    });

    document.getElementById('chartModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeChartModal();
        }
    });

    // Handle row click untuk chart
    document.addEventListener('click', function(event) {
        const row = event.target.closest('#tableBody tr');
        if (row && row.cells && row.cells.length > 0) {
            const plantData = {
                Plant: row.dataset.plant,
                Nama: row.dataset.nama,
                telat: parseFloat(row.dataset.telat) || 0,
                mangkir: parseFloat(row.dataset.mangkir) || 0,
                ijin: parseFloat(row.dataset.ijin) || 0,
                sakit: parseFloat(row.dataset.sakit) || 0
            };
            showChartModal(plantData);
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing Highcharts...');
        initHighcharts();
        loadListPeriode();
    });
</script>

<style>
    .table-container {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .overflow-x-auto {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
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
    
    /* .hidden {
        display: none;
    } */
    
    /* DataTables customization */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
    }
    
    .dataTables_wrapper .dt-buttons {
        margin-bottom: 1rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
    }
    
    #reporttbl tbody tr {
        cursor: pointer;
    }
    
    #reporttbl tbody tr:hover {
        background-color: #f3f4f6;
    }
</style>