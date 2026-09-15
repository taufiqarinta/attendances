<x-app-layout>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="min-h-screen bg-[#F4F5F7] py-4">

        <div class="mx-auto max-w-7xl px-4">

            {{-- ========================================================= --}}
            {{-- HERO --}}
            {{-- ========================================================= --}}

            <div
                class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#7A1113] via-[#9B1F14] to-[#D71920] shadow-lg">

                <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/10"></div>
                <div class="absolute right-20 bottom-0 h-28 w-28 rounded-full bg-white/10"></div>

                <div class="relative flex items-center gap-4 px-6 py-5">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/10 backdrop-blur">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Form Create Orientation</h1>
                        <p class="text-sm text-red-100">Buat sesi orientasi baru untuk karyawan.</p>
                    </div>
                </div>

            </div>

            {{-- ========================================================= --}}
            {{-- FORM SELECTION --}}
            {{-- ========================================================= --}}

            <div class="mt-4 rounded-2xl border border-gray-100 bg-white shadow-sm">

                <div class="p-5">

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        {{-- Kategori --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700">Kategori <span
                                    class="text-red-500">*</span></label>
                            <select id="categorySelect"
                                class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:outline-none">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- HR PIC (Searchable Dropdown) --}}
                        <div x-data="hrPicDropdown()" x-init="initHrPics({{ json_encode($hrPics ?? []) }})" class="relative">
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700">
                                HR PIC <span class="text-red-500">*</span>
                            </label>

                            {{-- Tombol Dropdown --}}
                            <button type="button" @click="toggleDropdown()"
                                class="flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 transition hover:border-red-300 hover:bg-red-50"
                                :class="isOpen ? 'border-red-400 ring-2 ring-red-100' : ''">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm font-medium text-gray-800"
                                            x-text="selectedHrPic ? selectedHrPic.nama : 'Pilih HR PIC'"></div>
                                        <div class="text-xs text-gray-500"
                                            x-text="selectedHrPic ? 'NIK: ' + selectedHrPic.nik + (selectedHrPic.jabatan ? ' • ' + selectedHrPic.jabatan : '') : 'Pilih HR PIC yang tersedia'">
                                        </div>
                                    </div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-gray-400 transition-transform duration-200"
                                    :class="isOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Dropdown --}}
                            <div x-show="isOpen" @click.away="closeDropdown()"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                                x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                                class="absolute left-0 right-0 z-50 mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden"
                                style="box-shadow: 0 20px 60px rgba(0,0,0,0.15);">

                                {{-- Search Input --}}
                                <div class="p-3 border-b border-gray-100">
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-5.2-5.2m1.7-5.3a7 7 0 11-14 0a7 7 0 0114 0z" />
                                        </svg>
                                        <input type="text" x-model="searchQuery" @input="filterHrPics()"
                                            placeholder="Cari nama, NIK, atau jabatan..."
                                            class="w-full rounded-lg border border-gray-200 pl-9 pr-4 py-2 text-sm focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:outline-none transition">
                                        <button x-show="searchQuery" @click="clearSearch()"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- List HR PIC --}}
                                <div class="max-h-60 overflow-y-auto">
                                    <template x-for="(hr, index) in filteredHrPics" :key="index">
                                        <button @click="selectHrPic(hr)"
                                            class="flex w-full items-center gap-3 px-4 py-3 hover:bg-red-50 transition group"
                                            :class="selectedHrPic && selectedHrPic.nik === hr.nik ? 'bg-red-50/50' : ''">
                                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full text-sm font-semibold"
                                                :class="selectedHrPic && selectedHrPic.nik === hr.nik ?
                                                    'bg-red-500 text-white' :
                                                    'bg-gray-100 text-gray-600 group-hover:bg-red-100 group-hover:text-red-600'">
                                                <span x-text="getInitials(hr.nama)"></span>
                                            </div>
                                            <div class="flex-1 text-left">
                                                <div class="text-sm font-medium text-gray-800" x-text="hr.nama"></div>
                                                <div class="text-xs text-gray-500">
                                                    <span x-text="'NIK: ' + hr.nik"></span>
                                                    <span x-show="hr.jabatan" x-text="' • ' + hr.jabatan"></span>
                                                </div>
                                            </div>
                                            <svg x-show="selectedHrPic && selectedHrPic.nik === hr.nik"
                                                class="h-5 w-5 text-red-600 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </template>

                                    {{-- No Results --}}
                                    <div x-show="filteredHrPics.length === 0" class="px-4 py-8 text-center">
                                        <svg class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-sm text-gray-500">Tidak ada HR PIC ditemukan</p>
                                        <p class="text-xs text-gray-400 mt-1">Coba kata kunci lainnya</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Script HR PIC Dropdown --}}
                        <script>
                            function hrPicDropdown() {
                                return {
                                    isOpen: false,
                                    searchQuery: '',
                                    selectedHrPic: null,
                                    hrPics: [],
                                    filteredHrPics: [],

                                    initHrPics(hrPics) {
                                        this.hrPics = hrPics;
                                        this.filteredHrPics = hrPics;
                                    },

                                    toggleDropdown() {
                                        this.isOpen = !this.isOpen;
                                        if (this.isOpen) {
                                            this.filteredHrPics = this.hrPics;
                                            this.searchQuery = '';
                                        }
                                    },

                                    closeDropdown() {
                                        this.isOpen = false;
                                    },

                                    filterHrPics() {
                                        if (!this.searchQuery.trim()) {
                                            this.filteredHrPics = this.hrPics;
                                            return;
                                        }

                                        const query = this.searchQuery.toLowerCase().trim();
                                        this.filteredHrPics = this.hrPics.filter(hr =>
                                            (hr.nama && hr.nama.toLowerCase().includes(query)) ||
                                            (hr.nik && String(hr.nik).toLowerCase().includes(query)) ||
                                            (hr.jabatan && hr.jabatan.toLowerCase().includes(query)) ||
                                            (hr.dept && hr.dept.toLowerCase().includes(query))
                                        );
                                    },

                                    clearSearch() {
                                        this.searchQuery = '';
                                        this.filterHrPics();
                                    },

                                    selectHrPic(hr) {
                                        this.selectedHrPic = hr;
                                        this.isOpen = false;
                                        console.log('HR PIC selected:', hr);
                                        // Simpan ke global variable untuk akses di submit
                                        window.selectedHrPic = hr;
                                        window.selectedHrPicNik = hr.nik;
                                        window.dispatchEvent(new CustomEvent('orientation-hr-pic-selected', {
                                            detail: hr
                                        }));
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
                                    }
                                }
                            }
                        </script>

                        {{-- Plant Dropdown --}}
                        @include('orientation.component-create.dropdwon-plant')

                        <!-- Peserta -->
                        @include('orientation.component-create.participant-modal')

                    </div>

                </div>

            </div>

            {{-- ========================================================= --}}
            {{-- RINCIAN KEGIATAN --}}
            {{-- ========================================================= --}}

            @include('orientation.component-create.activity-list')


            {{-- ========================================================= --}}
            {{-- INFORMATION & CHECKLIST --}}
            {{-- ========================================================= --}}

            @include('orientation.component-create.information')

            {{-- FOOTER --}}
            {{-- ========================================================= --}}

            @include('orientation.component-create.footer')

        </div>

    </div>

    {{-- JavaScript untuk submit form --}}
    <script>
        async function submitOrientation() {
            const plantId = window.selectedPlantId;
            const categoryId = document.getElementById('categorySelect')?.value;
            const hrPic = window.selectedHrPic || null;
            const hrPicNik = hrPic?.nik;

            if (!categoryId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih Kategori terlebih dahulu',
                    confirmButtonColor: '#dc2626'
                });
                return;
            }

            if (!plantId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih Plant terlebih dahulu',
                    confirmButtonColor: '#dc2626'
                });
                return;
            }

            if (!hrPicNik) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih HR PIC terlebih dahulu',
                    confirmButtonColor: '#dc2626'
                });
                return;
            }

            const participantElement = document.querySelector('[x-data="participantModal()"]');
            const activityElement = document.getElementById('orientationActivities');
            const participants = Alpine.$data(participantElement)?.selectedParticipants || [];
            const activities = Alpine.$data(activityElement)?.kegiatanRows || [];

            if (participants.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih minimal 1 peserta',
                    confirmButtonColor: '#dc2626'
                });
                return;
            }

            if (activities.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan tambahkan minimal 1 kegiatan',
                    confirmButtonColor: '#dc2626'
                });
                return;
            }

            const hrPicPayload = [{
                nik: hrPicNik,
                nama: hrPic.nama || hrPicNik,
                jabatan: hrPic.jabatan || null,
                dept: hrPic.dept || null,
            }];

            const submitButton = document.querySelector('[onclick="submitOrientation()"]');
            submitButton.disabled = true;
            submitButton.classList.add('opacity-60', 'cursor-not-allowed');

            try {
                const response = await fetch(@json(route('orientation.store')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        category_id: categoryId,
                        plant_id: plantId,
                        hr_pic: hrPicPayload,
                        participants: participants.map(participant => ({
                            nik: String(participant.nik),
                            nama: participant.nama || String(participant.nik),
                            jabatan: participant.jabatan || null,
                            dept: participant.dept || null
                        })),
                        activities: activities
                    })
                });
                const result = await response.json();

                if (!response.ok) {
                    const errors = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                    throw new Error(errors || 'Gagal menyimpan orientation.');
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Orientation berhasil dibuat',
                    text: result.message || 'Data orientation dan kegiatan berhasil disimpan.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc2626',
                    allowOutsideClick: false
                });

                window.location.assign(result.redirect);
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message || 'Gagal menyimpan orientation.',
                    confirmButtonColor: '#dc2626'
                });
            } finally {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        }
    </script>

</x-app-layout>
