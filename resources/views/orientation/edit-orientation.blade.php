<x-app-layout>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="min-h-screen bg-[#F4F5F7] py-4">
        <div class="mx-auto max-w-7xl px-4">
            <div
                class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#7A1113] via-[#9B1F14] to-[#D71920] shadow-lg">
                <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/10"></div>
                <div class="absolute bottom-0 right-20 h-28 w-28 rounded-full bg-white/10"></div>
                <div class="relative flex items-center gap-4 px-6 py-5">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/10 backdrop-blur"><svg
                            class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                        </svg></div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Edit Orientation</h1>
                        <p class="text-sm text-red-100">Perbarui peserta dan rincian kegiatan tanpa mengubah plant.</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="p-5">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700">Plant</label>
                            <div
                                class="flex w-full items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50"><svg
                                        class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14-4H5m14 8H5" />
                                    </svg></div>
                                <div class="text-left">
                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $orientation->plant?->name_plant }}</div>
                                    <div class="text-xs text-gray-500">Kode: {{ $orientation->plant?->code }} · Plant
                                        tidak dapat diubah</div>
                                </div>
                            </div>
                        </div>
                        @include('orientation.component-create.participant-modal')
                    </div>
                </div>
            </div>

            <script>
                window.selectedPlantId = @json($orientation->master_plants_id);
                window.selectedPlantData = @json($orientation->plant);
            </script>

            @include('orientation.component-create.activity-list')

            <div class="sticky bottom-4 mt-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-lg">
                <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Simpan perubahan orientation?</h3>
                        <p class="text-xs text-gray-500">Peserta dan daftar kegiatan akan diperbarui.</p>
                    </div>
                    <div class="flex w-full items-center gap-2 sm:w-auto"><a href="{{ route('orientation.index') }}"
                            class="flex-1 rounded-xl border border-gray-200 px-5 py-2.5 text-center text-sm font-medium text-gray-700 transition hover:bg-gray-50 sm:flex-none">Batal</a><button
                            type="button" onclick="submitOrientation()"
                            class="flex-1 rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-red-700 sm:flex-none">Simpan
                            Perubahan</button></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function submitOrientation() {
            const participantRoot = document.querySelector('[x-data="participantModal()"]');
            const activityRoot = document.getElementById('orientationActivities');
            const participants = Alpine.$data(participantRoot)?.selectedParticipants || [];
            const activities = Alpine.$data(activityRoot)?.kegiatanRows || [];
            if (!participants.length) return Swal.fire('Peserta belum dipilih', 'Pilih minimal satu peserta.',
                'warning');
            if (!activities.length) return Swal.fire('Kegiatan belum tersedia', 'Tambahkan minimal satu kegiatan.',
                'warning');

            const button = document.querySelector('[onclick="submitOrientation()"]');
            button.disabled = true;
            button.classList.add('opacity-60', 'cursor-not-allowed');
            try {
                const response = await fetch(@json(route('orientation.update', $orientation)), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        participants: participants.map(item => ({
                            nik: String(item.nik),
                            nama: item.nama || String(item.nik),
                            jabatan: item.jabatan || null,
                            dept: item.dept || null
                        })),
                        activities
                    })
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.errors ? Object.values(result.errors).flat().join('\n') :
                    result.message);
                await Swal.fire({
                    icon: 'success',
                    title: 'Orientation berhasil diperbarui',
                    text: result.message,
                    confirmButtonColor: '#dc2626'
                });
                window.location.assign(result.redirect);
            } catch (error) {
                Swal.fire('Gagal menyimpan', error.message || 'Perubahan orientation gagal disimpan.', 'error');
            } finally {
                button.disabled = false;
                button.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        }
    </script>
</x-app-layout>
