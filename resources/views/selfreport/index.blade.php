<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Report Kehadiran') }}
            </h2>
            <button onclick="refreshData()"
                class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm border border-gray-200 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 refresh-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                Refresh
            </button>
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

            {{-- Alert --}}
            <div id="alertError"
                class="mb-4 hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span id="errorMessage"></span>
            </div>

            {{-- Filter Card --}}
            <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">

                    {{-- Periode --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                        <input type="month" id="periode"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-400 focus:ring-red-400 text-sm"
                            value="{{ date('Y-m') }}">
                    </div>

                    {{-- Nama / NIK --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <select id="atasanSelectBawahan"
                            class="custom-select w-full rounded-md border-gray-300 shadow-sm focus:border-red-400 focus:ring-red-400 text-sm">
                            {{-- Diisi via JS setelah getBawahan --}}
                        </select>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex items-end">
                        <button onclick="loadReport()"
                            class="w-full h-10 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                            </svg>
                            Filter
                        </button>
                        <button onclick="resetFilter()"
                            class="w-full h-10 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            {{-- Legenda Warna --}}
            <div class="flex flex-wrap gap-3 mb-4 text-xs p-4">
                <span class="flex items-center gap-1">
                    <span class="inline-block w-4 h-4 rounded" style="background:#9df59f"></span> Hari Ini
                </span>
                <span class="flex items-center gap-1">
                    <span class="inline-block w-4 h-4 rounded" style="background:#ffe6e6"></span> Libur / Akhir Pekan
                </span>
                <span class="flex items-center gap-1">
                    <span class="inline-block w-4 h-4 rounded" style="background:yellow"></span> Telat
                </span>
                <span class="flex items-center gap-1">
                    <span class="inline-block w-4 h-4 rounded" style="background:red"></span> Mangkir
                </span>
            </div>

            {{-- Tabel --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4 sm:p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[700px] border-collapse text-sm" id="reportTable">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Date</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">In</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Out</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Keterangan</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Absences</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-b">Attendances</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="6" class="text-center py-12 text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Pilih periode dan klik Tampilkan</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary Badge --}}
                    <div id="summaryBar" class="hidden mt-4 flex flex-wrap gap-3 text-sm text-gray-600 border-t pt-4">
                        <span>Total: <strong id="sumTotal">0</strong> hari</span>
                        <span>Hadir: <strong id="sumHadir" class="text-green-600">0</strong></span>
                        <span>Libur: <strong id="sumLibur" class="text-blue-600">0</strong></span>
                        <span>Mangkir: <strong id="sumMangkir" class="text-red-600">0</strong></span>
                        <span>Telat: <strong id="sumTelat" class="text-yellow-600">0</strong></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ========================== SCRIPT ========================== --}}
    <script>
        // Sesuaikan URL API ke path server kamu
        const API_BASE = 'https://web.kobin.co.id/api/hris/selfreport/get_selfreport.php';

        // Data user dari session (di-render server-side, aman)
        const SESSION_NIK      = '{{ session("nik") }}';
        const SESSION_USERNAME = '{{ session("username") }}';
        const SESSION_PLANT    = '{{ session("plant") ?? "0001" }}';
        const SESSION_COMP     = '{{ auth()->user()->comp ?? "0001" }}';

        const today = new Date().toISOString().split('T')[0];

        // ─── Init ───────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', async () => {
            await initBawahan();
            await loadReport();
        });

        // ─── Inisialisasi dropdown bawahan ──────────────────────
        async function initBawahan() {
            try {
                const res    = await fetch(`${API_BASE}?action=getBawahan&nik=${SESSION_NIK}`);
                const result = await res.json();

                const select = document.getElementById('atasanSelectBawahan');
                select.innerHTML = `<option value="${SESSION_NIK}">${SESSION_USERNAME}</option>`;

                if (result.success && result.data.length > 0) {
                    result.data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value       = item.PersonnelNo;
                        opt.textContent = item.FirstName;
                        select.appendChild(opt);
                    });
                }
            } catch (err) {
                console.error('Gagal load bawahan:', err);
                // Tetap tampilkan diri sendiri meski gagal
                document.getElementById('atasanSelectBawahan').innerHTML =
                    `<option value="${SESSION_NIK}">${SESSION_USERNAME}</option>`;
            }
        }

        // ─── Load Report ─────────────────────────────────────────
        async function loadReport() {
            showLoading();
            try {
                const periodeVal = document.getElementById('periode').value; // format: YYYY-MM
                const nik        = document.getElementById('atasanSelectBawahan').value;

                // Ubah ke format YYYY-MM-01 untuk backend
                const tgl = periodeVal + '-01';

                const url = `${API_BASE}?action=report&tgl=${tgl}&nik=${nik}&plant=${SESSION_PLANT}`;
                const res    = await fetch(url);
                const result = await res.json();

                if (!result.success) throw new Error(result.message || 'Gagal mengambil data');

                renderTable(result.data);
                renderSummary(result.data);

            } catch (err) {
                showError('Gagal memuat data: ' + err.message);
                renderEmpty('Terjadi kesalahan saat memuat data');
            } finally {
                hideLoading();
            }
        }

        // ─── Render Tabel ────────────────────────────────────────
        function renderTable(data) {
            const tbody = document.getElementById('tableBody');

            if (!data || data.length === 0) {
                renderEmpty('Tidak ada data untuk periode ini');
                return;
            }

            let html = '';
            data.forEach(item => {
                const rowStyle = getRowStyle(item);
                const badge1   = getBadgeClass(item.absences);
                const badge2   = getBadgeClass(item.attendances);

                html += `
                <tr style="${rowStyle}" class="border-b border-gray-100 hover:brightness-95 transition-all">
                    <td class="px-4 py-2.5 whitespace-nowrap font-medium" data-sort="${item.validfrom}">
                        ${item.inputdate ?? '-'}
                    </td>
                    <td class="px-4 py-2.5 text-center whitespace-nowrap">${item.actualstart ?? '-'}</td>
                    <td class="px-4 py-2.5 text-center whitespace-nowrap">${item.actualfinish ?? '-'}</td>
                    <td class="px-4 py-2.5 max-w-xs text-wrap">${item.LongText ?? '-'}</td>
                    <td class="px-4 py-2.5 text-center">
                        ${item.absences ? `<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold ${badge1}">${item.absences}</span>` : '-'}
                    </td>
                    <td class="px-4 py-2.5 text-center">
                        ${item.attendances ? `<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold ${badge2}">${item.attendances}</span>` : '-'}
                    </td>
                </tr>`;
            });

            tbody.innerHTML = html;
        }

        function renderEmpty(msg = 'Tidak ada data') {
            document.getElementById('tableBody').innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-12 text-gray-400">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>${msg}</span>
                    </div>
                </td>
            </tr>`;
            document.getElementById('summaryBar').classList.add('hidden');
        }

        // ─── Summary Bar ─────────────────────────────────────────
        function renderSummary(data) {
            if (!data || data.length === 0) {
                document.getElementById('summaryBar').classList.add('hidden');
                return;
            }

            let hadir   = 0, libur = 0, mangkir = 0, telat = 0;
            const todayStr = today;

            data.forEach(item => {
                const teks = item.LongText ?? '';
                if (teks.includes('Hari Besar') || teks.includes('Hari Sabtu/Minggu')) {
                    libur++;
                } else if (teks.includes('Mangkir') && formatDate(item.validfrom) !== todayStr) {
                    mangkir++;
                } else if (teks.includes('Telat')) {
                    telat++;
                } else if (item.actualstart) {
                    hadir++;
                }
            });

            document.getElementById('sumTotal').textContent   = data.length;
            document.getElementById('sumHadir').textContent   = hadir;
            document.getElementById('sumLibur').textContent   = libur;
            document.getElementById('sumMangkir').textContent = mangkir;
            document.getElementById('sumTelat').textContent   = telat;
            document.getElementById('summaryBar').classList.remove('hidden');
        }

        // ─── Helper: warna baris ─────────────────────────────────
        function getRowStyle(item) {
            const teks    = item.LongText ?? '';
            const itemTgl = formatDate(item.validfrom);

            if (teks.includes('Hari Besar') || teks.includes('Hari Sabtu/Minggu')) {
                return 'background-color:#ffe6e680';
            }
            if (teks.includes('Mangkir') && itemTgl !== today) {
                return 'background-color:red;color:white';
            }
            if (itemTgl === today) {
                return 'background-color:#9df59f;color:black';
            }
            if (teks.includes('Telat')) {
                return 'background-color:yellow;color:black';
            }
            return '';
        }

        // ─── Helper: badge class (Tailwind) ──────────────────────
        function getBadgeClass(status) {
            if (status === 'Approved')     return 'bg-green-100 text-green-800';
            if (status === 'Need Approved') return 'bg-yellow-100 text-yellow-800';
            if (status === 'Rejected')     return 'bg-red-100 text-red-800';
            return 'bg-gray-100 text-gray-600';
        }

        // ─── Helper: format tanggal dari berbagai format ──────────
        function formatDate(val) {
            if (!val) return '';
            // Bisa berupa string '2024-01-15 00:00:00' atau ISO
            return val.toString().slice(0, 10);
        }

        // ─── Reset Filter ────────────────────────────────────────
        function resetFilter() {
            document.getElementById('periode').value = new Date().toISOString().slice(0, 7);
            document.getElementById('atasanSelectBawahan').value = SESSION_NIK;
            loadReport();
        }

        // ─── Refresh ─────────────────────────────────────────────
        function refreshData() {
            const icon = document.querySelector('.refresh-icon');
            icon.classList.add('animate-spin');
            loadReport().finally(() => {
                setTimeout(() => icon.classList.remove('animate-spin'), 600);
            });
        }

        // ─── Loading & Error UI ──────────────────────────────────
        function showLoading() { document.getElementById('loadingOverlay').classList.remove('hidden'); }
        function hideLoading() { document.getElementById('loadingOverlay').classList.add('hidden'); }

        function showError(msg) {
            const el = document.getElementById('alertError');
            document.getElementById('errorMessage').textContent = msg;
            el.classList.remove('hidden');
            setTimeout(() => el.classList.add('hidden'), 4000);
        }
    </script>

    <style>
        .animate-spin { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        #loadingOverlay { transition: opacity 0.2s ease; }
        #loadingOverlay.hidden { opacity: 0; pointer-events: none; }
        td { vertical-align: middle; }
    </style>

    <style>
        /* Kembalikan style dropdown yang di-override Tailwind */
        select, select * {
            -webkit-appearance: menulist !important;
            appearance: menulist !important;
            background-color: white !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem 2rem 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
            line-height: 1.25rem !important;
        }
        
        /* Perbaiki tombol */
        button {
            font-weight: 600 !important;
            transition-property: background-color !important;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1) !important;
            transition-duration: 150ms !important;
        }
        
        /* Pastikan tabel tetap rapi */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        
        td, th {
            padding: 0.5rem 1rem !important;
            vertical-align: middle !important;
        }

        .custom-select {
            -webkit-appearance: none !important;
            appearance: none !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
            background-position: right 0.5rem center !important;
            background-repeat: no-repeat !important;
            background-size: 1.5em 1.5em !important;
            padding-right: 2.5rem !important;
            background-color: #fff !important;
        }
    </style>
</x-app-layout>