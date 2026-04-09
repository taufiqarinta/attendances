<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Project') }}
        </h2>
    </x-slot>

    <div class="px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="bg-green-500 text-white text-sm p-2 mb-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <h2></h2>

            <div class="flex justify-between items-center mb-4">
                <button data-modal-target="add-project-modal" data-modal-toggle="add-project-modal"
                    class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Tambah Project
                </button>
            </div>

            <table class="min-w-full text-sm text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr class="text-center">
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nama Project</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($projects as $index => $project)
                        <tr>
                            <td class="px-6 py-3">{{ $projects->firstItem() + $index }}</td>
                            <td class="px-6 py-3">{{ $project->name }}</td>
                            <td class="px-6 py-3">
                                <button onclick="editProject({{ $project->id }}, '{{ $project->name }}')"
                                    class="text-blue-600 hover:text-blue-800">Edit</button>
                                <button onclick="deleteProject({{ $project->id }})"
                                    class="text-red-600 hover:text-red-800">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $projects->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="add-project-modal" class="hidden fixed inset-0 z-50 flex justify-center items-center">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold mb-4">Tambah Project</h3>
            <form action="{{ route('admin.project.store') }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Masukkan nama project"
                    class="border p-2 w-full rounded-lg" required />
                <button type="submit"
                    class="mt-3 text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Tambah Project
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="edit-project-modal"
        class="hidden fixed inset-0 z-50 flex justify-center items-center bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow p-6 relative">
            <button onclick="closeModal('edit-project-modal')"
                class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">&times;</button>
            <h3 class="text-xl font-semibold mb-4">Edit Project</h3>
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-id" name="id">
                <input type="text" id="edit-name" name="name" class="border p-2 w-full rounded-lg" required />
                <button type="submit"
                    class="mt-3 text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    Update Project
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div id="delete-project-modal"
        class="hidden fixed inset-0 z-50 flex justify-center items-center bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow p-6 relative">
            <button onclick="closeModal('delete-project-modal')"
                class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">&times;</button>
            <h3 class="text-xl font-semibold mb-4">Hapus Project</h3>
            <p>Apakah Anda yakin ingin menghapus project ini?</p>
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
        function editProject(id, name) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-form').action = "/admin/project/" + id;
            document.getElementById('edit-project-modal').classList.remove('hidden');
        }

        function deleteProject(id) {
            document.getElementById('delete-form').action = "/admin/project/" + id;
            document.getElementById('delete-project-modal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>

</x-app-layout>
