<div id="orientationActivities" class="mt-4 rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden" x-data="{
    modalTambahBaris: false,
    activities: [],
    filteredActivities: [],
    selectedPlant: null,
    picData: [],
    selectedActivity: null,
    selectedActivityId: '',
    activityDropdownOpen: false,
    activitySearch: '',
    selectedPic: null,
    searchActivity: '',
    searchPic: '',
    isLoading: false,
    kegiatanRows: [],
    editingIndex: null,

    init() {
        this.selectedPlant = window.selectedPlantData || null;

        // Load activities from PHP
        this.loadActivitiesFromPHP();

        // Load kegiatan rows from PHP
        this.loadKegiatanRowsFromPHP();

        this.filterActivitiesByPlant();
        window.addEventListener('orientation-plant-selected', (event) => {
            this.selectedPlant = event.detail;
            if (this.kegiatanRows.length > 0) {
                this.kegiatanRows = [];
            }
            this.selectedActivity = null;
            this.selectedActivityId = '';
            this.activityDropdownOpen = false;
            this.activitySearch = '';
            window.selectedActivityId = null;
            window.selectedActivityData = null;
            this.filterActivitiesByPlant(event.detail);
        });

        // Load PICs from API
        this.loadPICs();
    },

    loadActivitiesFromPHP() {
        try {
            const activitiesData = {{ \Illuminate\Support\Js::from($masterOrientationActivities ?? []) }};
            this.activities = activitiesData;
            this.filteredActivities = activitiesData;
            console.log('Activities loaded:', this.activities.length);
        } catch (e) {
            console.error('Error loading activities:', e);
            this.activities = [];
            this.filteredActivities = [];
        }
    },

    loadKegiatanRowsFromPHP() {
        try {
            const rowsData = {{ \Illuminate\Support\Js::from($kegiatanRows ?? []) }};
            this.kegiatanRows = rowsData;
            console.log('Kegiatan rows loaded:', this.kegiatanRows.length);
        } catch (e) {
            console.error('Error loading kegiatan rows:', e);
            this.kegiatanRows = [];
        }
    },

    filterActivitiesByPlant(selectedPlant = window.selectedPlantData) {
        if (!selectedPlant) {
            this.filteredActivities = [];
            return;
        }

        this.filteredActivities = this.activities.filter(item => {
            let plantIds = item.plants || [];
            if (typeof plantIds === 'string') {
                try { plantIds = JSON.parse(plantIds); } catch (error) { plantIds = []; }
            }
            return Array.isArray(plantIds) && plantIds.map(String).includes(String(selectedPlant.id));
        });
    },

    async loadPICs() {
        try {
            const response = await fetch('https://web.kobin.co.id/api/attendance/live/api_get_users.php');
            const result = await response.json();

            if (result.success && result.data) {
                this.picData = result.data.map(user => ({
                    nik: user.nik,
                    nama: user.nama,
                    jabatan: user.jabatan || '-',
                    dept: user.dept || '-',
                    plant: user.plant || ''
                }));
                this.enrichKegiatanRowsWithPic();
                console.log('PICs loaded:', this.picData.length);
            }
        } catch (error) {
            console.error('Error loading PICs:', error);
            this.picData = [];
        }
    },

    // Lengkapi nama dan jabatan pada kegiatan lama berdasarkan NIK dari API user.
    enrichKegiatanRowsWithPic() {
        this.kegiatanRows = this.kegiatanRows.map(row => {
            const picNik = String(row.pic_nik || row.pic || '').trim();
            const user = this.picData.find(item => String(item.nik || '').trim() === picNik);

            if (!user) {
                return {
                    ...row,
                    pic_nik: picNik,
                    pic: row.pic || picNik,
                    jabatan: row.jabatan || '-'
                };
            }

            return {
                ...row,
                pic_nik: user.nik,
                pic: user.nama || picNik,
                jabatan: user.jabatan || '-'
            };
        });
    },

    filterActivities() {
        if (!this.searchActivity.trim()) {
            this.filterActivitiesByPlant();
            return;
        }
        const query = this.searchActivity.toLowerCase().trim();
        this.filteredActivities = this.activities.filter(item => {
            const plant = window.selectedPlantData;
            const plantIds = Array.isArray(item.plants) ? item.plants : [];
            if (!plant || !item.status || !plantIds.map(String).includes(String(plant.id))) return false;

            return (item.activity_name && item.activity_name.toLowerCase().includes(query)) ||
                (item.description && item.description.toLowerCase().includes(query));
        });
    },

    selectActivity(item) {
        this.selectedActivity = item;
        this.selectedActivityId = String(item.id);
        this.activityDropdownOpen = false;
        this.activitySearch = '';
        this.searchActivity = item.activity_name;
        window.selectedActivityId = item.id;
        window.selectedActivityData = item;
    },

    selectActivityById(activityId) {
        const activity = this.filteredActivities.find(item => String(item.id) === String(activityId));
        if (activity) {
            this.selectActivity(activity);
            return;
        }

        this.selectedActivity = null;
        window.selectedActivityId = null;
        window.selectedActivityData = null;
    },

    get searchedActivities() {
        const query = this.activitySearch.trim().toLowerCase();
        if (!query) return this.filteredActivities;

        return this.filteredActivities.filter(item =>
            (item.activity_name || '').toLowerCase().includes(query) ||
            (item.description || '').toLowerCase().includes(query)
        );
    },

    selectPic(item) {
        this.selectedPic = item;
        this.searchPic = item.nama;
        const jabatanField = document.getElementById('activity_position');
        if (jabatanField) {
            jabatanField.value = item.jabatan || '-';
        }
        const nikField = document.getElementById('activity_pic_nik');
        if (nikField) {
            nikField.value = item.nik || '';
        }
    },

    filterPICs() {
        if (!this.searchPic.trim()) {
            return this.picData;
        }
        const query = this.searchPic.toLowerCase().trim();
        return this.picData.filter(item =>
            (item.nama && item.nama.toLowerCase().includes(query)) ||
            (item.nik && item.nik.includes(query)) ||
            (item.jabatan && item.jabatan.toLowerCase().includes(query))
        );
    },

    tambahKegiatan() {
        const activityId = window.selectedActivityId;
        const activityData = window.selectedActivityData;
        const tanggal = document.getElementById('activity_date').value;
        const startTime = document.getElementById('activity_start_time').value;
        const endTime = document.getElementById('activity_end_time').value;
        const picNik = document.getElementById('activity_pic_nik').value;
        const currentRow = this.editingIndex !== null ? this.kegiatanRows[this.editingIndex] : null;
        const picName = this.selectedPic?.nama || currentRow?.pic || '';
        const position = document.getElementById('activity_position').value;

        if (!activityId) {
            alert('Silakan pilih kegiatan terlebih dahulu');
            return;
        }

        if (!tanggal) {
            alert('Silakan pilih tanggal');
            return;
        }

        if (!startTime || !endTime) {
            alert('Silakan isi waktu mulai dan selesai');
            return;
        }

        if (!picNik) {
            alert('Silakan pilih PIC terlebih dahulu');
            return;
        }

        const newActivity = {
            id: currentRow?.id || Date.now(),
            activity_id: activityId,
            title: activityData.activity_name,
            description: activityData.description || '',
            tanggal: tanggal,
            waktu_mulai: startTime,
            waktu_selesai: endTime,
            pic: picName,
            pic_nik: picNik,
            jabatan: position,
            icon: this.getRandomIcon(),
            color: this.getRandomColor()
        };

        if (this.editingIndex !== null) {
            this.kegiatanRows.splice(this.editingIndex, 1, newActivity);
        } else {
            this.kegiatanRows.push(newActivity);
        }

        this.resetActivityForm();
        this.modalTambahBaris = false;
    },

    editKegiatan(index) {
        const row = this.kegiatanRows[index];
        if (!row) return;

        const activity = this.activities.find(item => String(item.id) === String(row.activity_id));
        this.editingIndex = index;
        this.selectedActivity = activity || {id: row.activity_id, activity_name: row.title, description: row.description || ''};
        this.selectedActivityId = String(row.activity_id);
        window.selectedActivityId = row.activity_id;
        window.selectedActivityData = this.selectedActivity;
        this.selectedPic = {nik: row.pic_nik, nama: row.pic, jabatan: row.jabatan || ''};
        this.modalTambahBaris = true;

        this.$nextTick(() => {
            document.getElementById('activity_date').value = row.tanggal || '';
            document.getElementById('activity_start_time').value = row.waktu_mulai || '08:00';
            document.getElementById('activity_end_time').value = row.waktu_selesai || '10:00';
            document.getElementById('activity_pic_nik').value = row.pic_nik || '';
            document.getElementById('activity_position').value = row.jabatan || '';
        });
    },

    resetActivityForm() {

        // Reset form
        this.selectedActivity = null;
        this.selectedActivityId = '';
        this.selectedPic = null;
        this.searchActivity = '';
        this.searchPic = '';
        window.selectedActivityId = null;
        window.selectedActivityData = null;
        document.getElementById('activity_date').value = '';
        document.getElementById('activity_start_time').value = '08:00';
        document.getElementById('activity_end_time').value = '10:00';
        document.getElementById('activity_position').value = '';
        document.getElementById('activity_pic_nik').value = '';

        this.editingIndex = null;
    },

    hapusKegiatan(index) {
        if (confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')) {
            this.kegiatanRows.splice(index, 1);
        }
    },

    getRandomIcon() {
        const icons = ['📋', '👨‍🏫', '📦', '📊', '🎯', '💡', '📈', '🔧', '📝', '🎓', '🏆', '⭐'];
        return icons[Math.floor(Math.random() * icons.length)];
    },

    getRandomColor() {
        const colors = [
            'bg-red-50 text-red-600',
            'bg-orange-50 text-orange-600',
            'bg-green-50 text-green-600',
            'bg-purple-50 text-purple-600',
            'bg-blue-50 text-blue-600',
            'bg-yellow-50 text-yellow-600',
            'bg-pink-50 text-pink-600',
            'bg-indigo-50 text-indigo-600'
        ];
        return colors[Math.floor(Math.random() * colors.length)];
    },

    getInitials(name) {
        if (!name) return '?';
        const words = name.trim().split(' ');
        if (words.length === 1) {
            return words[0].charAt(0).toUpperCase();
        }
        const first = words[0].charAt(0).toUpperCase();
        const last = words[words.length - 1].charAt(0).toUpperCase();
        return first + last;
    },

    formatDate(date) {
        if (!date) return '-';
        const d = new Date(date);
        return d.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }
}"
    x-init="init()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-gray-800">Rincian Kegiatan Orientation</h2>
                <p class="text-xs text-gray-500" x-text="'Total ' + kegiatanRows.length + ' kegiatan'"></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button @click="editingIndex = null; resetActivityForm(); modalTambahBaris = true; $nextTick(() => { filterActivitiesByPlant(); })"
                class="inline-flex items-center gap-1.5 rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Baris
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-12 px-3 py-2.5 text-center text-xs font-semibold text-gray-600">#</th>
                    <th class="min-w-[200px] px-3 py-2.5 text-left text-xs font-semibold text-gray-600">Kegiatan</th>
                    <th class="w-40 px-3 py-2.5 text-left text-xs font-semibold text-gray-600">Tanggal</th>
                    <th class="w-44 px-3 py-2.5 text-left text-xs font-semibold text-gray-600">Waktu</th>
                    <th class="min-w-[180px] px-3 py-2.5 text-left text-xs font-semibold text-gray-600">PIC</th>
                    <th class="min-w-[150px] px-3 py-2.5 text-left text-xs font-semibold text-gray-600">Jabatan</th>
                    <th class="w-20 px-3 py-2.5 text-center text-xs font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-if="kegiatanRows.length === 0">
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <svg class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm text-gray-500">Belum ada kegiatan</p>
                            <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Baris" untuk menambahkan</p>
                        </td>
                    </tr>
                </template>
                <template x-for="(row, index) in kegiatanRows" :key="row.id">
                    <tr class="hover:bg-red-50/30 transition group">
                        <td class="px-3 py-3 text-center text-sm font-semibold text-gray-500"
                            x-text="String(index + 1).padStart(2, '0')"></td>

                        <td class="px-3 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl"
                                    :class="row.color || 'bg-gray-50 text-gray-600'" x-text="row.icon || '📋'">
                                </div>
                                <span class="text-sm font-medium text-gray-700" x-text="row.title"></span>
                            </div>
                        </td>

                        <td class="px-3 py-3">
                            <span class="text-sm text-gray-700" x-text="formatDate(row.tanggal)"></span>
                        </td>

                        <td class="px-3 py-3">
                            <span class="text-sm text-gray-700"
                                x-text="row.waktu_mulai + ' - ' + row.waktu_selesai"></span>
                        </td>

                        <td class="px-3 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-200 text-gray-700 text-xs font-semibold"
                                    x-text="getInitials(row.pic)">
                                </div>
                                <span class="text-sm text-gray-700" x-text="row.pic"></span>
                            </div>
                        </td>

                        <td class="px-3 py-3">
                            <span class="text-sm text-gray-700" x-text="row.jabatan || '-'"></span>
                        </td>

                        <td class="px-3 py-3">
                            <div class="flex justify-center gap-1">
                                <button @click="editKegiatan(index)"
                                    class="p-1.5 rounded-lg hover:bg-blue-50 text-gray-400 hover:text-blue-600 transition"
                                    title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button @click="hapusKegiatan(index)"
                                    class="p-1.5 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-600 transition"
                                    title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL TAMBAH BARIS --}}
    {{-- ========================================================= --}}

    {{-- Overlay --}}
    <div x-show="modalTambahBaris" x-cloak class="fixed inset-0 z-[999] bg-black/50 backdrop-blur-sm"
        @click="modalTambahBaris = false">
    </div>

    {{-- Modal --}}
    <div x-show="modalTambahBaris" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        class="fixed inset-0 z-[999] flex items-center justify-center p-4">

        <div class="w-full max-w-5xl max-h-[95vh] h-auto min-h-[80vh] flex flex-col rounded-2xl bg-white shadow-2xl"
            @click.stop>

            {{-- Modal Header --}}
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 bg-white rounded-t-2xl">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800" x-text="editingIndex === null ? 'Tambah Baris Kegiatan' : 'Edit Kegiatan'"></h3>
                        <p class="text-sm text-gray-500" x-text="editingIndex === null ? 'Isi data kegiatan baru di bawah ini' : 'Perbarui data kegiatan yang dipilih'"></p>
                    </div>
                </div>
                <button @click="modalTambahBaris = false" class="p-2 rounded-xl hover:bg-gray-100 transition">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 flex-1 overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Kegiatan - Filter berdasarkan plant --}}
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Nama Kegiatan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <button type="button"
                                @click="if (selectedPlant && filteredActivities.length) { activityDropdownOpen = !activityDropdownOpen; activitySearch = ''; }"
                                :disabled="!selectedPlant || filteredActivities.length === 0"
                                class="flex w-full items-center justify-between rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-left text-sm transition hover:bg-gray-50 focus:border-red-500 focus:ring-red-500 focus:ring-1 disabled:cursor-not-allowed disabled:bg-gray-100">
                                <span :class="selectedActivity ? 'text-gray-800' : 'text-gray-400'"
                                    x-text="selectedActivity ? selectedActivity.activity_name : (selectedPlant ? 'Pilih kegiatan...' : 'Pilih plant terlebih dahulu')"></span>
                                <svg class="h-4 w-4 text-gray-400 transition-transform" :class="activityDropdownOpen ? 'rotate-180' : ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />
                                </svg>
                            </button>

                            <div x-show="activityDropdownOpen" x-cloak @click.away="activityDropdownOpen = false"
                                class="absolute z-50 mt-1 w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl">
                                <div class="border-b border-gray-100 p-3">
                                    <input type="text" x-model="activitySearch" x-ref="activitySearchInput"
                                        x-init="$watch('activityDropdownOpen', value => { if (value) $nextTick(() => $refs.activitySearchInput.focus()) })"
                                        placeholder="Cari kegiatan..."
                                        class="w-full rounded-lg border-gray-200 px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500 focus:ring-1">
                                </div>
                                <div class="max-h-56 overflow-y-auto p-1">
                                    <template x-for="item in searchedActivities" :key="item.id">
                                        <button type="button" @click="selectActivity(item)"
                                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left transition hover:bg-red-50">
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-50 text-sm font-semibold text-red-600"
                                                x-text="(item.activity_name || '?').charAt(0).toUpperCase()"></span>
                                            <span class="flex-1">
                                                <span class="block text-sm font-medium text-gray-800" x-text="item.activity_name"></span>
                                                <span class="block text-xs text-gray-500" x-text="item.description || '-'"></span>
                                            </span>
                                        </button>
                                    </template>
                                    <p x-show="searchedActivities.length === 0" class="px-3 py-6 text-center text-sm text-gray-500">Kegiatan tidak ditemukan.</p>
                                </div>
                            </div>
                        </div>
                        <p x-show="selectedPlant && filteredActivities.length === 0"
                            class="mt-1 text-xs text-red-500">Tidak ada kegiatan untuk plant ini.</p>
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="activity_date"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500 focus:ring-1 focus:bg-white transition-all duration-200">
                    </div>

                    {{-- Waktu Mulai --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Waktu Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="activity_start_time"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500 focus:ring-1 focus:bg-white transition-all duration-200"
                            value="08:00">
                    </div>

                    {{-- Waktu Selesai --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Waktu Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="activity_end_time"
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 px-4 py-3 text-sm focus:border-red-500 focus:ring-red-500 focus:ring-1 focus:bg-white transition-all duration-200"
                            value="10:00">
                    </div>

                    {{-- PIC - Dari API get_users --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            PIC <span class="text-red-500">*</span>
                        </label>
                        <div class="relative" x-data="{
                            open: false,
                            selected: null,
                            search: '',
                            filteredItems: [],
                            init() {
                                const parent = Alpine.$data(document.getElementById('orientationActivities'));
                                this.filteredItems = parent.picData || [];
                            },
                        
                            filterItems() {
                                const parent = Alpine.$data(document.getElementById('orientationActivities'));
                                const data = parent.picData || [];
                        
                                if (!this.search.trim()) {
                                    this.filteredItems = data;
                                    return;
                                }
                        
                                const query = this.search.toLowerCase().trim();
                        
                                this.filteredItems = data.filter(item =>
                                    (item.nama ?? '').toLowerCase().includes(query) ||
                                    (item.nik ?? '').includes(query) ||
                                    (item.jabatan ?? '').toLowerCase().includes(query)
                                );
                        
                            },
                        
                            selectItem(item) {
                                const parent = Alpine.$data(document.getElementById('orientationActivities'));
                                this.selected = item;
                                this.search = item.nama;
                                this.open = false;
                        
                                parent.selectPic(item);
                        
                            }
                        }" x-init="
                            init();
                            const parent = Alpine.$data(document.getElementById('orientationActivities'));
                            $watch(() => parent.selectedPic, value => {
                                selected = value;
                                search = value?.nama || '';
                            });
                            $watch('open', value => {
                                if (value && !search && parent.selectedPic) {
                                    selected = parent.selectedPic;
                                    search = parent.selectedPic.nama || '';
                                }
                            });
                        ">
                            <div @click="open = !open; filterItems()" class="relative cursor-pointer">
                                <input type="text" x-model="search" readonly placeholder="Pilih PIC..."
                                    class="w-full rounded-xl border-gray-200 bg-gray-50/50 pl-4 pr-10 py-3 text-sm cursor-pointer hover:bg-gray-50 focus:border-red-500 focus:ring-red-500 focus:ring-1 focus:bg-white transition-all duration-200">
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="absolute z-50 mt-1 w-full rounded-xl border border-gray-200 bg-white shadow-2xl overflow-hidden"
                                @click.away="open = false">

                                <div
                                    class="flex items-center justify-between border-b border-gray-100 px-4 py-3 bg-gray-50/50">
                                    <span class="text-sm font-semibold text-gray-700">Pilih PIC</span>
                                    <button @click="open = false" class="p-1 rounded-lg hover:bg-gray-200 transition">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="p-3 border-b border-gray-100">
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-5.2-5.2m1.7-5.3a7 7 0 11-14 0a7 7 0 0114 0z" />
                                        </svg>
                                        <input type="text" x-model="search" @input="filterItems()"
                                            placeholder="Cari PIC (Nama/NIK/Jabatan)..."
                                            class="w-full rounded-lg border-gray-200 pl-9 pr-3 py-2 text-sm focus:border-red-500 focus:ring-red-500 focus:ring-1">
                                    </div>
                                </div>

                                <div class="max-h-52 overflow-y-auto p-1">
                                    <template x-for="item in filteredItems" :key="item.nik">
                                        <div @click="selectItem(item)"
                                            class="px-3 py-2.5 rounded-lg cursor-pointer hover:bg-red-50 flex items-center gap-3 transition"
                                            :class="selected?.nik === item.nik ? 'bg-red-50 text-red-600' : 'text-gray-700'">
                                            <div
                                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-200 text-gray-700 text-sm font-semibold">
                                                <span
                                                    x-text="item.nama ? item.nama.charAt(0).toUpperCase() : '?'"></span>
                                            </div>
                                            <div class="flex-1">
                                                <div class="text-sm font-medium"
                                                    x-text="item.nama || 'Tidak ada nama'"></div>
                                                <div class="text-xs text-gray-500">
                                                    <span x-text="item.nik || '-'"></span> • <span
                                                        x-text="item.jabatan || '-'"></span>
                                                </div>
                                            </div>
                                            <svg x-show="selected?.nik === item.nik" class="h-5 w-5 text-red-500"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </template>
                                    <div x-show="filteredItems.length === 0" class="px-3 py-8 text-center">
                                        <p class="text-sm text-gray-500">Tidak ada PIC ditemukan</p>
                                        <p class="text-xs text-gray-400 mt-1">Coba kata kunci lainnya</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="activity_pic_nik">
                    </div>

                    {{-- Jabatan - Read Only --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Jabatan
                        </label>
                        <input type="text" id="activity_position" readonly
                            class="w-full rounded-xl border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-600 cursor-not-allowed"
                            placeholder="Otomatis terisi dari PIC">
                    </div>

                </div>
            </div>

            {{-- Modal Footer --}}
            <div
                class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4 bg-gray-50/50 rounded-b-2xl mt-auto">
                <button @click="modalTambahBaris = false"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 rounded-xl hover:bg-gray-100 transition">
                    Batal
                </button>
                <button @click="tambahKegiatan()"
                    class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span x-text="editingIndex === null ? 'Tambahkan' : 'Simpan Perubahan'"></span>
                </button>
            </div>

        </div>
    </div>

</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
