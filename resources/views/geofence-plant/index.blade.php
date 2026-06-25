<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Master Geofence Plant') }}
        </h2>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Header: Judul + Tombol Tambah -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Daftar Lokasi Geofence</h3>
                            <p class="text-sm text-gray-500">Kelola titik lokasi & radius absensi per plant.</p>
                        </div>
                        <a href="{{ route('geofence-plant.create') }}"
                            class="inline-flex items-center justify-center gap-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Tambah Data
                        </a>
                    </div>

                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('geofence-plant.index') }}" class="flex flex-col sm:flex-row gap-3 mb-4">
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari plant atau nama plant..."
                            class="flex-1 px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">

                        <select name="type" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Semua Jenis</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ $typeFilter === $type ? 'selected' : '' }}>
                                    {{ $type == 'in' ? 'Masuk (IN)' : 'Pulang (OUT)' }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit"
                            class="bg-gray-600 hover:bg-gray-800 text-white font-medium py-2 px-4 rounded text-sm">
                            Filter
                        </button>

                        @if($search || $typeFilter)
                            <a href="{{ route('geofence-plant.index') }}"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded text-sm text-center">
                                Reset
                            </a>
                        @endif
                    </form>

                    <!-- Tabel Data -->
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Plant</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Plant</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Jenis</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Radius (m)</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Koordinat</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Exclude Dept</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Exclude NIK</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($geofencePlants as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $row->plant }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $row->plant_name }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                {{ $row->type == 'in' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                                {{ $row->type == 'in' ? 'Masuk' : 'Pulang' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">{{ rtrim(rtrim(number_format($row->radius, 2, '.', ''), '0'), '.') }}</td>
                                        <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                                            {{ $row->latitude }}, {{ $row->longitude }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 text-xs">
                                            {{ $row->exclude_department ?: '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 text-xs">
                                            {{ $row->exclude_nik ?: '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                {{ (int)$row->status === 1 ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                                {{ $row->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <a href="{{ route('geofence-plant.edit', $row->id) }}"
                                                class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium mr-3">
                                                Edit
                                            </a>
                                            <button type="button"
                                                onclick="confirmDelete({{ $row->id }}, '{{ addslashes($row->plant_name) }}')"
                                                class="inline-flex items-center text-red-600 hover:text-red-800 font-medium">
                                                Hapus
                                            </button>

                                            <!-- Form hidden khusus untuk submit DELETE, dipicu oleh SweetAlert -->
                                            <form id="delete-form-{{ $row->id }}"
                                                action="{{ route('geofence-plant.destroy', $row->id) }}"
                                                method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                            Belum ada data geofence plant.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $geofencePlants->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id, plantName) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Data?',
                html: `Anda akan menghapus data geofence untuk <strong>${plantName}</strong>. Tindakan ini tidak dapat dibatalkan.`,
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
</x-app-layout>