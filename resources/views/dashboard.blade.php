<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Kehadiran') }}
            </h2>
            <button onclick="refreshAllData()"
                class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded flex items-center gap-2">
                <svg class="w-4 h-4 refresh-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Debug Session (HAPUS SETELAH SELESAI DEBUG) --}}
            <!-- <div class="bg-gray-100 p-3 mb-4 rounded text-xs font-mono">
                <strong>🔍 Session Debug:</strong><br>
                kode_jabatan: <span class="text-blue-600">{{ session('kode_jabatan') ?: '(kosong)' }}</span><br>
                plant: <span class="text-blue-600">{{ session('plant') ?: '(kosong)' }}</span><br>
                role: <span class="text-blue-600">{{ session('role') ?: '(kosong)' }}</span><br>
                posisi: <span class="text-blue-600">{{ session('posisi') ?: '(kosong)' }}</span><br>
                nik: <span class="text-blue-600">{{ session('nik') ?: '(kosong)' }}</span><br>
                username: <span class="text-blue-600">{{ session('username') ?: '(kosong)' }}</span><br>
                level: <span class="text-blue-600">{{ session('level') ?: '(kosong)' }}</span><br>
                comp: <span class="text-blue-600">{{ session('comp') ?: '(kosong)' }}</span><br>
                <hr class="my-2">
                <strong>📋 Aturan Akses:</strong><br>
                Admin (kode_jabatan = 0001 AND role = admin): 
                <span class="font-bold {{ session('kode_jabatan') == '0001' && session('role') == 'admin' ? 'text-green-600' : 'text-red-600' }}">
                    {{ session('kode_jabatan') == '0001' && session('role') == 'admin' ? '✓ AKTIF' : '✗ TIDAK AKTIF' }}
                </span><br>
                Atasan (kode_jabatan = 0001 AND (role = admin OR posisi = atasan)):
                <span class="font-bold {{ session('kode_jabatan') == '0001' && (session('role') == 'admin' || session('posisi') == 'atasan') ? 'text-green-600' : 'text-red-600' }}">
                    {{ session('kode_jabatan') == '0001' && (session('role') == 'admin' || session('posisi') == 'atasan') ? '✓ AKTIF' : '✗ TIDAK AKTIF' }}
                </span><br>
                Staff (posisi = staff):
                <span class="font-bold {{ session('posisi') == 'staff' ? 'text-green-600' : 'text-red-600' }}">
                    {{ session('posisi') == 'staff' ? '✓ AKTIF' : '✗ TIDAK AKTIF' }}
                </span>
            </div> -->

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
            </div>

            {{-- Info Card --}}
            <!-- <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
                <div class="flex items-center">
                    <i class="fa fa-bullhorn mr-2 text-blue-500"></i>
                    <p class="text-blue-700">Sekarang Anda dapat mengisi <b>Absence & Attendance</b> dalam satu menu. 
                        <a href="{{ url('izin') }}" class="font-bold underline">Klik disini ya 🪄</a>
                    </p>
                </div>
            </div> -->

            {{-- Welcome Card --}}
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Welcome, {{ session('username') }}</h3>
                
                {{-- Working Hours Info --}}
                <div id="workingHoursInfo" class="mt-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-gray-700 font-medium">Working Office Hour:</span>
                        <span id="workingHoursText" class="text-gray-600">Loading...</span>
                    </div>
                </div>
                
                <p class="text-gray-500 mt-3">Sudah kah anda melakukan absensi hari ini?</p>
                <br>
                <button onclick="window.location.href='{{ url('/absensi/create') }}'"
                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded flex items-center gap-2">
                    + Absen
                </button>
            </div>

            {{-- Admin Section (untuk user dengan comp = 0001 dan roles admin) --}}
            @php
                $kodeJabatan = session('kode_jabatan');
                $role = session('role');
                $posisi = session('posisi');
                $isAdmin = ($kodeJabatan == '0001' && $role == 'admin');
                $isAtasan = ($kodeJabatan == '0001' && ($role == 'admin' || $posisi == 'atasan'));
                $isStaff = ($posisi == 'staff');
            @endphp

            @if($isAdmin)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Dashboard Admin</h3>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Plant</label>
                            <select id="adminSelectPlant"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-400 focus:ring-red-400 text-sm">
                                <option value="">Loading...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                            <select id="adminSelectDept"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-400 focus:ring-red-400 text-sm">
                                <option value="">Loading...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                            <select id="adminSelectPeriode"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-400 focus:ring-red-400 text-sm">
                                <option value="">Loading...</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <div id="attendanceChart" style="width:100%; height:400px"></div>
                        </div>
                        <div>
                            <div id="absenceChart" style="width:100%; height:400px"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Atasan Section (untuk user dengan comp = 0001 dan roles admin/atasan) --}}
            @if($isAtasan)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">👥 Dashboard Bawahan</h3>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                            <select id="atasanSelectPeriode"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-400 focus:ring-red-400 text-sm">
                                <option value="">Loading...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                            <select id="atasanSelectBawahan"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-400 focus:ring-red-400 text-sm">
                                <option value="{{ session('nik') }}">{{ session('username') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <div id="attendanceBawahanChart" style="width:100%; height:400px"></div>
                        </div>
                        <div>
                            <div id="absenceBawahanChart" style="width:100%; height:400px"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Staff Section (untuk staff) --}}
            @if($isStaff)
            <div class="mb-8 p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">👤 Dashboard Saya</h3>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                        <select id="selfSelectPeriode"
                            class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-red-400 focus:ring-red-400 text-sm">
                            <option value="">Loading...</option>
                        </select>
                    </div>
                    <div>
                        <div id="selfChart" style="width:100%; height:400px"></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Jika tidak ada section yang aktif, tampilkan pesan --}}
            @if(!$isAdmin && !$isAtasan && !$isStaff)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                <p class="text-yellow-700">Anda tidak memiliki akses ke dashboard ini. Silakan hubungi administrator.</p>
            </div>
            @endif

        </div>
    </div>

    
    
    {{-- Gunakan CDN alternatif --}}
    <script src="https://cdn.jsdelivr.net/npm/highcharts@11.4.8/highcharts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/highcharts@11.4.8/highcharts-3d.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/highcharts@11.4.8/modules/exporting.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/highcharts@11.4.8/modules/export-data.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/highcharts@11.4.8/modules/accessibility.js"></script>

    <script>
        // ==================== KONFIGURASI ====================
        const API_BASE = 'https://web.kobin.co.id/api/hris/dashboard/get_dashboard.php';
        const SESSION_NIK = '{{ session("nik") }}';
        const SESSION_USERNAME = '{{ session("username") }}';
        const SESSION_PLANT = '{{ session("plant") ?? "1000" }}';
        const SESSION_COMP = '{{ session("kode_jabatan") ?? "0001" }}';
        
        // Cek role dari server-side
        const hasAdminSection = {{ $isAdmin ? 'true' : 'false' }};
        const hasAtasanSection = {{ $isAtasan ? 'true' : 'false' }};
        const hasStaffSection = {{ $isStaff ? 'true' : 'false' }};
        
        // console.log('=== DASHBOARD INIT DEBUG ===');
        // console.log('Session values:', {
        //     nik: SESSION_NIK,
        //     username: SESSION_USERNAME,
        //     comp: SESSION_COMP,
        //     plant: SESSION_PLANT,
        //     hasAdminSection: hasAdminSection,
        //     hasAtasanSection: hasAtasanSection,
        //     hasStaffSection: hasStaffSection
        // });
        
        // Global chart objects
        let attendanceChart = null;
        let absenceChart = null;
        let attendanceBawahanChart = null;
        let absenceBawahanChart = null;
        let selfChart = null;
        
        // Data cache
        let listPeriode = [];
        let listPlant = [];
        let listDept = [];
        let listBawahan = [];
        let periodeAktif = null;  // Untuk menyimpan periode aktif
        
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

        async function fetchWorkingHours() {
            const nik = SESSION_NIK;
            const today = new Date().toISOString().split('T')[0]; // Format YYYY-MM-DD
            
            try {
                const url = `https://web.kobin.co.id/api/hris/harikerja/getharikerja.php?personnelNo=${nik}&validFrom=${today}`;
                const response = await fetch(url);
                const result = await response.json();
                
                if (result.success && result.data && result.data.length > 0) {
                    const workingData = result.data[0];
                    const plannedStart = workingData.PlannedStart;
                    const plannedFinish = workingData.PlannedFinish;
                    
                    // Extract time from datetime (format: 2026-05-25 08:00:00)
                    const startTime = plannedStart ? plannedStart.split(' ')[1].substring(0, 5) : '--:--';
                    const finishTime = plannedFinish ? plannedFinish.split(' ')[1].substring(0, 5) : '--:--';
                    
                    return {
                        startTime: startTime,
                        finishTime: finishTime,
                        fullStart: plannedStart,
                        fullFinish: plannedFinish,
                        workSchedule: workingData.WorkSchedule,
                        dailyWS: workingData.DailyWS
                    };
                }
                return null;
            } catch (err) {
                console.error('Error fetching working hours:', err);
                return null;
            }
        }

        // Fungsi untuk memuat dan menampilkan jam kerja
        async function loadWorkingHours() {
            const workingHoursText = document.getElementById('workingHoursText');
            if (!workingHoursText) return;
            
            try {
                const workingHours = await fetchWorkingHours();
                
                if (workingHours && workingHours.startTime !== '--:--') {
                    workingHoursText.textContent = `${workingHours.startTime} - ${workingHours.finishTime}`;
                    
                    // Optional: Tambahkan tooltip dengan informasi lebih detail
                    workingHoursText.title = `Schedule: ${workingHours.workSchedule || '-'} | Daily: ${workingHours.dailyWS || '-'}`;
                    
                    // Optional: Ubah warna berdasarkan status jam kerja saat ini
                    const now = new Date();
                    const currentTime = now.getHours() * 60 + now.getMinutes();
                    const startMinutes = parseInt(workingHours.startTime.split(':')[0]) * 60 + parseInt(workingHours.startTime.split(':')[1]);
                    const finishMinutes = parseInt(workingHours.finishTime.split(':')[0]) * 60 + parseInt(workingHours.finishTime.split(':')[1]);
                    
                    if (currentTime < startMinutes) {
                        // Belum jam kerja
                        workingHoursText.classList.add('text-blue-600');
                        workingHoursText.parentElement.parentElement.classList.add('border-blue-400', 'bg-blue-400');
                    } else if (currentTime >= startMinutes && currentTime <= finishMinutes) {
                        // Jam kerja
                        workingHoursText.classList.add('text-green-600');
                        workingHoursText.parentElement.parentElement.classList.add('border-green-400', 'bg-green-400');
                    } else {
                        // Sudah lewat jam kerja
                        workingHoursText.classList.add('text-orange-600');
                        workingHoursText.parentElement.parentElement.classList.add('border-orange-400', 'bg-orange-400');
                    }
                } else {
                    workingHoursText.textContent = 'Tidak tersedia';
                    workingHoursText.classList.add('text-red-600');
                }
            } catch (err) {
                console.error('Error loading working hours:', err);
                workingHoursText.textContent = 'Gagal memuat data';
                workingHoursText.classList.add('text-red-600');
            }
        }

        function formatPeriodeForAPI(periode) {
            if (!periode) return '';
            
            // Jika sudah dalam format YYYY-MM-DD
            if (periode.match(/^\d{4}-\d{2}-\d{2}$/)) {
                return periode;
            }
            
            // Jika dalam format YYYYMM
            if (periode.match(/^\d{6}$/)) {
                const year = periode.substring(0, 4);
                const month = periode.substring(4, 6);
                return `${year}-${month}-01`;
            }
            
            return periode;
        }

        function formatPeriodeName(periode) {
            if (!periode) return '';
            if (periode.match(/^\d{6}$/)) {
                const year = periode.substring(0, 4);
                const month = periode.substring(4, 6);
                const date = new Date(year, parseInt(month) - 1, 1);
                return date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
            }
            if (periode.match(/^\d{4}-\d{2}-\d{2}$/)) {
                const [year, month] = periode.split('-');
                const date = new Date(year, parseInt(month) - 1, 1);
                return date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
            }
            return periode;
        }
        
        async function fetchAPI(action, params = {}) {
            const url = new URL(API_BASE);
            url.searchParams.append('action', action);
            Object.keys(params).forEach(key => {
                if (params[key] !== undefined && params[key] !== null && params[key] !== '') {
                    url.searchParams.append(key, params[key]);
                }
            });
            
            // console.log(`Fetching ${action}:`, url.toString());
            
            try {
                const response = await fetch(url);
                const result = await response.json();
                // console.log(`Response ${action}:`, result);
                
                if (!result.success) {
                    throw new Error(result.message || 'Gagal mengambil data');
                }
                return result.data;
            } catch (err) {
                console.error(`Fetch error ${action}:`, err);
                throw err;
            }
        }
        
        // ==================== LOAD PERIODE AKTIF ====================
        async function loadPeriodeAktif() {
            try {
                const data = await fetchAPI('getPeriodeNow', { 
                    comp: SESSION_COMP, 
                    plant: SESSION_PLANT 
                });
                
                // console.log('Response getPeriodeNow:', data);
                
                if (data && data.length > 0) {
                    // Data dari getPeriodeNow biasanya berisi periode dalam format YYYYMM
                    // Contoh: { Periode: "202603", ... }
                    if (data[0].Periode) {
                        periodeAktif = data[0].Periode;
                        // console.log('Periode aktif ditemukan:', periodeAktif);
                        return periodeAktif;
                    }
                    // Coba field lain jika ada
                    if (data[0].periode) {
                        periodeAktif = data[0].periode;
                        // console.log('Periode aktif ditemukan (periode):', periodeAktif);
                        return periodeAktif;
                    }
                }
                
                console.warn('Periode aktif tidak ditemukan, menggunakan default');
                return null;
            } catch (err) {
                console.error('Gagal load periode aktif:', err);
                return null;
            }
        }
        
        // ==================== LOAD MASTER DATA ====================
        async function loadMasterData() {
            // console.log('Loading master data...');
            try {
                // Load periode aktif dulu
                await loadPeriodeAktif();
                
                // Load list periode
                const periodeData = await fetchAPI('getListPeriode');
                listPeriode = periodeData;
                // console.log('Periode loaded:', listPeriode.length);
                
                // Load plant
                const plantData = await fetchAPI('getPlant');
                listPlant = plantData;
                // console.log('Plant loaded:', listPlant.length);
                
                // Load departemen
                const deptData = await fetchAPI('getListDept');
                listDept = deptData;
                // console.log('Dept loaded:', listDept.length);
                
                // Load bawahan (untuk atasan)
                if (hasAtasanSection && SESSION_COMP === '0001') {
                    const bawahanData = await fetchAPI('getBawahan', { nik: SESSION_NIK });
                    listBawahan = bawahanData;
                    // console.log('Bawahan loaded:', listBawahan.length);
                }
                
                return true;
            } catch (err) {
                showError('Gagal memuat data master: ' + err.message);
                return false;
            }
        }
        
        // ==================== RENDER DROPDOWNS ====================
        function renderDropdowns() {
            // console.log('Rendering dropdowns...');
            // console.log('List periode available:', listPeriode);
            // console.log('Periode aktif:', periodeAktif);
            
            // Fungsi untuk menentukan periode yang dipilih
            function getSelectedPeriode() {
                if (!listPeriode.length) {
                    console.warn('List periode kosong');
                    return '';
                }
                
                // Log semua periode yang tersedia
                // console.log('Semua periode:', listPeriode.map(p => p.Periode));
                
                // Coba cari periode aktif di list
                if (periodeAktif) {
                    // Konversi periodeAktif ke string untuk perbandingan yang aman
                    const aktifStr = String(periodeAktif).trim();
                    // console.log('Mencari periode aktif:', aktifStr);
                    
                    // Cari dengan berbagai format
                    const found = listPeriode.find(p => {
                        const periodeItem = String(p.Periode).trim();
                        // Bandingkan langsung
                        if (periodeItem === aktifStr) {
                            // console.log('Found exact match:', periodeItem);
                            return true;
                        }
                        // Bandingkan 6 digit pertama jika format berbeda
                        if (periodeItem.substring(0, 6) === aktifStr.substring(0, 6)) {
                            // console.log('Found partial match:', periodeItem, 'vs', aktifStr);
                            return true;
                        }
                        return false;
                    });
                    
                    if (found) {
                        // console.log('Periode aktif ditemukan di list:', found.Periode);
                        return found.Periode;
                    } else {
                        console.log('Periode aktif', periodeAktif, 'tidak ditemukan di list periode');
                        console.log('List periode tersedia:', listPeriode.map(p => p.Periode));
                    }
                }
                
                // Jika tidak ada periode aktif atau tidak ditemukan, ambil periode yang paling mendekati tanggal sekarang
                const now = new Date();
                const currentYearMonth = now.getFullYear().toString() + (now.getMonth() + 1).toString().padStart(2, '0');
                // console.log('Current year month:', currentYearMonth);
                
                // Cari periode yang sama dengan bulan sekarang
                const currentPeriod = listPeriode.find(p => {
                    const periodeStr = String(p.Periode);
                    return periodeStr === currentYearMonth || periodeStr.substring(0, 6) === currentYearMonth;
                });
                
                if (currentPeriod) {
                    // console.log('Menggunakan periode bulan sekarang:', currentPeriod.Periode);
                    return currentPeriod.Periode;
                }
                
                // Jika tidak ada, ambil yang pertama (terbaru karena order by desc)
                // console.log('Menggunakan periode pertama (terbaru):', listPeriode[0].Periode);
                return listPeriode[0].Periode;
            }
            
            // Self Periode dropdown
            const selfSelect = document.getElementById('selfSelectPeriode');
            if (selfSelect && listPeriode.length > 0) {
                selfSelect.innerHTML = '';
                listPeriode.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.Periode;
                    // Format nama periode yang lebih bagus
                    const formattedName = formatPeriodeName(item.Periode);
                    option.textContent = formattedName;
                    selfSelect.appendChild(option);
                });
                
                const selectedValue = getSelectedPeriode();
                selfSelect.value = selectedValue;
                console.log('Self periode dropdown rendered, selected:', selectedValue);
                
                // Trigger load chart setelah dropdown diisi
                if (hasStaffSection && selfSelect.value) {
                    setTimeout(() => loadSelfChart(), 100);
                }
            } else if (selfSelect) {
                console.warn('No periode data available');
                selfSelect.innerHTML = '<option value="">No data</option>';
            }
            
            // Admin Periode dropdown
            const adminPeriodeSelect = document.getElementById('adminSelectPeriode');
            if (adminPeriodeSelect && listPeriode.length > 0) {
                adminPeriodeSelect.innerHTML = '';
                listPeriode.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.Periode;
                    const formattedName = formatPeriodeName(item.Periode);
                    option.textContent = formattedName;
                    adminPeriodeSelect.appendChild(option);
                });
                adminPeriodeSelect.value = getSelectedPeriode();
                console.log('Admin periode dropdown rendered, selected:', adminPeriodeSelect.value);
                
                if (hasAdminSection && adminPeriodeSelect.value) {
                    setTimeout(() => loadAdminChart(), 100);
                }
            }
            
            // Atasan Periode dropdown
            const atasanPeriodeSelect = document.getElementById('atasanSelectPeriode');
            if (atasanPeriodeSelect && listPeriode.length > 0) {
                atasanPeriodeSelect.innerHTML = '';
                listPeriode.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.Periode;
                    const formattedName = formatPeriodeName(item.Periode);
                    option.textContent = formattedName;
                    atasanPeriodeSelect.appendChild(option);
                });
                atasanPeriodeSelect.value = getSelectedPeriode();
                console.log('Atasan periode dropdown rendered, selected:', atasanPeriodeSelect.value);
                
                if (hasAtasanSection && atasanPeriodeSelect.value) {
                    setTimeout(() => loadAtasanChart(), 100);
                }
            }
            
            // Admin Plant dropdown
            const adminPlantSelect = document.getElementById('adminSelectPlant');
            if (adminPlantSelect && listPlant.length > 0) {
                adminPlantSelect.innerHTML = '';
                listPlant.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.code;
                    option.textContent = item.nama;
                    if (item.code === SESSION_PLANT) option.selected = true;
                    if (item.code !== SESSION_PLANT) option.disabled = true;
                    adminPlantSelect.appendChild(option);
                });
            }
            
            // Admin Dept dropdown
            const adminDeptSelect = document.getElementById('adminSelectDept');
            if (adminDeptSelect && listDept.length > 0) {
                adminDeptSelect.innerHTML = '';
                listDept.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.Desc1;
                    option.textContent = item.Desc1;
                    if (item.Code === '1674') option.selected = true;
                    adminDeptSelect.appendChild(option);
                });
            }
            
            // Atasan Bawahan dropdown
            const atasanBawahanSelect = document.getElementById('atasanSelectBawahan');
            if (atasanBawahanSelect) {
                atasanBawahanSelect.innerHTML = `<option value="${SESSION_NIK}">${SESSION_USERNAME}</option>`;
                listBawahan.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.PersonnelNo;
                    option.textContent = item.FirstName;
                    atasanBawahanSelect.appendChild(option);
                });
            }
        }

        // ==================== LOAD SELF CHART ====================
        async function loadSelfChart() {
            console.log('loadSelfChart called, selfChart exists:', !!selfChart);
            
            if (!selfChart) {
                console.warn('selfChart is null, skipping load');
                return;
            }
            
            const periodeSelect = document.getElementById('selfSelectPeriode');
            if (!periodeSelect) {
                console.error('selfSelectPeriode element not found!');
                return;
            }
            
            const periodeValue = periodeSelect.value;
            if (!periodeValue) {
                console.warn('No periode selected');
                return;
            }
            
            // FORMAT PERIODE untuk API (YYYY-MM-01)
            const periodeForAPI = formatPeriodeForAPI(periodeValue);
            const plant = SESSION_PLANT;
            
            console.log('Loading self chart for periode:', periodeValue, '-> formatted:', periodeForAPI, 'nik:', SESSION_NIK, 'plant:', plant);
            
            try {
                const data = await fetchAPI('getChartByNik', { 
                    nik: SESSION_NIK, 
                    periode: periodeForAPI,  // Kirim format YYYY-MM-01
                    plant: plant 
                });
                console.log('Self chart data received:', data);
                
                const chartData = data[0] || {};
                const totalTelat = Number(chartData.l1 || 0) + Number(chartData.l2 || 0) + Number(chartData.l3 || 0);
                
                const pieData = [
                    ['Telat', totalTelat],
                    ['Pulang Awal', Number(chartData.p || 0)],
                    ['Mangkir', Number(chartData.m || 0)],
                    ['Izin', Number(chartData.i || 0)],
                    ['Dispensasi', Number(chartData.dis || 0)],
                    ['Cuti', Number(chartData.c || 0)],
                    ['Sakit', Number(chartData.s || 0)],
                    ['Dinas', Number(chartData.d || 0)]
                ];
                
                console.log('Pie data to update:', pieData);
                
                selfChart.series[0].update({
                    data: pieData
                });
                
                console.log('Self chart updated successfully');
            } catch (err) {
                console.error('Error loading self chart:', err);
                showError('Gagal memuat chart: ' + err.message);
            }
        }
        
        // ==================== LOAD ATASAN CHART ====================
        async function loadAtasanChart() {
            if (!attendanceBawahanChart && !absenceBawahanChart) return;
            
            const periodeSelect = document.getElementById('atasanSelectPeriode');
            if (!periodeSelect) return;
            
            const periodeValue = periodeSelect.value;
            if (!periodeValue) return;
            
            // FORMAT PERIODE untuk API
            const periodeForAPI = formatPeriodeForAPI(periodeValue);
            const nik = document.getElementById('atasanSelectBawahan')?.value || SESSION_NIK;
            const plant = SESSION_PLANT;
            
            console.log('Loading atasan chart for periode:', periodeValue, '-> formatted:', periodeForAPI, 'nik:', nik);
            
            try {
                const data = await fetchAPI('getChartByNik', { 
                    nik: nik, 
                    periode: periodeForAPI, 
                    plant: plant 
                });
                const chartData = data[0] || {};
                const name = chartData.firstname || SESSION_USERNAME;
                
                if (attendanceBawahanChart) {
                    attendanceBawahanChart.series[0].update({
                        data: [Number(chartData.l1 || 0), Number(chartData.l2 || 0), 
                            Number(chartData.l3 || 0), Number(chartData.p || 0)],
                        name: name
                    });
                }
                
                if (absenceBawahanChart) {
                    absenceBawahanChart.series[0].update({
                        data: [Number(chartData.m || 0), Number(chartData.i || 0), 
                            Number(chartData.dis || 0), Number(chartData.c || 0),
                            Number(chartData.s || 0), Number(chartData.d || 0)],
                        name: name
                    });
                }
            } catch (err) {
                console.error('Error loading atasan chart:', err);
                showError('Gagal memuat chart atasan: ' + err.message);
            }
        }
        
        // ==================== LOAD ADMIN CHART ====================
        async function loadAdminChart() {
            if (!attendanceChart && !absenceChart) return;
            
            const plant = document.getElementById('adminSelectPlant')?.value || SESSION_PLANT;
            const dept = document.getElementById('adminSelectDept')?.value || 'HRGA';
            const periodeValue = document.getElementById('adminSelectPeriode')?.value;
            
            if (!periodeValue) return;
            
            // FORMAT PERIODE untuk API
            const periodeForAPI = formatPeriodeForAPI(periodeValue);
            
            console.log('Loading admin chart for dept:', dept, 'periode:', periodeValue, '-> formatted:', periodeForAPI);
            
            try {
                const data = await fetchAPI('getChartByDept', { 
                    dept: dept, 
                    periode: periodeForAPI, 
                    plant: plant 
                });
                const chartData = data[0] || {};
                
                if (attendanceChart) {
                    attendanceChart.series[0].update({
                        data: [Number(chartData.l1 || 0), Number(chartData.l2 || 0), 
                            Number(chartData.l3 || 0), Number(chartData.p || 0)],
                        name: chartData.dept || dept
                    });
                }
                
                if (absenceChart) {
                    absenceChart.series[0].update({
                        data: [Number(chartData.m || 0), Number(chartData.i || 0), 
                            Number(chartData.dis || 0), Number(chartData.c || 0),
                            Number(chartData.s || 0), Number(chartData.d || 0)],
                        name: chartData.dept || dept
                    });
                }
            } catch (err) {
                console.error('Error loading admin chart:', err);
                showError('Gagal memuat chart admin: ' + err.message);
            }
        }
        
        // ==================== INIT CHARTS ====================
        function initSelfChart() {
            const container = document.getElementById('selfChart');
            if (!container) {
                console.error('selfChart container not found!');
                return null;
            }
            
            console.log('Initializing selfChart...');
            
            try {
                const chart = Highcharts.chart('selfChart', {
                    chart: {
                        type: 'pie',
                        options3d: {
                            enabled: true,
                            alpha: 35,
                            beta: 0
                        }
                    },
                    legend: {
                        enabled: true,
                        align: 'right'
                    },
                    colors: [
                        'rgba(235, 64, 52,0.8)',
                        'rgba(235, 192, 52,0.8)',
                        'rgba(235, 229, 52,0.8)',
                        'rgba(171, 235, 52,0.8)',
                        'rgba(52, 235, 89,0.8)',
                        'rgba(52, 235, 168,0.8)',
                        'rgba(52, 235, 232,0.8)',
                        'rgba(52, 159, 235,0.8)'
                    ],
                    title: {
                        text: 'Absence and Attendance'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            depth: 35,
                            dataLabels: {
                                enabled: false,
                                format: '{point.name}'
                            },
                            showInLegend: true
                        }
                    },
                    series: [{
                        type: 'pie',
                        name: 'Data',
                        data: [
                            ['Telat', 0],
                            ['Pulang Awal', 0],
                            ['Mangkir', 0],
                            ['Izin', 0],
                            ['Dispensasi', 0],
                            ['Cuti', 0],
                            ['Sakit', 0],
                            ['Dinas', 0]
                        ]
                    }]
                });
                
                console.log('selfChart initialized successfully');
                return chart;
            } catch (err) {
                console.error('Error initializing selfChart:', err);
                return null;
            }
        }
        
        function initAttendanceChart(containerId, title, categories, colors, seriesData) {
            const container = document.getElementById(containerId);
            if (!container) return null;
            
            return Highcharts.chart(containerId, {
                chart: { type: 'column', options3d: { enabled: true, alpha: 0, beta: 0, depth: 20, viewDistance: 25 } },
                colors: colors,
                title: { text: title },
                xAxis: { categories: categories },
                yAxis: { title: { text: 'Jumlah' } },
                plotOptions: { series: { depth: 25, colorByPoint: true } },
                series: seriesData
            });
        }
        
        function initAbsenceChart(containerId, title, categories, colors, seriesData) {
            const container = document.getElementById(containerId);
            if (!container) return null;
            
            return Highcharts.chart(containerId, {
                chart: { type: 'column', options3d: { enabled: true, alpha: 0, beta: 0, depth: 20, viewDistance: 25 } },
                colors: colors,
                title: { text: title },
                xAxis: { categories: categories },
                yAxis: { title: { text: 'Jumlah' } },
                plotOptions: { series: { depth: 25, colorByPoint: true } },
                series: seriesData
            });
        }
        
        function initAllCharts() {
            console.log('Initializing charts, hasStaffSection:', hasStaffSection);
            
            if (hasStaffSection) {
                selfChart = initSelfChart();
            }
            
            if (hasAdminSection) {
                attendanceChart = initAttendanceChart('attendanceChart', 'Attendance Chart', 
                    ['L1', 'L2', 'L3', 'Pulang awal'], 
                    ['#4df241', '#f2f241', '#f27641', '#c041f2'],
                    [{ name: 'HRGA', data: [0, 0, 0, 0] }]);
                
                absenceChart = initAbsenceChart('absenceChart', 'Absence Chart',
                    ['Mangkir', 'Ijin', 'Dispensasi', 'Cuti', 'Sakit', 'Dinas'],
                    ['#F24141', '#F2A541', '#F3CA40', '#40F99B', '#D78A76', '#577590'],
                    [{ name: 'HRGA', data: [0, 0, 0, 0, 0, 0] }]);
            }
            
            if (hasAtasanSection) {
                attendanceBawahanChart = initAttendanceChart('attendanceBawahanChart', 'Attendance Chart',
                    ['L1', 'L2', 'L3', 'Pulang awal'],
                    ['#4df241', '#f2f241', '#f27641', '#c041f2'],
                    [{ name: SESSION_USERNAME, data: [0, 0, 0, 0] }]);
                
                absenceBawahanChart = initAbsenceChart('absenceBawahanChart', 'Absence Chart',
                    ['Mangkir', 'Ijin', 'Dispensasi', 'Cuti', 'Sakit', 'Dinas'],
                    ['#F24141', '#F2A541', '#F3CA40', '#40F99B', '#D78A76', '#577590'],
                    [{ name: SESSION_USERNAME, data: [0, 0, 0, 0, 0, 0] }]);
            }
        }
        
        // ==================== EVENT LISTENERS ====================
        function setupEventListeners() {
            // Self filter
            if (hasStaffSection) {
                const selfPeriode = document.getElementById('selfSelectPeriode');
                if (selfPeriode) {
                    selfPeriode.addEventListener('change', () => {
                        console.log('Self periode changed to:', selfPeriode.value);
                        loadSelfChart();
                    });
                }
            }
            
            // Atasan filters
            if (hasAtasanSection) {
                const atasanPeriode = document.getElementById('atasanSelectPeriode');
                const atasanBawahan = document.getElementById('atasanSelectBawahan');
                
                if (atasanPeriode) {
                    atasanPeriode.addEventListener('change', () => {
                        console.log('Atasan periode changed to:', atasanPeriode.value);
                        loadAtasanChart();
                    });
                }
                if (atasanBawahan) {
                    atasanBawahan.addEventListener('change', () => {
                        console.log('Atasan bawahan changed to:', atasanBawahan.value);
                        loadAtasanChart();
                    });
                }
            }
            
            // Admin filters
            if (hasAdminSection) {
                const adminPlant = document.getElementById('adminSelectPlant');
                const adminDept = document.getElementById('adminSelectDept');
                const adminPeriode = document.getElementById('adminSelectPeriode');
                
                if (adminPlant) adminPlant.addEventListener('change', () => loadAdminChart());
                if (adminDept) adminDept.addEventListener('change', () => loadAdminChart());
                if (adminPeriode) adminPeriode.addEventListener('change', () => loadAdminChart());
            }
        }
        
        // ==================== REFRESH ALL ====================
        async function refreshAllData() {
            console.log('Refreshing all data...');
            const icon = document.querySelector('.refresh-icon');
            if (icon) icon.classList.add('animate-spin');
            
            showLoading();
            try {
                await loadMasterData();
                renderDropdowns();
                await loadSelfChart();
                await loadAtasanChart();
                await loadAdminChart();
            } catch (err) {
                showError('Gagal refresh data: ' + err.message);
            } finally {
                hideLoading();
                if (icon) setTimeout(() => icon.classList.remove('animate-spin'), 500);
            }
        }
        
        // ==================== INITIALIZATION ====================
        async function init() {
            // console.log('=== INITIALIZING DASHBOARD ===');
            // console.log('Checking Highcharts availability:', typeof Highcharts !== 'undefined');
            
            if (typeof Highcharts === 'undefined') {
                console.error('Highcharts not loaded!');
                showError('Highcharts library gagal dimuat. Periksa koneksi internet.');
                return;
            }
            
            showLoading();
            try {
                // 1. Load master data (termasuk periode aktif)
                await loadMasterData();
                
                // 2. Render dropdowns (akan memilih periode yang benar)
                renderDropdowns();
                
                // 3. Init charts
                initAllCharts();
                
                // 4. Setup event listeners
                setupEventListeners();
                
                // 5. Load charts (akan menggunakan periode yang sudah dipilih)
                // Gunakan setTimeout untuk memastikan dropdown sudah siap
                setTimeout(async () => {
                    await loadSelfChart();
                    await loadAtasanChart();
                    await loadAdminChart();
                    await loadWorkingHours();
                    // console.log('=== DASHBOARD INITIALIZED SUCCESSFULLY ===');
                }, 200);
                
            } catch (err) {
                console.error('Init error:', err);
                showError('Gagal inisialisasi dashboard: ' + err.message);
            } finally {
                hideLoading();
            }
        }
        
        // Wait for DOM and Highcharts to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
        
        // Expose refresh function globally
        window.refreshAllData = refreshAllData;
    </script>
        
    <style>
        .animate-spin { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        #loadingOverlay { transition: opacity 0.2s ease; }
        #loadingOverlay.hidden { opacity: 0; pointer-events: none; }
    </style>
</x-app-layout>