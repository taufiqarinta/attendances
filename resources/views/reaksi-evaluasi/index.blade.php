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
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Master Reaksi Evaluasi</h1>
                        <p class="text-sm text-red-100">Kelola master reaksi evaluasi untuk program orientasi karyawan
                            baru</p>
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
                        <input type="text" id="searchInput" placeholder="Cari reaksi evaluasi..."
                            class="h-10 w-full rounded-xl border border-gray-200 bg-white pl-10 pr-3 text-sm focus:border-red-500 focus:ring-red-500"
                            onkeyup="searchReaksi()">
                    </div>

                    {{-- Right Action --}}
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <select id="statusFilter"
                            class="h-10 w-full sm:w-56 rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-700
               focus:border-red-500 focus:ring-red-500"
                            onchange="filterReaksi()">
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
                            Tambah Reaksi Evaluasi
                        </button>
                    </div>
                </div>

                {{-- ===================================================== --}}
                {{-- TABLE --}}
                {{-- ===================================================== --}}
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[768px]" id="reaksiTable">
                        <thead class="bg-slate-50/80 border-y border-gray-200">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">
                                    No</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[200px]">
                                    Nama Reaksi Evaluasi</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white" id="reaksiTableBody">
                            @php $no = $reaksi->firstItem() ?? 1; @endphp
                            @forelse($reaksi as $item)
                                <tr class="hover:bg-slate-50/60 transition-colors duration-150 reaksi-row group"
                                    data-status="{{ $item->status }}" data-name="{{ strtolower($item->reaksi_name) }}"
                                    data-id="{{ $item->id }}">

                                    <td class="px-4 py-3.5 text-sm text-gray-500 text-center">
                                        <span
                                            class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-xs font-semibold text-gray-600">
                                            {{ $no++ }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="flex-shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-sm text-gray-800">
                                                    {{ $item->reaksi_name }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3.5">
                                        @if ($item->status == 1)
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
                                            <button onclick="openModal('edit', {{ $item->id }})"
                                                class="p-1.5 rounded-lg hover:bg-blue-50 transition-all duration-200 group/edit"
                                                title="Edit Reaksi Evaluasi">
                                                <svg class="w-4 h-4 text-gray-400 group-hover/edit:text-blue-600 transition-colors"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button onclick="confirmDelete({{ $item->id }})"
                                                class="p-1.5 rounded-lg hover:bg-red-50 transition-all duration-200 group/delete"
                                                title="Hapus Reaksi Evaluasi">
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
                                    <td colspan="4" class="px-4 py-16 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div
                                                class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center">
                                                <svg class="w-10 h-10 text-slate-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-base font-semibold text-gray-700">Belum ada data
                                                    reaksi evaluasi</p>
                                                <p class="text-sm text-gray-400 mt-1">Klik tombol <span
                                                        class="font-medium text-gray-600">"Tambah Reaksi
                                                        Evaluasi"</span>
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
                            {{ $reaksi->appends(request()->query())->onEachSide(1)->links() }}
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
                <h3 id="modalTitle" class="text-lg font-bold text-gray-800">Tambah Reaksi Evaluasi</h3>
                <button onclick="closeModal()" class="p-1.5 rounded-lg hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="modalForm" class="p-6 space-y-4" action="{{ route('master-reaksi-evaluasi.store') }}"
                method="POST">
                @csrf
                <input type="hidden" id="formId" name="id" value="">
                <input type="hidden" name="_method" id="formMethod" value="POST">

                {{-- Nama Reaksi Evaluasi --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Reaksi Evaluasi <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="formNama" name="reaksi_name"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-red-500 focus:ring-red-500 focus:outline-none"
                        placeholder="Masukkan nama reaksi evaluasi" required>
                    <p class="mt-1 text-xs text-red-500 hidden" id="errorNama"></p>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status <span
                            class="text-red-500">*</span></label>
                    <select id="formStatus" name="status"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-red-500 focus:ring-red-500 focus:outline-none"
                        required>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                    <p class="mt-1 text-xs text-red-500 hidden" id="errorStatus"></p>
                </div>

                {{-- Tombol --}}
                <div class="flex gap-3 pt-2 sticky bottom-0 bg-white py-3 border-t border-gray-100 -mx-6 px-6">
                    <button type="button" onclick="closeModal()"
                        class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" id="submitBtn"
                        class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
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
        // TOAST HELPER
        // =====================================================
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        function showSuccess(message) {
            Toast.fire({
                icon: 'success',
                title: message
            });
        }

        function showError(message) {
            Toast.fire({
                icon: 'error',
                title: message
            });
        }

        // =====================================================
        // FLASH MESSAGE FROM SESSION
        // =====================================================
        @if (session('success'))
            showSuccess(@json(session('success')));
        @endif

        @if (session('error'))
            showError(@json(session('error')));
        @endif

        @if ($errors->any())
            showError(@json($errors->first()));
        @endif

        // =====================================================
        // STATE MANAGEMENT
        // =====================================================
        const state = {
            currentPage: 1,
            rowsPerPage: 10,
            currentSearch: '',
            currentStatus: 'all',
            isModalOpen: false,
            isEditMode: false,
            editId: null
        };

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
                title.textContent = 'Tambah Reaksi Evaluasi';
                formId.value = '';
                formMethod.value = 'POST';
                form.action = "{{ route('master-reaksi-evaluasi.store') }}";
                document.getElementById('formStatus').value = '1';
            } else if (type === 'edit') {
                state.isEditMode = true;
                state.editId = id;
                title.textContent = 'Edit Reaksi Evaluasi';
                formId.value = id;
                formMethod.value = 'PUT';
                form.action = `/orientation/master-reaksi-evaluasi/${id}`;
                fetchReaksiData(id);
            }

            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            document.body.style.overflow = 'hidden';
            state.isModalOpen = true;
        }

        function resetModalForm() {
            document.getElementById('formNama').value = '';
            document.getElementById('formNama').classList.remove('border-red-500');
            document.getElementById('errorNama').classList.add('hidden');
            document.getElementById('errorStatus').classList.add('hidden');
        }

        function fetchReaksiData(id) {
            fetch(`/orientation/master-reaksi-evaluasi/${id}/edit`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    document.getElementById('formNama').value = data.reaksi_name;
                    document.getElementById('formStatus').value = data.status == 1 ? '1' : '0';
                })
                .catch(error => {
                    console.error('Error fetching reaksi evaluasi data:', error);
                    showError('Gagal mengambil data reaksi evaluasi!');
                    closeModal();
                });
        }

        function closeModal() {
            const overlay = document.getElementById('modalOverlay');
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            document.body.style.overflow = '';
            state.isModalOpen = false;
        }

        // =====================================================
        // CRUD OPERATIONS
        // =====================================================
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data reaksi evaluasi akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteReaksi(id);
                }
            });
        }

        function deleteReaksi(id) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/orientation/master-reaksi-evaluasi/${id}`;
            form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
            document.body.appendChild(form);
            form.submit();
        }

        // =====================================================
        // SUBMIT FORM VIA AJAX (dengan SweetAlert)
        // =====================================================
        async function submitFormAjax(e) {
            e.preventDefault();

            const form = document.getElementById('modalForm');
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;

            // Reset error
            document.getElementById('errorNama').classList.add('hidden');
            document.getElementById('formNama').classList.remove('border-red-500');

            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Menyimpan...';

            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    // Handle validation error
                    if (response.status === 422 && data.errors) {
                        if (data.errors.reaksi_name) {
                            const errorNama = document.getElementById('errorNama');
                            errorNama.textContent = data.errors.reaksi_name[0];
                            errorNama.classList.remove('hidden');
                            document.getElementById('formNama').classList.add('border-red-500');
                        }
                        showError(data.message || 'Validasi gagal. Periksa kembali input Anda.');
                    } else {
                        showError(data.message || 'Terjadi kesalahan pada server.');
                    }
                    return;
                }

                // Success
                closeModal();
                showSuccess(data.message || 'Data berhasil disimpan!');

                // Reload halaman setelah 1 detik agar data ter-update
                setTimeout(() => {
                    window.location.reload();
                }, 1000);

            } catch (error) {
                console.error('Submit error:', error);
                showError('Terjadi kesalahan jaringan. Silakan coba lagi.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        // =====================================================
        // SEARCH AND FILTER FUNCTIONS
        // =====================================================
        function searchReaksi() {
            const input = document.getElementById('searchInput');
            state.currentSearch = input.value.toLowerCase().trim();
            applyFilters();
        }

        function filterReaksi() {
            const select = document.getElementById('statusFilter');
            state.currentStatus = select.value;
            applyFilters();
        }

        function applyFilters() {
            const rows = document.querySelectorAll('.reaksi-row');
            const searchTerm = state.currentSearch;
            const statusFilter = state.currentStatus;

            rows.forEach(row => {
                let show = true;

                if (searchTerm) {
                    const name = row.getAttribute('data-name') || '';
                    if (!name.includes(searchTerm)) {
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
        }

        // =====================================================
        // EVENT LISTENERS
        // =====================================================
        document.addEventListener('DOMContentLoaded', function() {
            // Submit via AJAX
            document.getElementById('modalForm').addEventListener('submit', submitFormAjax);

            document.getElementById('modalOverlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && state.isModalOpen) {
                    closeModal();
                }
            });

            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchReaksi();
                }, 300);
            });

            console.log('Master Reaksi Evaluasi page initialized');
        });

        // Export functions
        window.openModal = openModal;
        window.closeModal = closeModal;
        window.confirmDelete = confirmDelete;
        window.searchReaksi = searchReaksi;
        window.filterReaksi = filterReaksi;
    </script>
</x-app-layout>
