<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Area') }}
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

            <div class="flex items-center justify-between mb-4">
                <button data-modal-target="add-p2-modal" data-modal-toggle="add-p2-modal"
                    class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Tambah Area
                </button>
            </div>

            <table class="min-w-full text-sm text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr class="text-center">
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nama Area</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($locations as $index => $p2)
                        <tr>
                            <td class="px-6 py-3">{{ $locations->firstItem() + $index }}</td>
                            <td class="px-6 py-3">{{ $p2->name }}</td>
                            <td class="px-6 py-3">
                                <button onclick="editP2({{ $p2->id }}, '{{ $p2->name }}')"
                                    class="text-blue-600 hover:text-blue-800">Edit</button>
                                <button onclick="deleteP2({{ $p2->id }})"
                                    class="text-red-600 hover:text-red-800">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $locations->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="add-p2-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="relative p-6 bg-white rounded-lg shadow">
            {{-- <button onclick="closeModal('add-p2-modal')"
                class="absolute text-gray-600 top-2 right-2 hover:text-gray-900">
                ✖
            </button> --}}
            <h3 class="mb-4 text-xl font-semibold">Tambah Area</h3>
            <form action="{{ route('admin.location.store') }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Masukkan nama Area"
                    class="w-full p-2 border rounded-lg" required />
                <button type="submit"
                    class="mt-3 text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Tambah Area
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="edit-p2-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="relative p-6 bg-white rounded-lg shadow">
            <button onclick="closeModal('edit-p2-modal')"
                class="absolute text-gray-600 top-2 right-2 hover:text-gray-900">
                ✖
            </button>
            <h3 class="mb-4 text-xl font-semibold">Edit Area</h3>
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-id" name="id">
                <input type="text" id="edit-name" name="name" class="w-full p-2 border rounded-lg" required />
                <button type="submit"
                    class="mt-3 text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Update Area
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div id="delete-p2-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="relative p-6 bg-white rounded-lg shadow">
            <button onclick="closeModal('delete-p2-modal')"
                class="absolute text-gray-600 top-2 right-2 hover:text-gray-900">
                ✖
            </button>
            <h3 class="mb-4 text-xl font-semibold">Hapus Area</h3>
            <p>Apakah Anda yakin ingin menghapus area ini?</p>
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
        function editP2(id, name) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-form').action = "/admin/location/" + id;
            document.getElementById('edit-p2-modal').classList.remove('hidden');
        }

        function deleteP2(id) {
            document.getElementById('delete-form').action = "/admin/location/" + id;
            document.getElementById('delete-p2-modal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>
</x-app-layout>
