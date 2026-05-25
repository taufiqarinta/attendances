<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Summary Report per Employee') }}
            </h2>
            <div class="flex gap-2">
                <button onclick="exportToExcel()"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                    Export Excel
                </button>
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Loading Overlay --}}
            <div id="loadingOverlay"
                class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg p-6 flex flex-col items-center shadow-xl">
                    <svg class="animate-spin h-10 w-10 text-red-500 mb-3" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-gray-700 font-medium">Memuat data...</span>
                </div>
            </div>

            {{-- Alert Error --}}
            <div id="alertError"
                class="mb-4 hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span id="errorMessage"></span>
                <button onclick="this.parentElement.classList.add('hidden')" class="absolute top-0 right-0 px-3 py-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            {{-- Alert Success --}}
            <div id="alertSuccess"
                class="mb-4 hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <span id="successMessage"></span>
                <button onclick="this.parentElement.classList.add('hidden')" class="absolute top-0 right-0 px-3 py-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            {{-- Filter Card --}}
            <div class="filter-card">
                <div class="filter-grid">
                    {{-- Location --}}
                    <div class="field">
                        <label>Location</label>
                        <select id="plantSelect">
                            <option value="1000">HEAD OFFICE</option>
                            <option value="1001">PABRIK NGORO</option>
                            <option value="1002">CAKK</option>
                            <option value="1003">PK - 2</option>
                            <option value="1004">MISS</option>
                            <option value="1005">KAISAR PABRIK</option>
                        </select>
                        <div class="field-spacer"></div>
                    </div>

                    {{-- Periode (Dropdown) --}}
                    <div class="field">
                        <label>Periode</label>
                        <select id="periodeSelect" class="w-full rounded-md border-gray-300">
                            <option value="">Memuat data...</option>
                        </select>
                        <div class="field-spacer"></div>
                    </div>

                    {{-- Search --}}
                    <div class="field">
                        <label>Cari Nama / NIK / Dept</label>
                        <input type="text" id="searchInput" placeholder="Ketik untuk mencari...">
                        <p class="search-hint" id="searchResultCount"></p>
                    </div>

                    {{-- Tombol --}}
                    <div class="field">
                        <label>&nbsp;</label>
                        <button class="btn-tampilkan" onclick="loadReport()">Tampilkan</button>
                        <div class="field-spacer"></div>
                    </div>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm" id="reportTable">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r" rowspan="2">NIK</th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-r" rowspan="2">Nama</th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-r" rowspan="2">Dept</th>
                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r" rowspan="2">HK</th>
                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r" colspan="4">Attendances</th>
                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider" colspan="6">Absences</th>
                                </tr>
                                <tr class="bg-gray-50">
                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-500 border-r">L1</th>
                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-500 border-r">L2</th>
                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-500 border-r">L3</th>
                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-500 border-r">P</th>
                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-500 border-r">M</th>
                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-500 border-r">I</th>
                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-500 border-r">DIS</th>
                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-500 border-r">C</th>
                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-500 border-r">S</th>
                                    <th class="px-2 py-2 text-center text-xs font-semibold text-gray-500">D</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="14" class="text-center py-12 text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <span>Pilih periode dan klik Tampilkan</span>
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

    {{-- Include SheetJS for Excel export --}}
    <script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // ==================== KONFIGURASI ====================
        const API_BASE = 'https://web.kobin.co.id/api/hris/report/get_report.php';
        const SESSION_NIK = '{{ session("nik") }}';
        const SESSION_USERNAME = '{{ session("username") }}';
        const SESSION_PLANT = '{{ session("plant") ?? "1000" }}';
        
        let currentData = [];      // Data asli dari API
        let filteredData = [];      // Data setelah difilter search
        let periodeAktif = null;
        let listPeriode = [];
        
        console.log('=== REPORT INIT DEBUG ===');
        console.log('Session:', { nik: SESSION_NIK, plant: SESSION_PLANT });
        
        // ==================== HELPER FUNCTIONS ====================
        function showLoading() { 
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.classList.remove('hidden'); 
        }
        
        function hideLoading() { 
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.classList.add('hidden'); 
        }
        
        function showError(msg) {
            console.error('ERROR:', msg);
            const el = document.getElementById('alertError');
            if (el) {
                document.getElementById('errorMessage').textContent = msg;
                el.classList.remove('hidden');
                setTimeout(() => el.classList.add('hidden'), 5000);
            }
        }

        function showSuccess(msg) {
            const el = document.getElementById('alertSuccess');
            if (el) {
                document.getElementById('successMessage').textContent = msg;
                el.classList.remove('hidden');
                setTimeout(() => el.classList.add('hidden'), 3000);
            }
        }
        
        async function fetchAPI(action, params = {}) {
            const url = new URL(API_BASE);
            url.searchParams.append('action', action);
            Object.keys(params).forEach(key => {
                if (params[key] !== undefined && params[key] !== null && params[key] !== '') {
                    url.searchParams.append(key, params[key]);
                }
            });
            
            console.log(`Fetching ${action}:`, url.toString());
            
            try {
                const response = await fetch(url);
                const result = await response.json();
                console.log(`Response ${action}:`, result);
                
                if (!result.success) {
                    throw new Error(result.message || 'Gagal mengambil data');
                }
                return result.data;
            } catch (err) {
                console.error(`Fetch error ${action}:`, err);
                throw err;
            }
        }
        
        // Format periode untuk API (YYYYMM -> YYYY-MM-01)
        function formatPeriodeForAPI(periode) {
            if (!periode) return '';
            if (periode.match(/^\d{6}$/)) {
                return periode.substring(0, 4) + '-' + periode.substring(4, 6) + '-01';
            }
            return periode;
        }
        
        // ==================== SEARCH FUNCTION ====================
        function filterData(searchTerm) {
            if (!searchTerm || searchTerm.trim() === '') {
                filteredData = [...currentData];
                document.getElementById('searchResultCount').textContent = `Menampilkan ${filteredData.length} data`;
                renderTable(filteredData);
                return;
            }
            
            const term = searchTerm.toLowerCase().trim();
            filteredData = currentData.filter(item => {
                return (item.nik && item.nik.toLowerCase().includes(term)) ||
                       (item.firstname && item.firstname.toLowerCase().includes(term)) ||
                       (item.dept && item.dept.toLowerCase().includes(term));
            });
            
            document.getElementById('searchResultCount').textContent = 
                `Menampilkan ${filteredData.length} dari ${currentData.length} data`;
            
            renderTable(filteredData);
        }
        
        // ==================== LOAD LIST PERIODE ====================
        async function loadListPeriode() {
            try {
                const data = await fetchAPI('getListPeriode');
                listPeriode = data;
                console.log('List periode loaded:', listPeriode.length);
                
                const periodeSelect = document.getElementById('periodeSelect');
                if (periodeSelect && data && data.length > 0) {
                    let options = '<option value="">Pilih Periode</option>';
                    let firstPeriode = null;
                    
                    data.forEach(item => {
                        let periodeValue = item.Periode || item.periode || item.CGroup;
                        let nameValue = item.Name || item.name || item.CGroup;
                        
                        if (periodeValue) {
                            options += `<option value="${periodeValue}">${nameValue || periodeValue}</option>`;
                            if (!firstPeriode) firstPeriode = periodeValue;
                        }
                    });
                    
                    periodeSelect.innerHTML = options;
                    
                    // Set periode aktif jika ada
                    if (periodeAktif && periodeAktif !== '') {
                        periodeSelect.value = periodeAktif;
                    } else if (firstPeriode) {
                        periodeSelect.value = firstPeriode;
                    }
                }
                
                return listPeriode;
            } catch (err) {
                console.error('Gagal load list periode:', err);
                const periodeSelect = document.getElementById('periodeSelect');
                if (periodeSelect) {
                    periodeSelect.innerHTML = '<option value="">Error loading data</option>';
                }
                return [];
            }
        }
        
        // ==================== LOAD PERIODE AKTIF ====================
        async function loadPeriodeAktif() {
            try {
                const data = await fetchAPI('getPeriodeNow', { 
                    comp: '0001', 
                    plant: SESSION_PLANT 
                });
                
                console.log('Response getPeriodeNow:', data);
                
                if (data && data.length > 0 && data[0].Periode) {
                    periodeAktif = data[0].Periode;
                    console.log('Periode aktif:', periodeAktif);
                    return periodeAktif;
                }
                return null;
            } catch (err) {
                console.error('Gagal load periode aktif:', err);
                return null;
            }
        }
        
        // ==================== LOAD REPORT ====================
        async function loadReport() {
            const periodeSelect = document.getElementById('periodeSelect');
            if (!periodeSelect || !periodeSelect.value) {
                showError('Silakan pilih periode terlebih dahulu');
                return;
            }
            
            const periodeValue = periodeSelect.value;
            const periodeForAPI = formatPeriodeForAPI(periodeValue);
            const plant = document.getElementById('plantSelect')?.value || '1000';
            const comp = '0001';
            
            console.log('Loading report for periode:', periodeValue, '-> API:', periodeForAPI);
            
            showLoading();
            try {
                const data = await fetchAPI('getReport', { 
                    periode: periodeForAPI, 
                    plant: plant,
                    comp: comp
                });
                
                console.log('Report data received:', data.length);
                
                currentData = data;
                filteredData = [...data];
                
                // Reset search input
                const searchInput = document.getElementById('searchInput');
                if (searchInput) searchInput.value = '';
                document.getElementById('searchResultCount').textContent = `Menampilkan ${data.length} data`;
                
                if (data.length === 0) {
                    renderEmpty('Tidak ada data untuk periode ini');
                    showError('Tidak ada data untuk periode ' + periodeValue);
                } else {
                    renderTable(data);
                    showSuccess(`Berhasil memuat ${data.length} data karyawan`);
                }
            } catch (err) {
                console.error('Error loading report:', err);
                showError('Gagal memuat data: ' + err.message);
                renderEmpty();
            } finally {
                hideLoading();
            }
        }
        
        // ==================== RENDER TABLE ====================
        function renderTable(data) {
            const tbody = document.getElementById('tableBody');
            
            if (!data || data.length === 0) {
                renderEmpty('Tidak ada data yang cocok dengan pencarian');
                return;
            }
            
            const totals = calculateTotals(data);
            const totalAttendances = totals.l1 + totals.l2 + totals.l3 + totals.p;
            const totalAbsences = totals.m + totals.i + totals.dis + totals.c + totals.s + totals.d;
            let html = '';
            let currentDept = '';
            let deptCount = 0;
            
            data.forEach((item, index) => {
                const deptName = item.dept || '-';
                const deptDisplay = deptName === 'Manufacturing Operational Controller (MOC) & Corporate Project Admin' 
                    ? 'MOC' 
                    : deptName;
                
                if (deptName !== currentDept) {
                    if (currentDept !== '') {
                        html += `
                            <tr class="bg-gray-100 font-bold">
                                <td colspan="14" class="px-3 py-2 text-sm text-gray-700">
                                    Total ${currentDept}: ${deptCount} karyawan
                                </td>
                            </tr>
                        `;
                        deptCount = 0;
                    }
                    currentDept = deptName;
                    
                    html += `
                        <tr class="bg-blue-50">
                            <td colspan="14" class="px-3 py-2 text-sm font-semibold text-blue-800">
                                📁 ${deptDisplay}
                            </td>
                        </tr>
                    `;
                }
                deptCount++;
                
                html += `
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 text-center">${escapeHtml(item.nik || '-')}</td>
                        <td class="px-3 py-2">${escapeHtml(item.firstname || '-')}</td>
                        <td class="px-3 py-2">${escapeHtml(deptDisplay)}</td>
                        <td class="px-3 py-2 text-center">${numberFormat(item.hk)}</td>
                        <td class="px-3 py-2 text-center">${numberFormat(item.l1)}</td>
                        <td class="px-3 py-2 text-center">${numberFormat(item.l2)}</td>
                        <td class="px-3 py-2 text-center">${numberFormat(item.l3)}</td>
                        <td class="px-3 py-2 text-center">${numberFormat(item.p)}</td>
                        <td class="px-3 py-2 text-center">${numberFormat(item.m)}</td>
                        <td class="px-3 py-2 text-center">${numberFormat(item.i)}</td>
                        <td class="px-3 py-2 text-center">${numberFormat(item.dis)}</td>
                        <td class="px-3 py-2 text-center">${numberFormat(item.c)}</td>
                        <td class="px-3 py-2 text-center">${numberFormat(item.s)}</td>
                        <td class="px-3 py-2 text-center">${numberFormat(item.d)}</td>
                    </tr>
                `;
            });
            
            if (currentDept !== '') {
                html += `
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="14" class="px-3 py-2 text-sm text-gray-700">
                            Total ${currentDept}: ${deptCount} karyawan
                        </td>
                    </tr>
                `;
            }

            html += `
                <tr class="bg-blue-50 font-bold border-t-2 border-blue-200">
                    <td colspan="3" class="px-3 py-3 text-sm text-blue-700">
                        Total seluruh karyawan: ${totals.employees} | Attendances: ${numberFormat(totalAttendances)} | Absences: ${numberFormat(totalAbsences)}
                    </td>
                    <td class="px-3 py-3 text-center text-sm text-blue-700">${numberFormat(totals.hk)}</td>
                    <td class="px-3 py-3 text-center text-sm text-blue-700">${numberFormat(totals.l1)}</td>
                    <td class="px-3 py-3 text-center text-sm text-blue-700">${numberFormat(totals.l2)}</td>
                    <td class="px-3 py-3 text-center text-sm text-blue-700">${numberFormat(totals.l3)}</td>
                    <td class="px-3 py-3 text-center text-sm text-blue-700">${numberFormat(totals.p)}</td>
                    <td class="px-3 py-3 text-center text-sm text-blue-700">${numberFormat(totals.m)}</td>
                    <td class="px-3 py-3 text-center text-sm text-blue-700">${numberFormat(totals.i)}</td>
                    <td class="px-3 py-3 text-center text-sm text-blue-700">${numberFormat(totals.dis)}</td>
                    <td class="px-3 py-3 text-center text-sm text-blue-700">${numberFormat(totals.c)}</td>
                    <td class="px-3 py-3 text-center text-sm text-blue-700">${numberFormat(totals.s)}</td>
                    <td class="px-3 py-3 text-center text-sm text-blue-700">${numberFormat(totals.d)}</td>
                </tr>
            `;
            
            tbody.innerHTML = html;
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function renderEmpty(msg = 'Tidak ada data') {
            document.getElementById('tableBody').innerHTML = `
                <tr>
                    <td colspan="14" class="text-center py-12 text-gray-400">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>${msg}</span>
                        </div>
                    </td>
                </tr>
            `;
        }
        
        function numberFormat(value) {
            if (value === undefined || value === null) return '0';
            return new Intl.NumberFormat('id-ID').format(value);
        }

        function toNumber(value) {
            if (value === undefined || value === null || value === '') return 0;
            const numeric = parseFloat(String(value).replace(/,/g, '').trim());
            return Number.isFinite(numeric) ? numeric : 0;
        }

        function calculateTotals(data) {
            return data.reduce((totals, item) => {
                totals.hk += toNumber(item.hk);
                totals.l1 += toNumber(item.l1);
                totals.l2 += toNumber(item.l2);
                totals.l3 += toNumber(item.l3);
                totals.p += toNumber(item.p);
                totals.m += toNumber(item.m);
                totals.i += toNumber(item.i);
                totals.dis += toNumber(item.dis);
                totals.c += toNumber(item.c);
                totals.s += toNumber(item.s);
                totals.d += toNumber(item.d);
                return totals;
            }, {
                employees: data.length,
                hk: 0,
                l1: 0,
                l2: 0,
                l3: 0,
                p: 0,
                m: 0,
                i: 0,
                dis: 0,
                c: 0,
                s: 0,
                d: 0,
            });
        }
        
        // ==================== EXPORT TO EXCEL ====================
        function exportToExcel() {
            // Gunakan filteredData (data hasil search) untuk export
            const dataToExport = filteredData.length > 0 ? filteredData : currentData;
            
            if (!dataToExport || dataToExport.length === 0) {
                showError('Tidak ada data untuk diexport');
                return;
            }
            
            // Prepare data for Excel
            const excelData = dataToExport.map(item => ({
                'NIK': item.nik || '',
                'Nama': item.firstname || '',
                'Dept': (item.dept === 'Manufacturing Operational Controller (MOC) & Corporate Project Admin' ? 'MOC' : item.dept) || '',
                'HK': item.hk || 0,
                'L1': item.l1 || 0,
                'L2': item.l2 || 0,
                'L3': item.l3 || 0,
                'P': item.p || 0,
                'M': item.m || 0,
                'I': item.i || 0,
                'DIS': item.dis || 0,
                'C': item.c || 0,
                'S': item.s || 0,
                'D': item.d || 0
            }));
            
            // Create worksheet
            const ws = XLSX.utils.json_to_sheet(excelData);
            
            // Set column widths
            const colWidths = [
                {wch: 12}, // NIK
                {wch: 25}, // Nama
                {wch: 35}, // Dept
                {wch: 8},  // HK
                {wch: 8},  // L1
                {wch: 8},  // L2
                {wch: 8},  // L3
                {wch: 8},  // P
                {wch: 8},  // M
                {wch: 8},  // I
                {wch: 8},  // DIS
                {wch: 8},  // C
                {wch: 8},  // S
                {wch: 8}   // D
            ];
            ws['!cols'] = colWidths;
            
            // Create workbook
            const wb = XLSX.utils.book_new();
            
            const periode = document.getElementById('periodeSelect')?.options[document.getElementById('periodeSelect')?.selectedIndex]?.text || 'report';
            const searchTerm = document.getElementById('searchInput')?.value || '';
            
            function sanitizeSheetName(name) {
                if (!name) return 'Report';
                return name
                    .replace(/[/\\?*[\]:]/g, '_')
                    .substring(0, 31);
            }
            
            let sheetName = 'Summary Report';
            if (searchTerm) {
                const cleanSearch = sanitizeSheetName(searchTerm);
                sheetName = `Search_${cleanSearch}`;
            } else if (periode) {
                const cleanPeriode = periode.replace(/\//g, '-');
                sheetName = `Report_${cleanPeriode}`;
            }
            
            sheetName = sanitizeSheetName(sheetName);
            XLSX.utils.book_append_sheet(wb, ws, sheetName);
            
            function sanitizeFilename(name) {
                if (!name) return 'Summary_Report';
                return name
                    .replace(/[/\\?*[\]:]/g, '_')
                    .replace(/\//g, '-');
            }
            
            const fileName = searchTerm 
                ? `Summary_Report_${periode.replace(/\//g, '-')}_search_${sanitizeFilename(searchTerm)}.xlsx`
                : `Summary_Report_${periode.replace(/\//g, '-')}.xlsx`;
            
            XLSX.writeFile(wb, fileName);
            showSuccess(`Berhasil mengeksport ${dataToExport.length} data ke Excel`);
        }
        
        // ==================== REFRESH DATA ====================
        async function refreshData() {
            const icon = document.querySelector('.refresh-icon');
            if (icon) icon.classList.add('animate-spin');
            
            showLoading();
            try {
                await loadPeriodeAktif();
                await loadListPeriode();
                
                // Auto load report jika periode sudah dipilih
                const periodeSelect = document.getElementById('periodeSelect');
                if (periodeSelect && periodeSelect.value) {
                    await loadReport();
                }
            } catch (err) {
                showError('Gagal refresh data: ' + err.message);
            } finally {
                hideLoading();
                if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 500);
            }
        }
        
        // Event listener untuk periode change
        document.getElementById('periodeSelect')?.addEventListener('change', function() {
            if (this.value) {
                loadReport();
            }
        });
        
        // ==================== INITIALIZATION ====================
        async function init() {
            console.log('=== INITIALIZING REPORT PAGE ===');
            showLoading();
            try {
                await loadPeriodeAktif();
                await loadListPeriode();
                
                // Setup search on input
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.addEventListener('input', (e) => {
                        filterData(e.target.value);
                    });
                }
                
                // Auto load report jika periode sudah terpilih
                const periodeSelect = document.getElementById('periodeSelect');
                if (periodeSelect && periodeSelect.value) {
                    await loadReport();
                } else {
                    renderEmpty('Pilih periode dan klik Tampilkan');
                }
                
                console.log('=== REPORT PAGE INITIALIZED SUCCESSFULLY ===');
            } catch (err) {
                console.error('Init error:', err);
                showError('Gagal inisialisasi halaman: ' + err.message);
            } finally {
                hideLoading();
            }
        }
        
        // Start initialization
        document.addEventListener('DOMContentLoaded', init);
        
        // Expose functions globally
        window.loadReport = loadReport;
        window.refreshData = refreshData;
        window.exportToExcel = exportToExcel;
    </script>
    
    <style>
        .animate-spin { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        #loadingOverlay { transition: opacity 0.2s ease; }
        #loadingOverlay.hidden { opacity: 0; pointer-events: none; }
        
        /* Search input styling */
        #searchInput:focus {
            outline: none;
            ring: 2px solid #dc2626;
        }

        .filter-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 24px;
            margin-bottom: 24px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 16px;
            align-items: end;
        }

        .filter-grid .field label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 4px;
        }

        .filter-grid .field input,
        .filter-grid .field select {
            width: 100%;
            border: 1px solid #D1D5DB;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 13px;
            box-sizing: border-box;
            outline: none;
        }

        .filter-grid .field input:focus,
        .filter-grid .field select:focus {
            border-color: #F87171;
            box-shadow: 0 0 0 2px rgba(248,113,113,0.2);
        }

        .search-hint {
            font-size: 11px;
            color: #6B7280;
            margin-top: 4px;
            min-height: 16px;
        }

        .field-spacer {
            font-size: 11px;
            min-height: 16px;
            margin-top: 4px;
        }

        .btn-tampilkan {
            width: 100%;
            background: #EF4444;
            color: white;
            font-weight: 600;
            font-size: 13px;
            padding: 8px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-tampilkan:hover {
            background: #DC2626;
        }

        @media (max-width: 768px) {
            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</x-app-layout>