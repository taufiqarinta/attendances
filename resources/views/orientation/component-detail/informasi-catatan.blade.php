<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    {{-- Informasi Umum --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 text-sm">Informasi Program</h4>
                <div class="text-xs text-gray-600 mt-1 space-y-1">
                    <p>
                        <span class="font-medium text-gray-700">Batch:</span>
                        {{ $orientation->batch_name ?? '-' }}
                    </p>
                    <p>
                        <span class="font-medium text-gray-700">Lokasi:</span>
                        {{ $orientation->plant->name_plant ?? '-' }}
                    </p>
                    <p>
                        <span class="font-medium text-gray-700">Total Materi:</span>
                        {{ $orientation->activities->count() ?? 0 }} materi
                    </p>
                    {{-- <p>
                        <span class="font-medium text-gray-700">Total Peserta:</span>
                        {{ $orientation->participants->count() ?? 0 }} orang
                    </p> --}}
                    <p>
                        <span class="font-medium text-gray-700">Status:</span>
                        @php
                            $status = $orientation->status ?? 'draft';
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-700',
                                'active' => 'bg-green-100 text-green-700',
                                'completed' => 'bg-blue-100 text-blue-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                            ];
                            $statusLabels = [
                                'draft' => 'Draft',
                                'active' => 'Aktif',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ];
                        @endphp
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $statusLabels[$status] ?? ucfirst($status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Catatan --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <div class="flex items-start gap-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-50 flex-shrink-0 mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 text-sm">Catatan Penting</h4>
                <ul class="mt-1 space-y-1 text-xs text-gray-600">
                    <li>• Pastikan setiap materi memiliki <strong>PIC/Trainer</strong> yang
                        bertanggung jawab.</li>
                    <li>• Tekan tombol <strong class="text-blue-600">"Mulai"</strong> untuk memulai
                        sesi materi.</li>
                    <li>• Setelah sesi selesai, tekan tombol <strong class="text-green-600">"Selesai"</strong> untuk
                        menandai materi telah
                        selesai.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
