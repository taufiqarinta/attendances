<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Motif') }}
        </h2>
    </x-slot>

    <div class="px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-2 mb-4 text-sm text-white bg-green-500 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <h2></h2>

            <div class="mb-4">
                <label class="block mb-1 text-sm font-semibold text-gray-700">Pilih Merk</label>
                <select id="merkSelect" class="w-full p-2 border rounded-lg">
                    <option value="">-- Pilih Merk --</option>
                    @php
                        $listMerk = $ukurans->pluck('merk')->unique('id');
                    @endphp
                    @foreach ($listMerk as $merk)
                        <option value="{{ $merk->id }}">{{ $merk->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="hidden mb-4" id="motifSearchContainer">
                <label class="block mb-1 text-sm font-semibold text-gray-700">Cari Motif</label>
                <input type="text" id="motifSearchInput" class="w-full p-2 border rounded-lg"
                    placeholder="Ketik nama motif...">
                <ul id="motifResult" class="hidden mt-2 overflow-y-auto text-left bg-white border rounded-lg max-h-40">
                </ul>
            </div>

            <div class="flex items-center justify-between mb-4">
                <button data-modal-target="add-project-modal" data-modal-toggle="add-project-modal"
                    class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Tambah Motif
                </button>
            </div>

            <table class="min-w-full text-sm text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr class="text-center">
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nama Merk</th>
                        <th class="px-6 py-3">Ukuran</th>
                        <th class="px-6 py-3">Nama Motif</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="motifTableBody" class="text-center">
                    @foreach ($motifs as $index => $motif)
                        <tr>
                            <td class="px-6 py-3">{{ $motifs->firstItem() + $index }}</td>
                            <td class="px-6 py-3">{{ optional($motif->ukuran->merk)->name ?? '-' }}</td>
                            <td class="px-6 py-3">{{ optional($motif->ukuran)->name ?? '-' }}</td>
                            <td class="px-6 py-3">{{ $motif->name }}</td>
                            <td class="px-6 py-3">
                                <button
                                    onclick="editProject({{ $motif->id }}, '{{ $motif->ukuran_id }}', '{{ $motif->name }}')"
                                    class="text-blue-600 hover:text-blue-800">Edit</button>
                                <button onclick="deleteProject({{ $motif->id }})"
                                    class="text-red-600 hover:text-red-800">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $motifs->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="add-project-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-xl font-semibold">Tambah Motif</h3>
            <form action="{{ route('admin.motif.store') }}" method="POST">
                @csrf

                <!-- Dropdown untuk memilih Project -->
                <label class="block text-sm font-medium text-gray-700">Pilih Merk & Ukuran</label>
                <select name="ukuran_id" class="w-full p-2 border rounded-lg" required>
                    <option value="" disabled selected>Pilih Merk & Ukuran</option>
                    @foreach ($ukurans as $ukuran)
                        <option value="{{ $ukuran->id }}">{{ $ukuran->merk->name }} - {{ $ukuran->name }}</option>
                    @endforeach
                </select>

                <!-- Input Nama PT -->
                <label class="block mt-2 text-sm font-medium text-gray-700">Nama Motif</label>
                <input type="text" name="name" placeholder="Masukkan Nama Motif"
                    class="w-full p-2 border rounded-lg">

                <button type="submit"
                    class="mt-3 text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Tambah Motif
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="edit-project-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="relative p-6 bg-white rounded-lg shadow">
            <button onclick="closeModal('edit-project-modal')"
                class="absolute text-gray-600 top-2 right-2 hover:text-gray-900">&times;</button>
            <h3 class="mb-4 text-xl font-semibold">Edit Motif</h3>
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit-id" name="id">

                <!-- Dropdown untuk memilih Project -->
                <label class="block text-sm font-medium text-gray-700">Pilih Merk & Ukuran</label>
                <select id="edit-project" name="ukuran_id" class="w-full p-2 border rounded-lg" required>
                    <option value="" disabled selected>Pilih Merk & Ukuran</option>
                    @foreach ($ukurans as $ukuran)
                        <option value="{{ $ukuran->id }}">
                            {{ $ukuran->merk->name ?? '-' }} - {{ $ukuran->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Input Nama PT -->
                <label class="block mt-2 text-sm font-medium text-gray-700">Nama Motif</label>
                <input type="text" id="edit-nama-pt" name="name" class="w-full p-2 border rounded-lg">

                <button type="submit"
                    class="mt-3 text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Update Motif
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div id="delete-project-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="relative p-6 bg-white rounded-lg shadow">
            <button onclick="closeModal('delete-project-modal')"
                class="absolute text-gray-600 top-2 right-2 hover:text-gray-900">&times;</button>
            <h3 class="mb-4 text-xl font-semibold">Hapus Motif</h3>
            <p>Apakah Anda yakin ingin menghapus motif ini?</p>
            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="mt-3 text-white bg-red-700 hover:bg-red-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function editProject(id, ukuran_id, name) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nama-pt').value = name !== 'null' ? name : '';
            document.getElementById('edit-form').action = "/admin/motif/" + id;

            // ✅ SET SELECTED VALUE
            let selectEl = document.getElementById('edit-project');
            selectEl.value = ukuran_id;

            document.getElementById('edit-project-modal').classList.remove('hidden');
        }

        function deleteProject(id) {
            document.getElementById('delete-form').action = "/admin/motif/" + id;
            document.getElementById('delete-project-modal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>

    <script>
        document.getElementById('merkSelect').addEventListener('change', function() {
            const merkId = this.value;
            const motifInput = document.getElementById('motifSearchInput');
            const motifContainer = document.getElementById('motifSearchContainer');
            const tbody = document.getElementById('motifTableBody');

            motifInput.value = '';
            tbody.innerHTML = '';
            window.filteredMotifs = [];

            if (!merkId) {
                motifContainer.classList.add('hidden');
                return;
            }

            motifContainer.classList.remove('hidden');

            fetch(`/admin/filter-motif?merk_id=${merkId}`)
                .then(res => res.json())
                .then(data => {
                    window.filteredMotifs = data;
                    renderMotifTable(data);
                });
        });

        document.getElementById('motifSearchInput').addEventListener('input', function() {
            const keyword = this.value.toLowerCase();
            const filtered = (window.filteredMotifs || []).filter(motif =>
                motif.name.toLowerCase().includes(keyword)
            );
            renderMotifTable(filtered);
        });

        function renderMotifTable(data) {
            const tbody = document.getElementById('motifTableBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="py-4 text-gray-500">Tidak ada motif ditemukan.</td></tr>`;
                return;
            }

            data.forEach((motif, index) => {
                const merkName = motif.ukuran?.merk?.name || '-';
                const ukuranName = motif.ukuran?.name || '-';
                const motifName = motif.name || '-';
                const motifId = motif.id;
                const ukuranId = motif.ukuran_id;

                const row = `
                <tr>
                    <td class="px-6 py-3">${index + 1}</td>
                    <td class="px-6 py-3">${merkName}</td>
                    <td class="px-6 py-3">${ukuranName}</td>
                    <td class="px-6 py-3">${motifName}</td>
                    <td class="px-6 py-3">
                        <button
                            onclick="editProject(${motifId}, '${ukuranId}', '${motifName}')"
                            class="text-blue-600 hover:text-blue-800">Edit</button>
                        <button
                            onclick="deleteProject(${motifId})"
                            class="text-red-600 hover:text-red-800">Hapus</button>
                    </td>
                </tr>
            `;
                tbody.innerHTML += row;
            });
        }
    </script>

</x-app-layout>
