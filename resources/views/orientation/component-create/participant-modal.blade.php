<div x-data="participantModal()" class="space-y-6">
    <!-- Peserta -->
    <div>
        <label class="mb-1.5 block text-xs font-semibold text-gray-700">Peserta Training <span
                class="text-red-500">*</span></label>
        <button type="button" @click="openModal()"
            class="flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 transition hover:border-red-300 hover:bg-red-50">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m10-10a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="text-left">
                    <div class="text-sm font-medium text-gray-800">Pilih Peserta</div>
                    <div class="text-xs text-gray-500">Bisa memilih lebih dari satu</div>
                </div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Selected Participants Chips -->
        <div x-show="selectedParticipants.length > 0" class="mt-3 flex flex-wrap gap-2">
            <template x-for="(participant, index) in selectedParticipants" :key="index">
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1.5 text-sm border border-red-100">
                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-red-200 text-red-700 text-xs font-semibold"
                        x-text="getInitials(participant.nama)">
                    </div>
                    <span class="text-sm font-medium text-gray-700"
                        x-text="participant.nama || participant.nik || 'Tanpa Nama'"></span>
                    <button @click="removeParticipant(index)"
                        class="ml-1 rounded-full p-0.5 hover:bg-red-100 transition">
                        <svg class="h-3.5 w-3.5 text-gray-400 hover:text-red-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>
            <span class="text-sm text-gray-500 font-medium"
                x-text="selectedParticipants.length + ' peserta dipilih'"></span>
        </div>

        <!-- Loading Indicator -->
        <div x-show="loading" class="mt-3 flex items-center gap-2 text-sm text-gray-500">
            <svg class="animate-spin h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            Memuat data peserta...
        </div>

        <!-- Error Message -->
        <div x-show="errorMessage" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            <span x-text="errorMessage"></span>
        </div>
    </div>

    <!-- Modal Pilih Peserta -->
    <div x-show="modalPeserta" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        class="fixed inset-0 z-[999] flex items-center justify-center p-4"
        style="background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px);" @click.away="closeModal()">

        <div class="w-full max-w-6xl h-auto max-h-[90vh] rounded-3xl bg-white shadow-2xl flex flex-col" @click.stop
            style="border-radius: 24px;">

            {{-- Modal Header - Fixed --}}
            <div
                class="flex items-center justify-between border-b border-gray-100 px-8 py-5 bg-white rounded-t-3xl flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl" style="background: #F8FAFC;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m10-10a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Pilih Peserta Training</h3>
                        <p class="text-xs text-gray-500">Semua karyawan (tanpa filter plant)</p>
                    </div>
                </div>
                <button @click="closeModal()" class="p-2 rounded-xl hover:bg-gray-100 transition">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body - Scrollable --}}
            <div class="flex-1 overflow-y-auto px-8 py-6">

                {{-- Aksi peserta: input manual & import Excel --}}
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <button type="button" @click="toggleManualForm()"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:border-red-300 hover:bg-red-50">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Manual
                    </button>

                    <button type="button" @click="$refs.importFile.click()" :disabled="importing"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:border-red-300 hover:bg-red-50 disabled:opacity-60">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                        <span x-text="importing ? 'Mengimport...' : 'Import Excel'"></span>
                    </button>
                    <input type="file" x-ref="importFile" class="hidden" accept=".xlsx,.xls,.csv"
                        @change="handleImportFile($event)">

                    <a href="{{ route('orientation.participants.template') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:border-red-300 hover:bg-red-50">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V3" />
                        </svg>
                        Download Template
                    </a>
                </div>

                {{-- Notifikasi import / manual --}}
                <p x-show="importMessage" class="mb-4 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-xs text-green-700"
                    x-text="importMessage"></p>
                <p x-show="importError" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700"
                    x-text="importError"></p>

                {{-- Form input manual (semua kolom opsional) --}}
                <div x-show="showManualForm" class="mb-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="mb-3 text-xs font-semibold text-gray-700">
                        Tambah Peserta Manual
                        <span class="font-normal text-gray-400">(semua kolom opsional)</span>
                    </p>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <input type="text" x-model="manualForm.nik" placeholder="NIK"
                            class="w-full rounded-lg border-gray-200 px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500">
                        <input type="text" x-model="manualForm.nama" placeholder="Nama"
                            class="w-full rounded-lg border-gray-200 px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500">
                        <input type="text" x-model="manualForm.jabatan" placeholder="Jabatan"
                            class="w-full rounded-lg border-gray-200 px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500">
                        <input type="text" x-model="manualForm.dept" placeholder="Dept"
                            class="w-full rounded-lg border-gray-200 px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <button type="button" @click="addManualParticipant()"
                            class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700">
                            Tambahkan
                        </button>
                        <button type="button" @click="cancelManualForm()"
                            class="rounded-lg px-3 py-2 text-xs font-medium text-gray-600 transition hover:bg-gray-200">
                            Batal
                        </button>
                    </div>
                </div>

                {{-- Peserta hasil input manual / import Excel --}}
                <div x-show="manualParticipants.length > 0" class="mb-4">
                    <p class="mb-2 text-xs font-semibold text-gray-700">
                        Peserta manual / import (<span x-text="manualParticipants.length"></span>)
                    </p>
                    <div class="space-y-2">
                        <template x-for="item in manualParticipants" :key="item._uid">
                            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-3 py-2">
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-medium text-gray-800"
                                        x-text="item.nama || item.nik || 'Tanpa Nama'"></p>
                                    <p class="truncate text-[11px] text-gray-400"
                                        x-text="[item.nik, item.jabatan, item.dept].filter(Boolean).join(' • ') || 'Data lain kosong'"></p>
                                </div>
                                <button type="button" @click="removeManualParticipant(item._uid)"
                                    class="rounded-lg p-1.5 text-gray-400 transition hover:bg-red-50 hover:text-red-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Search Input --}}
                <div class="mb-6">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-5.2-5.2m1.7-5.3a7 7 0 11-14 0a7 7 0 0114 0z" />
                        </svg>
                        <input type="text" x-model="searchPeserta" @input="filterPeserta()"
                            placeholder="Cari nama karyawan, NIK atau jabatan..."
                            class="w-full rounded-xl border-gray-200 pl-9 pr-4 py-2.5 text-sm focus:border-red-500 focus:ring-red-500 focus:ring-1">
                        <button x-show="searchPeserta" @click="clearSearch()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Selected Participants --}}
                <div x-show="selectedParticipants.length > 0" class="mb-6">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-gray-700"
                            x-text="selectedParticipants.length + ' karyawan dipilih'"></span>
                        <template x-for="(participant, index) in selectedParticipants" :key="index">
                            <div
                                class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-sm border border-red-100">
                                <div class="flex h-5 w-5 items-center justify-center rounded-full bg-red-200 text-red-700 text-[10px] font-semibold"
                                    x-text="getInitials(participant.nama)">
                                </div>
                                <span class="text-xs font-medium text-gray-700" x-text="participant.nama"></span>
                                <button @click="removeParticipant(index)"
                                    class="rounded-full p-0.5 hover:bg-red-100 transition">
                                    <svg class="h-3 w-3 text-gray-400 hover:text-red-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto border border-gray-200 rounded-2xl">
                    <table class="w-full min-w-[800px]">
                        <thead>
                            <tr class="bg-gradient-to-r from-red-50 to-red-100/50 border-b border-red-100">
                                <th class="w-12 px-4 py-3.5 text-center">
                                    <input type="checkbox" @change="toggleSelectAll()" x-model="selectAll"
                                        class="rounded border-gray-300 text-red-600 focus:ring-red-500 focus:ring-2 h-4 w-4 cursor-pointer">
                                </th>
                                <th
                                    class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-3.5 w-3.5 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Nama Karyawan
                                    </div>
                                </th>
                                <th
                                    class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-3.5 w-3.5 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                        </svg>
                                        NIK
                                    </div>
                                </th>
                                <th
                                    class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-3.5 w-3.5 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Jabatan
                                    </div>
                                </th>
                                <th
                                    class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-3.5 w-3.5 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Departemen
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="(item, index) in filteredPeserta" :key="index">
                                <tr @click="toggleParticipant(index)"
                                    class="cursor-pointer transition-all duration-150 hover:bg-red-50/30 group"
                                    :class="item.selected ? 'bg-red-50/40 hover:bg-red-50/60' : ''">
                                    <td class="px-4 py-3.5 text-center" @click.stop>
                                        <input type="checkbox" x-model="item.selected"
                                            @change="updateSelectedParticipants()"
                                            class="rounded border-gray-300 text-red-600 focus:ring-red-500 focus:ring-2 h-4 w-4 cursor-pointer transition-all"
                                            :class="item.selected ? 'scale-105' : ''">
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-semibold border-2 transition"
                                                :class="item.selected ? 'bg-red-500 text-white border-red-600' :
                                                    'bg-gray-200 text-gray-700 border-gray-100 group-hover:border-red-200'"
                                                x-text="getInitials(item.nama)">
                                            </div>
                                            <span class="text-sm font-medium text-gray-800" x-text="item.nama"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-sm text-gray-600 font-mono" x-text="item.nik">
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100"
                                            x-text="item.jabatan || '-'"></span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100"
                                            x-text="item.dept || '-'"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    {{-- No Results --}}
                    <div x-show="filteredPeserta.length === 0 && !loading" class="py-16 text-center bg-gray-50/30">
                        <svg class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-gray-500">Tidak ada peserta ditemukan</p>
                        <p class="text-xs text-gray-400 mt-1">Coba ubah kata kunci pencarian</p>
                    </div>

                    {{-- Loading in table --}}
                    <div x-show="loading" class="py-16 text-center bg-gray-50/30">
                        <svg class="animate-spin h-12 w-12 mx-auto text-red-600 mb-4"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <p class="text-sm text-gray-500">Memuat data peserta...</p>
                    </div>
                </div>

                {{-- Pagination --}}
                <div
                    class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 pt-4 border-t border-gray-100">
                    <div class="text-sm text-gray-600">
                        Menampilkan <span class="font-medium text-gray-800" x-text="filteredPeserta.length"></span>
                        dari <span class="font-medium text-gray-800" x-text="pesertaData.length"></span> data
                    </div>
                    <div class="flex items-center gap-4">
                        <button @click="currentPage > 1 && changePage(currentPage - 1)"
                            class="px-4 py-2 text-sm font-medium text-gray-600 rounded-xl hover:bg-gray-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="currentPage === 1">
                            <span class="flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Sebelumnya
                            </span>
                        </button>
                        <span class="text-sm text-gray-600">Halaman <span class="font-medium text-gray-800"
                                x-text="currentPage"></span>
                            dari <span class="font-medium text-gray-800" x-text="totalPages"></span></span>
                        <button @click="currentPage < totalPages && changePage(currentPage + 1)"
                            class="px-4 py-2 text-sm font-medium text-gray-600 rounded-xl hover:bg-gray-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="currentPage === totalPages">
                            <span class="flex items-center gap-1">
                                Selanjutnya
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Modal Footer - Fixed --}}
            <div
                class="flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-gray-100 px-8 py-4 bg-gray-50/50 rounded-b-3xl flex-shrink-0">
                <button @click="clearAllParticipants"
                    class="text-sm font-medium text-red-600 hover:text-red-700 transition hover:underline">
                    Bersihkan Semua
                </button>
                <div class="flex items-center gap-3">
                    <button @click="closeModal()"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 rounded-xl hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button @click="saveParticipants"
                        class="inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:bg-red-700 active:scale-95"
                        style="background: #D71920;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan (<span class="font-bold" x-text="selectedParticipants.length"></span>)
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('participantModal', () => ({
            modalPeserta: false,
            searchPeserta: '',
            selectAll: false,
            currentPage: 1,
            pageSize: 10,
            pesertaData: [],
            selectedParticipants: [],
            initialParticipants: @json($initialParticipants ?? []),
            initialParticipantNiks: [],
            manualParticipants: [],
            manualPrimed: false,
            showManualForm: false,
            manualForm: { nik: '', nama: '', jabatan: '', dept: '' },
            importing: false,
            importMessage: '',
            importError: '',
            loading: false,
            errorMessage: '',
            selectedPlant: null,

            // Watch for plant selection from dropdown
            init() {
                this.initialParticipantNiks = this.initialParticipants.map(participant =>
                    String(typeof participant === 'object' ? participant.nik : participant)
                );
                this.selectedParticipants = this.initialParticipants.map(participant => {
                    if (typeof participant === 'object') {
                        return {
                            nik: String(participant.nik || ''),
                            nama: participant.nama || String(participant.nik || ''),
                            jabatan: participant.jabatan || '',
                            dept: participant.dept || ''
                        };
                    }

                    return {
                        nik: String(participant),
                        nama: String(participant),
                        jabatan: '',
                        dept: ''
                    };
                });

                // Hapus setInterval plant watch
                // Tidak ada reset peserta saat plant berubah
            },

            async initParticipants() {
                await this.fetchParticipants();
            },

            async openModal() {
                // Tidak perlu cek plant lagi
                this.searchPeserta = '';
                this.currentPage = 1;
                this.selectAll = false;

                // Jika belum ada data, fetch dari API
                if (this.pesertaData.length === 0) {
                    await this.fetchParticipants();
                }

                this.primeManualParticipants();

                this.modalPeserta = true;
            },

            closeModal() {
                this.modalPeserta = false;
                // Reset search when closing modal
                this.searchPeserta = '';
                this.currentPage = 1;
                this.selectAll = false;
            },

            async fetchParticipants() {
                this.loading = true;
                this.errorMessage = '';

                try {
                    // Tidak pakai parameter plant lagi
                    const url = 'https://web.kobin.co.id/api/attendance/live/api_get_users.php';

                    const response = await fetch(url);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success && result.data) {
                        const selectedNiks = this.initialParticipantNiks.map(String);
                        this.pesertaData = result.data.map(user => ({
                            nik: user.nik || '',
                            nama: user.nama || '',
                            email: user.email || '',
                            level: user.level || '',
                            plant: user.plant || '',
                            comp: user.comp || '',
                            tglmasuk: user.tglmasuk || '',
                            divisi: user.divisi || '',
                            dept: user.dept || '',
                            jabatan: user.jabatan || '',
                            kode_jabatan: user.kode_jabatan || '',
                            role: user.role || '',
                            selected: selectedNiks.includes(String(user.nik))
                        }));

                        this.updateSelectedParticipants();
                    } else {
                        throw new Error(result.message || 'Gagal mengambil data peserta');
                    }
                } catch (error) {
                    console.error('Error fetching participants:', error);
                    this.errorMessage = `Gagal memuat data: ${error.message}`;
                    this.pesertaData = [];
                } finally {
                    this.loading = false;
                }
            },

            get filteredPeserta() {
                let filtered = this.pesertaData.filter(item => {
                    const searchMatch = this.searchPeserta === '' ||
                        (item.nama && item.nama.toLowerCase().includes(this
                            .searchPeserta.toLowerCase())) ||
                        (item.nik && item.nik.includes(this.searchPeserta)) ||
                        (item.jabatan && item.jabatan.toLowerCase().includes(this
                            .searchPeserta.toLowerCase())) ||
                        (item.dept && item.dept.toLowerCase().includes(this
                            .searchPeserta.toLowerCase()));

                    return searchMatch;
                });

                // Pagination
                const start = (this.currentPage - 1) * this.pageSize;
                const end = start + this.pageSize;
                return filtered.slice(start, end);
            },

            get totalPages() {
                const filtered = this.pesertaData.filter(item => {
                    const searchMatch = this.searchPeserta === '' ||
                        (item.nama && item.nama.toLowerCase().includes(this
                            .searchPeserta.toLowerCase())) ||
                        (item.nik && item.nik.includes(this.searchPeserta)) ||
                        (item.jabatan && item.jabatan.toLowerCase().includes(this
                            .searchPeserta.toLowerCase())) ||
                        (item.dept && item.dept.toLowerCase().includes(this
                            .searchPeserta.toLowerCase()));

                    return searchMatch;
                });
                return Math.ceil(filtered.length / this.pageSize) || 1;
            },

            filterPeserta() {
                this.currentPage = 1;
            },

            clearSearch() {
                this.searchPeserta = '';
                this.filterPeserta();
            },

            changePage(page) {
                this.currentPage = page;
            },

            toggleParticipant(index) {
                const item = this.filteredPeserta[index];
                if (item) {
                    const originalItem = this.pesertaData.find(p => p.nik === item.nik);
                    if (originalItem) {
                        originalItem.selected = !originalItem.selected;
                    }
                    this.updateSelectedParticipants();
                }
            },

            toggleSelectAll() {
                this.filteredPeserta.forEach(item => {
                    const originalItem = this.pesertaData.find(p => p.nik === item.nik);
                    if (originalItem) {
                        originalItem.selected = this.selectAll;
                    }
                });
                this.updateSelectedParticipants();
            },

            updateSelectedParticipants() {
                const merged = [];
                const seenNiks = new Set();

                this.pesertaData.filter(p => p.selected).forEach(p => {
                    const nik = p.nik ? String(p.nik) : '';
                    if (nik) seenNiks.add(nik);
                    merged.push({
                        nik: nik,
                        nama: p.nama || '',
                        jabatan: p.jabatan || '',
                        dept: p.dept || '',
                        _manual: false,
                        _uid: nik ? 'api-' + nik : this.makeUid(),
                    });
                });

                this.manualParticipants.forEach(p => {
                    const nik = p.nik ? String(p.nik) : '';
                    if (nik && seenNiks.has(nik)) return;
                    if (nik) seenNiks.add(nik);
                    merged.push({ ...p, nik: nik });
                });

                this.selectedParticipants = merged;
            },

            removeParticipant(index) {
                const participant = this.selectedParticipants[index];
                if (!participant) return;

                if (participant._manual) {
                    this.removeManualParticipant(participant._uid);
                    return;
                }

                const nik = String(participant.nik || '');
                const originalItem = this.pesertaData.find(p => String(p.nik || '') === nik);
                if (originalItem) {
                    originalItem.selected = false;
                }

                this.updateSelectedParticipants();
            },

            removeManualParticipant(uid) {
                this.manualParticipants = this.manualParticipants.filter(p => p._uid !== uid);
                this.updateSelectedParticipants();
            },

            clearAllParticipants() {
                this.pesertaData.forEach(item => {
                    item.selected = false;
                });
                this.manualParticipants = [];
                this.updateSelectedParticipants();
                this.selectAll = false;
            },

            makeUid() {
                return 'p-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 8);
            },

            /**
             * Peserta lama yang tidak ada di data karyawan (mis. hasil import)
             * tetap ditampilkan sebagai peserta manual.
             */
            primeManualParticipants() {
                if (this.manualPrimed) return;
                this.manualPrimed = true;

                const apiNiks = new Set(this.pesertaData.map(p => String(p.nik || '')));

                this.manualParticipants = this.initialParticipants
                    .map(p => (typeof p === 'object' && p !== null) ? p : { nik: String(p), nama: String(p) })
                    .filter(p => {
                        const nik = p.nik ? String(p.nik) : '';
                        return !nik || !apiNiks.has(nik);
                    })
                    .map(p => ({
                        nik: p.nik ? String(p.nik) : '',
                        nama: p.nama || '',
                        jabatan: p.jabatan || '',
                        dept: p.dept || '',
                        _manual: true,
                        _uid: this.makeUid(),
                    }));

                this.updateSelectedParticipants();
            },

            toggleManualForm() {
                this.showManualForm = !this.showManualForm;
                this.importMessage = '';
                this.importError = '';
            },

            cancelManualForm() {
                this.showManualForm = false;
                this.manualForm = { nik: '', nama: '', jabatan: '', dept: '' };
            },

            addManualParticipant() {
                const row = {
                    nik: (this.manualForm.nik || '').trim(),
                    nama: (this.manualForm.nama || '').trim(),
                    jabatan: (this.manualForm.jabatan || '').trim(),
                    dept: (this.manualForm.dept || '').trim(),
                };

                if (!row.nik && !row.nama && !row.jabatan && !row.dept) {
                    this.importError = 'Isi minimal salah satu data peserta.';
                    return;
                }

                const apiItem = row.nik
                    ? this.pesertaData.find(p => String(p.nik || '') === row.nik)
                    : null;

                if (apiItem) {
                    apiItem.selected = true;
                } else if (!row.nik || !this.manualParticipants.some(p => p.nik === row.nik)) {
                    this.manualParticipants.push({ ...row, _manual: true, _uid: this.makeUid() });
                }

                this.importError = '';
                this.importMessage = 'Peserta manual ditambahkan.';
                this.cancelManualForm();
                this.updateSelectedParticipants();
            },

            async handleImportFile(event) {
                const file = event.target.files && event.target.files[0];
                if (!file) return;

                this.importing = true;
                this.importError = '';
                this.importMessage = '';

                try {
                    const formData = new FormData();
                    formData.append('file', file);

                    const response = await fetch('{{ route('orientation.import.participants') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        const errors = result.errors ? Object.values(result.errors).flat().join(' ') : null;
                        throw new Error(errors || result.message || 'Gagal mengimport file.');
                    }

                    const existingNiks = new Set(
                        this.selectedParticipants
                            .map(p => String(p.nik || ''))
                            .filter(Boolean)
                    );

                    let added = 0;
                    let duplicate = 0;

                    (result.participants || []).forEach(row => {
                        const nik = row.nik ? String(row.nik) : '';

                        if (nik && existingNiks.has(nik)) {
                            duplicate++;
                            return;
                        }
                        if (nik) existingNiks.add(nik);

                        const apiItem = nik
                            ? this.pesertaData.find(p => String(p.nik || '') === nik)
                            : null;

                        if (apiItem) {
                            apiItem.selected = true;
                        } else {
                            this.manualParticipants.push({
                                nik: nik,
                                nama: row.nama || '',
                                jabatan: row.jabatan || '',
                                dept: row.dept || '',
                                _manual: true,
                                _uid: this.makeUid(),
                            });
                        }

                        added++;
                    });

                    this.updateSelectedParticipants();

                    this.importMessage = added > 0
                        ? added + ' peserta ditambahkan' + (duplicate > 0 ? ', ' + duplicate + ' duplikat dilewati.' : '.')
                        : (result.message || 'Tidak ada peserta baru pada file.');
                } catch (error) {
                    this.importError = error.message || 'Gagal mengimport file.';
                } finally {
                    this.importing = false;
                    event.target.value = '';
                }
            },

            saveParticipants() {
                this.modalPeserta = false;
                this.updateSelectedParticipants();

                // Store selected participants globally
                window.selectedParticipants = this.selectedParticipants;

                console.log('Selected participants:', this.selectedParticipants);
            },

            // Method to refresh data manually (can be called when plant changes)
            refreshData() {
                this.pesertaData = [];
                this.selectedParticipants = [];
                this.errorMessage = '';
                this.fetchParticipants();
            },

            // Helper function to get initials from name
            getInitials(name) {
                if (!name) return '?';
                const words = name.trim().split(' ');
                if (words.length === 1) {
                    return words[0].charAt(0).toUpperCase();
                }
                // Take first letter of first and last word
                const first = words[0].charAt(0).toUpperCase();
                const last = words[words.length - 1].charAt(0).toUpperCase();
                return first + last;
            }
        }));
    });
</script>
