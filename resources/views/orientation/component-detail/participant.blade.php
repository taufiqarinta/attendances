{{-- Daftar Peserta --}}
@if (isset($participantDetails) && $participantDetails->count() > 0)
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm mt-6">
        <div class="flex items-center justify-between border-b border-gray-100 px-4 sm:px-6 py-3.5">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 text-sm">Daftar Peserta</h3>
                    <p class="text-xs text-gray-500">Total {{ $participantStats['total'] ?? 0 }} peserta</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3">No</th>
                        <th class="px-4 sm:px-6 py-3">NIK</th>
                        <th class="px-4 sm:px-6 py-3">Nama</th>
                        <th class="px-4 sm:px-6 py-3 hidden md:table-cell">Jabatan</th>
                        <th class="px-4 sm:px-6 py-3 hidden lg:table-cell">Departemen</th>
                        <th class="px-4 sm:px-6 py-3 hidden xl:table-cell">Email</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participantDetails as $index => $participant)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 sm:px-6 py-3 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 sm:px-6 py-3 font-mono text-xs">{{ $participant['nik'] ?? '-' }}</td>
                            <td class="px-4 sm:px-6 py-3 font-medium text-gray-800">{{ $participant['nama'] }}</td>
                            <td class="px-4 sm:px-6 py-3 text-gray-600 hidden md:table-cell">
                                {{ $participant['jabatan'] }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-gray-600 hidden lg:table-cell">
                                {{ $participant['dept'] }}
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-gray-600 hidden xl:table-cell">
                                {{ $participant['email'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 sm:px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Belum ada peserta terdaftar</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 px-4 sm:px-6 py-3 bg-gray-50 rounded-b-2xl">
            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-gray-600">
                <div class="flex items-center gap-4">
                    <span>Total: <strong>{{ $participantStats['total'] }}</strong> peserta</span>
                </div>
                <div>
                    <span class="text-gray-400">
                        Terakhir diupdate: {{ now()->format('d M Y H:i') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
@endif
