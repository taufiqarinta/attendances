<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Upload Data') }}
        </h2>
    </x-slot>

    <div class="px-4 py-8 overflow-hidden">
        <div class="mx-auto">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-2 mb-4 text-sm text-white bg-green-500 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 overflow-auto">
                <div class="flex items-center min-w-[1000px] justify-between gap-4">
                    {{-- Tombol Tambah Aktivitas di kiri --}}
                    <button data-modal-target="add-activity-modal" data-modal-toggle="add-activity-modal"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        type="button">
                        Upload Data
                    </button>

                    <form action="{{ route('head.upload') }}" method="GET" class="flex items-center gap-2">
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                            class="p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500">
                        <button type="submit"
                            class="px-3 py-2 text-white bg-blue-600 rounded hover:bg-blue-700 text-sm">Cari
                            data</button>

                        @if (request('tanggal'))
                            <a href="{{ route('head.upload') }}"
                                class="px-3 py-2 text-sm text-gray-200 rounded hover:bg-gray-300">Lihat Semua Data</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="overflow-auto">
                <table class="w-full text-sm text-gray-700 border border-gray-200 rounded-lg table-auto">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr class="text-center">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama File</th>
                            <th class="px-4 py-3">Area</th>
                            <th class="px-4 py-3">Tanggal upload</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center bg-white divide-y divide-gray-200">
                        @forelse ($files as $index => $file)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $files->firstItem() + $index }}</td>
                                <td class="px-4 py-3">{{ $file->nama_file ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $file->location->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $file->created_at->format('Y-m-d') ?? '-' }}</td>
                                <td class="flex items-center justify-center gap-3 px-4 py-3">
                                    @php
                                        $extension = pathinfo($file->path, PATHINFO_EXTENSION);
                                        $isPdf = in_array(strtolower($extension), ['pdf']);
                                        $isExcel = in_array(strtolower($extension), ['xls', 'xlsx', 'csv']);
                                    @endphp
                                    @if ($isPdf)
                                        <a href="{{ asset('storage/' . $file->path) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800" title="Lihat File">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    @endif
                                    {{-- Download --}}
                                    <a href="{{ asset('storage/' . $file->path) }}" download
                                        class="text-green-600 hover:text-green-800" title="Download File">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                                        </svg>
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.upload.delete', $file->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus file ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <!-- Hapus (Trigger Modal) -->
                                        <button type="button" class="text-red-600 hover:text-red-800 open-delete-modal"
                                            data-id="{{ $file->id }}" title="Hapus File">
                                            <!-- Icon Tong Sampah -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada data pada
                                    tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            <div class="mt-4">
                {{ $files->appends(request()->only('tanggal'))->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="add-activity-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden transition duration-300 ease-out bg-black bg-opacity-50">
        <div class="relative w-full max-w-md max-h-[90vh] overflow-y-auto p-6 bg-white rounded-lg shadow-lg">


            <!-- Modal header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Upload Data</h3>
                <button type="button"
                    class="w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900"
                    data-modal-toggle="add-activity-modal">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <form id="activity-form" method="POST" action="{{ route('head.upload.store') }}"
                enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="flex flex-col">
                    <label for="locations" class="block mb-1 text-sm font-medium">Pilih Lokasi</label>
                    {{-- <select id="locations" name="locations[]" multiple
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select> --}}
                    <input type="hidden" name="location_id" value="{{ $locations->id }}">
                    <p class="text-sm font-medium"><strong>{{ $locations->name }}</strong></p>
                </div>

                <div class="flex flex-col">
                    <label for="file" class="block mb-1 text-sm font-medium">Upload File</label>
                    <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv,.pdf"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>

                <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-medium rounded-lg text-sm px-5 py-2.5">
                    Simpan
                </button>
            </form>

        </div>
    </div>

    @if ($errors->any())
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                document.getElementById('add-activity-modal').classList.remove('hidden');
            });
        </script>
    @endif

    <!-- Modal Hapus -->
    <div id="delete-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 items-center justify-center hidden bg-black bg-opacity-50">
        <div class="w-full max-w-sm p-6 bg-white rounded-lg shadow-lg">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">Konfirmasi Hapus</h3>
            <p class="mb-4 text-gray-600">Apakah kamu yakin ingin menghapus file ini?</p>
            <form id="delete-form" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" id="cancel-delete"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        new TomSelect('#locations', {
            plugins: ['remove_button'],
            maxItems: null,
            placeholder: "Ketik dan pilih lokasi",
            create: false,
            persist: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.open-delete-modal').on('click', function() {
                const id = $(this).data('id');
                const action = `{{ route('head.upload.delete', '__ID__') }}`.replace('__ID__', id);
                $('#delete-form').attr('action', action);
                $('#delete-modal').removeClass('hidden').addClass('flex');
            });

            $('#cancel-delete').on('click', function() {
                $('#delete-modal').removeClass('flex').addClass('hidden');
            });
        });
    </script>
</x-app-layout>
