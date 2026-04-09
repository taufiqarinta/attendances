<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Ukuran') }}
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

            <div class="flex items-center justify-between mb-4">
                <button data-modal-target="add-project-modal" data-modal-toggle="add-project-modal"
                    class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Tambah Ukuran
                </button>
            </div>

            <table class="min-w-full text-sm text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr class="text-center">
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nama Merk</th>
                        <th class="px-6 py-3">Ukuran</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($ukurans as $index => $ukuran)
                        <tr>
                            <td class="px-6 py-3">{{ $ukurans->firstItem() + $index }}</td>
                            <td class="px-6 py-3">{{ optional($ukuran->merk)->name ?? 'Tidak Ada' }}</td>
                            <td class="px-6 py-3">{{ $ukuran->name }}</td>
                            <td class="px-6 py-3">
                                <button
                                    onclick="editProject({{ $ukuran->id }}, '{{ $ukuran->merk_id }}', '{{ $ukuran->name }}')"
                                    class="text-blue-600 hover:text-blue-800">Edit</button>
                                <button onclick="deleteProject({{ $ukuran->id }})"
                                    class="text-red-600 hover:text-red-800">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $ukurans->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="add-project-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-xl font-semibold">Tambah Ukuran</h3>
            <form action="{{ route('admin.ukuran.store') }}" method="POST">
                @csrf

                <!-- Dropdown untuk memilih Project -->
                <label class="block text-sm font-medium text-gray-700">Pilih Merk</label>
                <select name="merk_id" class="w-full p-2 border rounded-lg" required>
                    <option value="" disabled selected>Pilih Merk</option>
                    @foreach ($merks as $merk)
                        <option value="{{ $merk->id }}">{{ $merk->name }}</option>
                    @endforeach
                </select>

                <!-- Input Nama PT -->
                <label class="block mt-2 text-sm font-medium text-gray-700">Ukuran</label>
                <input type="text" name="name" placeholder="Masukkan Ukuran"
                    class="w-full p-2 border rounded-lg">

                <button type="submit"
                    class="mt-3 text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Tambah Ukuran
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
            <h3 class="mb-4 text-xl font-semibold">Edit Ukuran</h3>
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit-id" name="id">

                <!-- Dropdown untuk memilih Project -->
                <label class="block text-sm font-medium text-gray-700">Pilih Merk</label>
                <select id="edit-project" name="merk_id" class="w-full p-2 border rounded-lg" required>
                    <option value="" disabled selected>Pilih Merk</option>
                    @foreach ($merks as $merk)
                        <option value="{{ $merk->id }}">{{ $merk->name }}</option>
                    @endforeach
                </select>

                <!-- Input Nama PT -->
                <label class="block mt-2 text-sm font-medium text-gray-700">Ukuran</label>
                <input type="text" id="edit-nama-pt" name="name" class="w-full p-2 border rounded-lg">

                <button type="submit"
                    class="mt-3 text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Update Ukuran
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
            <h3 class="mb-4 text-xl font-semibold">Hapus Ukuran</h3>
            <p>Apakah Anda yakin ingin menghapus ukuran ini?</p>
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
        function editProject(id, merk_id, name) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-project').value = merk_id;
            document.getElementById('edit-nama-pt').value = name !== 'null' ? name : '';
            document.getElementById('edit-form').action = "/admin/ukuran/" + id;
            document.getElementById('edit-project-modal').classList.remove('hidden');
        }

        function deleteProject(id) {
            document.getElementById('delete-form').action = "/admin/ukuran/" + id;
            document.getElementById('delete-project-modal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>

</x-app-layout>
