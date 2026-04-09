<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('File Data') }}
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
                    <form action="{{ route('data') }}" method="GET" class="flex items-center gap-2">
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                            class="p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500">
                        <button type="submit"
                            class="px-3 py-2 text-white bg-blue-600 rounded hover:bg-blue-700 text-sm">Cari
                            data</button>

                        @if (request('tanggal'))
                            <a href="{{ route('data') }}"
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
                            <th class="px-4 py-3">Tanggal File</th>
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
