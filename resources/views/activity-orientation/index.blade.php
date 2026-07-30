<x-app-layout>
    {{-- CDN untuk testing --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="min-h-screen bg-[#F4F5F7] py-6">
        <div class="max-w-[1440px] mx-auto px-6">

            {{-- ===================================================== --}}
            {{-- HERO --}}
            {{-- ===================================================== --}}
            <div
                class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-red-900 via-red-800 to-red-600 shadow-lg mb-6">
                <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10"></div>
                <div class="absolute right-10 bottom-0 h-20 w-20 rounded-full bg-white/10"></div>

                <div class="relative flex items-center gap-4 px-6 py-5">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/10 backdrop-blur">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Master Kegiatan</h1>
                        <p class="text-sm text-red-100">Kelola master kegiatan orientation.</p>
                    </div>
                </div>
            </div>

            {{-- ===================================================== --}}
            {{-- CONTENT --}}
            {{-- ===================================================== --}}
            <div class="rounded-2xl bg-white shadow-sm border border-gray-200">

                {{-- Toolbar --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4">

                    {{-- Search --}}
                    <div class="relative w-full sm:max-w-md">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                        </svg>
                        <input type="text" id="searchInput" placeholder="Cari kode atau kegiatan..."
                            class="h-10 w-full rounded-xl border border-gray-200 bg-white pl-10 pr-3 text-sm focus:border-red-500 focus:ring-red-500"
                            onkeyup="searchActivities()">
                    </div>

                    {{-- Right Action --}}
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <select id="statusFilter"
                            class="h-10 w-full sm:w-56 rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-700
               focus:border-red-500 focus:ring-red-500"
                            onchange="filterActivities()">
                            <option value="all">Status : Semua</option>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>

                        <button onclick="openModal('create')"
                            class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-red-600 px-4 text-sm font-semibold text-white transition hover:bg-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Kegiatan
                        </button>
                    </div>
                </div>

                {{-- ===================================================== --}}
                {{-- TABLE --}}
                {{-- ===================================================== --}}
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[768px]" id="activityTable">
                        <thead class="bg-slate-50/80 border-y border-gray-200">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">
                                    No</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">
                                    Kode</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[150px]">
                                    Nama Kegiatan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell min-w-[200px]">
                                    Deskripsi</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell min-w-[180px]">
                                    Plant</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white" id="activityTableBody">
                            @php $no = $activities->firstItem() ?? 1; @endphp
                            @forelse($activities as $activity)
                                <tr class="hover:bg-slate-50/60 transition-colors duration-150 activity-row group"
                                    data-status="{{ $activity->status }}"
                                    data-name="{{ strtolower($activity->activity_name) }}"
                                    data-code="{{ strtolower($activity->code_activity ?? '') }}"
                                    data-id="{{ $activity->id }}">

                                    <td class="px-4 py-3.5 text-sm text-gray-500 text-center">
                                        <span
                                            class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-xs font-semibold text-gray-600">
                                            {{ $no++ }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3.5 text-sm font-semibold text-red-700">
                                        {{ $activity->code_activity ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="flex-shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-sm text-gray-800">
                                                    {{ $activity->activity_name }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3.5 text-sm text-gray-500 hidden md:table-cell">
                                        <div class="max-w-xs truncate">
                                            {{ $activity->description ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3.5 hidden lg:table-cell">
                                        @php
                                            $plantNames = $activity->plant_names ?? collect();
                                            $plantCount = $plantNames->count();
                                            $maxDisplay = 3;
                                        @endphp

                                        @if ($plantCount > 0)
                                            <div class="flex flex-wrap items-center gap-1">
                                                @foreach ($plantNames->take($maxDisplay) as $plant)
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-slate-200 transition-colors">
                                                        {{ Str::limit($plant->name_plant, 12) }}
                                                    </span>
                                                @endforeach

                                                @if ($plantCount > $maxDisplay)
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600 cursor-help hover:bg-blue-100 transition-colors"
                                                        title="{{ $plantNames->pluck('name_plant')->implode(', ') }}">
                                                        +{{ $plantCount - $maxDisplay }} lagi
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3.5">
                                        @if ($activity->status == 1)
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 border border-emerald-200/50">
                                                <span class="relative flex h-2 w-2">
                                                    <span
                                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                    <span
                                                        class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                                </span>
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700 border border-red-200/50">
                                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center justify-center gap-1">
                                            <button onclick="openModal('edit', {{ $activity->id }})"
                                                class="p-1.5 rounded-lg hover:bg-blue-50 transition-all duration-200 group/edit"
                                                title="Edit Kegiatan">
                                                <svg class="w-4 h-4 text-gray-400 group-hover/edit:text-blue-600 transition-colors"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button onclick="confirmDelete({{ $activity->id }})"
                                                class="p-1.5 rounded-lg hover:bg-red-50 transition-all duration-200 group/delete"
                                                title="Hapus Kegiatan">
                                                <svg class="w-4 h-4 text-gray-400 group-hover/delete:text-red-600 transition-colors"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-16 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div
                                                class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center">
                                                <svg class="w-10 h-10 text-slate-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-base font-semibold text-gray-700">Belum ada data
                                                    kegiatan</p>
                                                <p class="text-sm text-gray-400 mt-1">Klik tombol <span
                                                        class="font-medium text-gray-600">"Tambah Kegiatan"</span>
                                                    untuk menambahkan data</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- ===================================================== --}}
                {{-- FOOTER TABLE WITH PAGINATION --}}
                {{-- ===================================================== --}}
                <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                    <div class="flex justify-center lg:justify-end">
                        <div class="pagination-wrapper">
                            {{ $activities->appends(request()->query())->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- MODAL CREATE / EDIT --}}
    {{-- ===================================================== --}}
    <div id="modalOverlay"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] hidden items-center justify-center p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto animate-scale-up relative">
            <div
                class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
                <h3 id="modalTitle" class="text-lg font-bold text-gray-800">Tambah Kegiatan</h3>
                <button onclick="closeModal()" class="p-1.5 rounded-lg hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="modalForm" class="p-6 space-y-4" action="{{ route('orientation.master-activity.store') }}"
                method="POST">
                @csrf
                <input type="hidden" id="formId" name="id" value="">
                <input type="hidden" name="_method" id="formMethod" value="POST">

                {{-- Nama Kegiatan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kegiatan <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="formNama" name="activity_name"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-red-500 focus:ring-red-500 focus:outline-none"
                        placeholder="Masukkan nama kegiatan" required>
                </div>

                {{-- Deskripsi Kegiatan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi Kegiatan</label>
                    <textarea id="formDeskripsi" name="description" rows="3"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-red-500 focus:ring-red-500 focus:outline-none"
                        placeholder="Masukkan deskripsi kegiatan"></textarea>
                </div>

                {{-- Plant (Multiple Select) --}}
                <div class="relative" style="z-index: 100;">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Plant <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <div id="plantDropdown"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm bg-white cursor-pointer focus:border-red-500 focus:ring-red-500 focus:outline-none"
                            onclick="togglePlantDropdown()">
                            <div id="selectedPlants" class="flex flex-wrap gap-1.5 min-h-[24px] pr-6">
                                <span class="text-gray-400 text-sm" id="placeholderText">Pilih Plant</span>
                            </div>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="formPlant" name="plant_ids" value="">
                    <p class="text-xs text-gray-500 mt-1">* Pilih satu atau lebih plant</p>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5
                    ">Status <span
                            class="text-red-500">*</span></label>
                    <select id="formStatus" name="status"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-red-500 focus:ring-red-500 focus:outline-none"
                        required>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="flex gap-3 pt-2 sticky bottom-0 bg-white py-3 border-t border-gray-100 -mx-6 px-6">
                    <button type="button" onclick="closeModal()"
                        class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- PLANT DROPDOWN (DI LUAR MODAL) --}}
    {{-- ===================================================== --}}
    <div id="plantOptionsContainer"
        class="hidden fixed z-[99999] bg-white border border-gray-200 rounded-xl shadow-2xl max-h-56 overflow-y-auto"
        style="min-width: 200px;">
        <div class="sticky top-0 bg-white border-b border-gray-100 px-4 py-2">
            <span class="text-xs font-semibold text-gray-500">PILIH PLANT</span>
        </div>
        @foreach ($plants as $plant)
            <label
                class="flex items-center gap-2 px-4 py-2.5 hover:bg-red-50 cursor-pointer border-b border-gray-100 transition">
                <input type="checkbox" value="{{ $plant->id }}"
                    class="plant-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500"
                    onchange="updateSelectedPlants()">
                <span class="text-sm text-gray-700">{{ $plant->name_plant }}</span>
            </label>
        @endforeach
    </div>

    {{-- ===================================================== --}}
    {{-- STYLE ANIMASI --}}
    {{-- ===================================================== --}}
    <style>
        .animate-scale-up {
            animation: scaleUp 0.2s ease-out;
        }

        @keyframes scaleUp {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
    <style>
        /* =======================================================
   PAGINATION LARAVEL TAILWIND
======================================================= */

        .pagination-wrapper nav {
            display: flex;
            justify-content: center;
            margin-top: 8px;
            background: transparent;
        }

        .pagination-wrapper nav>div:first-child {
            display: none;
            /* Sembunyikan "Showing x to y..." */
        }

        .pagination-wrapper nav>div:last-child {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pagination-wrapper a,
        .pagination-wrapper span[aria-current="page"] span,
        .pagination-wrapper span[aria-disabled="true"] span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 12px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #374151;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all .2s ease;
        }

        .pagination-wrapper a:hover {
            background: #f9fafb;
            color: #dc2626;
            border-color: #dc2626;
        }

        .pagination-wrapper span[aria-current="page"] span {
            background: #dc2626;
            color: #ffffff;
            border-color: #dc2626;
        }

        .pagination-wrapper span[aria-disabled="true"] span {
            color: #9ca3af;
            background: #f9fafb;
            cursor: not-allowed;
        }

        .pagination-wrapper svg {
            width: 18px;
            height: 18px;
        }

        /* Mobile */
        @media (max-width:640px) {

            .pagination-wrapper nav {
                justify-content: center;
            }

            .pagination-wrapper a,
            .pagination-wrapper span[aria-current="page"] span,
            .pagination-wrapper span[aria-disabled="true"] span {
                min-width: 34px;
                height: 34px;
                padding: 0 10px;
                font-size: 13px;
            }

            .pagination-wrapper nav>div:last-child {
                gap: 4px;
            }
        }
    </style>

    <script>
        // =====================================================
        // STATE MANAGEMENT
        // =====================================================
        const state = {
            isPlantDropdownOpen: false,
            currentPage: 1,
            rowsPerPage: parseInt(document.getElementById('perPage')?.value || 10),
            currentSearch: '',
            currentStatus: 'all',
            isModalOpen: false,
            isEditMode: false,
            editId: null
        };

        // =====================================================
        // PLANT DROPDOWN MULTIPLE SELECT
        // =====================================================
        function togglePlantDropdown() {
            const dropdown = document.getElementById('plantDropdown');
            const container = document.getElementById('plantOptionsContainer');

            if (state.isPlantDropdownOpen) {
                closePlantDropdown();
            } else {
                openPlantDropdown(dropdown, container);
            }
        }

        function openPlantDropdown(dropdown, container) {
            const rect = dropdown.getBoundingClientRect();
            container.style.left = rect.left + 'px';
            container.style.top = (rect.bottom + 4) + 'px';
            container.style.width = rect.width + 'px';
            container.classList.remove('hidden');
            state.isPlantDropdownOpen = true;
        }

        function closePlantDropdown() {
            const container = document.getElementById('plantOptionsContainer');
            container.classList.add('hidden');
            state.isPlantDropdownOpen = false;
        }

        function updateSelectedPlants() {
            const checkboxes = document.querySelectorAll('.plant-checkbox:checked');
            const selectedContainer = document.getElementById('selectedPlants');
            const hiddenInput = document.getElementById('formPlant');

            selectedContainer.innerHTML = '';

            if (checkboxes.length === 0) {
                selectedContainer.innerHTML = '<span class="text-gray-400 text-sm">Pilih Plant</span>';
                hiddenInput.value = '';
                return;
            }

            const plantNames = {};
            @foreach ($plants as $plant)
                plantNames['{{ $plant->id }}'] = '{{ $plant->name_plant }}';
            @endforeach

            const selectedValues = [];
            checkboxes.forEach((checkbox) => {
                const value = checkbox.value;
                selectedValues.push(value);

                const badge = document.createElement('span');
                badge.className =
                    'inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700';
                badge.innerHTML = `
                ${plantNames[value] || value}
                <span class="cursor-pointer hover:text-red-900" onclick="removePlant('${value}')">×</span>
            `;
                selectedContainer.appendChild(badge);
            });

            hiddenInput.value = selectedValues.join(',');
        }

        function removePlant(value) {
            const checkbox = document.querySelector(`.plant-checkbox[value="${value}"]`);
            if (checkbox) {
                checkbox.checked = false;
                updateSelectedPlants();
            }
        }

        // =====================================================
        // MODAL FUNCTIONS
        // =====================================================
        function openModal(type, id = null) {
            const overlay = document.getElementById('modalOverlay');
            const title = document.getElementById('modalTitle');
            const formId = document.getElementById('formId');
            const formMethod = document.getElementById('formMethod');
            const form = document.getElementById('modalForm');

            resetModalForm();

            if (type === 'create') {
                state.isEditMode = false;
                title.textContent = 'Tambah Kegiatan';
                formId.value = '';
                formMethod.value = 'POST';
                form.action = "{{ route('orientation.master-activity.store') }}";
                document.getElementById('formStatus').value = '1';
            } else if (type === 'edit') {
                state.isEditMode = true;
                state.editId = id;
                title.textContent = 'Edit Kegiatan';
                formId.value = id;
                formMethod.value = 'PUT';
                form.action = `/orientation/master-activity/${id}`;
                fetchActivityData(id);
            }

            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            document.body.style.overflow = 'hidden';
            state.isModalOpen = true;
            closePlantDropdown();
        }

        function resetModalForm() {
            document.getElementById('formNama').value = '';
            document.getElementById('formDeskripsi').value = '';
            document.querySelectorAll('.plant-checkbox').forEach(cb => cb.checked = false);
            updateSelectedPlants();
        }

        function fetchActivityData(id) {
            fetch(`/orientation/master-activity/${id}/edit`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    document.getElementById('formNama').value = data.activity_name;
                    document.getElementById('formDeskripsi').value = data.description || '';
                    document.getElementById('formStatus').value = data.status == 1 ? '1' : '0';

                    if (data.plants && data.plants.length > 0) {
                        document.querySelectorAll('.plant-checkbox').forEach(cb => {
                            cb.checked = data.plants.includes(parseInt(cb.value));
                        });
                    }
                    updateSelectedPlants();
                })
                .catch(error => {
                    console.error('Error fetching activity data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengambil data kegiatan!',
                        confirmButtonColor: '#dc2626'
                    });
                    closeModal();
                });
        }

        function closeModal() {
            const overlay = document.getElementById('modalOverlay');
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            document.body.style.overflow = '';
            state.isModalOpen = false;
            closePlantDropdown();
        }

        function validateForm() {
            const selectedPlants = document.getElementById('formPlant').value;

            if (!selectedPlants) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih minimal satu plant!',
                    confirmButtonColor: '#dc2626'
                });
                return false;
            }

            const plantArray = selectedPlants.split(',');
            if (plantArray.length === 0 || (plantArray.length === 1 && plantArray[0] === '')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih minimal satu plant!',
                    confirmButtonColor: '#dc2626'
                });
                return false;
            }

            return true;
        }

        // =====================================================
        // CRUD OPERATIONS
        // =====================================================
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data kegiatan akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteActivity(id);
                }
            });
        }

        function deleteActivity(id) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/orientation/master-activity/${id}`;
            form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
            document.body.appendChild(form);
            form.submit();
        }

        // =====================================================
        // SEARCH AND FILTER FUNCTIONS
        // =====================================================
        function searchActivities() {
            const input = document.getElementById('searchInput');
            state.currentSearch = input.value.toLowerCase().trim();
            applyFilters();
        }

        function filterActivities() {
            const select = document.getElementById('statusFilter');
            state.currentStatus = select.value;
            applyFilters();
        }

        function applyFilters() {
            const rows = document.querySelectorAll('.activity-row');
            const searchTerm = state.currentSearch;
            const statusFilter = state.currentStatus;

            rows.forEach(row => {
                let show = true;

                if (searchTerm) {
                    const name = row.getAttribute('data-name') || '';
                    const code = row.getAttribute('data-code') || '';
                    const description = row.querySelector('td:nth-child(4)')?.textContent?.toLowerCase() || '';
                    if (!name.includes(searchTerm) && !code.includes(searchTerm) && !description.includes(searchTerm)) {
                        show = false;
                    }
                }

                if (show && statusFilter !== 'all') {
                    const status = row.getAttribute('data-status');
                    if (status !== statusFilter) {
                        show = false;
                    }
                }

                row.style.display = show ? '' : 'none';
            });

            state.currentPage = 1;
            updatePagination();
        }

        // =====================================================
        // PAGINATION FUNCTIONS
        // =====================================================
        function updatePagination() {
            const rows = document.querySelectorAll('.activity-row:not([style*="display: none"])');
            const totalRows = rows.length;
            const totalPages = Math.max(1, Math.ceil(totalRows / state.rowsPerPage));

            if (state.currentPage > totalPages) {
                state.currentPage = totalPages;
            }

            const paginationInfo = document.getElementById('paginationInfo');
            const pageNumbers = document.getElementById('pageNumbers');
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');

            if (totalRows === 0) {
                paginationInfo.innerHTML = 'Tidak ada data yang ditampilkan';
                pageNumbers.innerHTML = '';
                prevBtn.disabled = true;
                nextBtn.disabled = true;
                return;
            }

            const start = (state.currentPage - 1) * state.rowsPerPage;
            const end = Math.min(start + state.rowsPerPage, totalRows);

            rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });

            // Update pagination info
            document.getElementById('startCount').textContent = start + 1;
            document.getElementById('endCount').textContent = end;
            document.getElementById('totalCount').textContent = totalRows;

            // Update page buttons
            updatePageButtons(totalPages);

            // Update prev/next buttons
            prevBtn.disabled = state.currentPage === 1;
            nextBtn.disabled = state.currentPage === totalPages;
        }

        function updatePageButtons(totalPages) {
            const container = document.getElementById('pageNumbers');
            container.innerHTML = '';

            let startPage = Math.max(1, state.currentPage - 2);
            let endPage = Math.min(totalPages, state.currentPage + 2);

            if (totalPages > 5) {
                if (state.currentPage <= 3) {
                    endPage = 5;
                } else if (state.currentPage >= totalPages - 2) {
                    startPage = totalPages - 4;
                }
            }

            if (startPage > 1) {
                container.appendChild(createPageButton(1));
                if (startPage > 2) {
                    container.appendChild(createDots());
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                container.appendChild(createPageButton(i));
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    container.appendChild(createDots());
                }
                container.appendChild(createPageButton(totalPages));
            }
        }

        function createPageButton(page) {
            const button = document.createElement('button');
            const isActive = page === state.currentPage;

            button.className = `flex h-9 w-9 items-center justify-center rounded-xl font-semibold text-sm transition ${
            isActive 
                ? 'bg-red-600 text-white shadow-sm cursor-default' 
                : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:border-red-300'
        }`;
            button.textContent = page;
            button.onclick = () => goToPage(page);

            return button;
        }

        function createDots() {
            const dots = document.createElement('span');
            dots.className = 'px-2 text-gray-400 select-none';
            dots.textContent = '...';
            return dots;
        }

        function goToPage(page) {
            const rows = document.querySelectorAll('.activity-row:not([style*="display: none"])');
            const totalPages = Math.max(1, Math.ceil(rows.length / state.rowsPerPage));

            if (page >= 1 && page <= totalPages) {
                state.currentPage = page;
                updatePagination();

                document.getElementById('activityTable')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }

        function changePage(delta) {
            const rows = document.querySelectorAll('.activity-row:not([style*="display: none"])');
            const totalPages = Math.max(1, Math.ceil(rows.length / state.rowsPerPage));
            const newPage = state.currentPage + delta;

            if (newPage >= 1 && newPage <= totalPages) {
                goToPage(newPage);
            }
        }

        function changePerPage() {
            const select = document.getElementById('perPage');
            state.rowsPerPage = parseInt(select.value);
            state.currentPage = 1;
            updatePagination();
        }

        // =====================================================
        // EVENT LISTENERS
        // =====================================================
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize pagination
            updatePagination();

            // Modal form submit
            document.getElementById('modalForm').addEventListener('submit', function(e) {
                e.preventDefault();
                if (validateForm()) {
                    this.submit();
                }
            });

            // Close modal on overlay click
            document.getElementById('modalOverlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && state.isModalOpen) {
                    closeModal();
                }

                if (e.ctrlKey) {
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        changePage(-1);
                    } else if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        changePage(1);
                    }
                }
            });

            // Per page change
            document.getElementById('perPage').addEventListener('change', changePerPage);

            // Search with debounce
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchActivities();
                }, 300);
            });

            // Close plant dropdown on outside click
            document.addEventListener('click', function(e) {
                const dropdown = document.getElementById('plantDropdown');
                const container = document.getElementById('plantOptionsContainer');

                if (dropdown && container && state.isPlantDropdownOpen) {
                    if (!dropdown.contains(e.target) && !container.contains(e.target)) {
                        closePlantDropdown();
                    }
                }
            });

            // Update dropdown position on scroll/resize
            function updateDropdownPosition() {
                if (state.isPlantDropdownOpen) {
                    const dropdown = document.getElementById('plantDropdown');
                    const container = document.getElementById('plantOptionsContainer');
                    const rect = dropdown.getBoundingClientRect();
                    container.style.left = rect.left + 'px';
                    container.style.top = (rect.bottom + 4) + 'px';
                    container.style.width = rect.width + 'px';
                }
            }

            window.addEventListener('scroll', updateDropdownPosition);
            window.addEventListener('resize', updateDropdownPosition);

            console.log('Master Kegiatan page initialized');
        });

        // =====================================================
        // UTILITY FUNCTIONS
        // =====================================================
        function showToast(message, type = 'success') {
            Swal.fire({
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 2000,
                background: 'white',
                toast: true,
                position: 'top-end'
            });
        }

        function refreshData() {
            state.currentPage = 1;
            state.currentSearch = '';
            state.currentStatus = 'all';

            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = 'all';

            applyFilters();
        }

        // Export functions
        window.openModal = openModal;
        window.closeModal = closeModal;
        window.confirmDelete = confirmDelete;
        window.searchActivities = searchActivities;
        window.filterActivities = filterActivities;
        window.goToPage = goToPage;
        window.changePage = changePage;
        window.changePerPage = changePerPage;
        window.togglePlantDropdown = togglePlantDropdown;
        window.removePlant = removePlant;
        window.updateSelectedPlants = updateSelectedPlants;
        window.refreshData = refreshData;
        window.showToast = showToast;
    </script>
</x-app-layout>
