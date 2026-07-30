<x-app-layout>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="min-h-screen bg-[#F4F5F7] py-4">

        <div class="mx-auto max-w-7xl px-4">

            {{-- ========================================================= --}}
            {{-- HERO --}}
            {{-- ========================================================= --}}

            <div
                class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#7A1113] via-[#9B1F14] to-[#D71920] shadow-lg">

                <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/10"></div>
                <div class="absolute right-20 bottom-0 h-28 w-28 rounded-full bg-white/10"></div>

                <div class="relative flex items-center gap-4 px-6 py-5">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-white/10 backdrop-blur">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Form Create Orientation</h1>
                        <p class="text-sm text-red-100">Buat sesi orientasi baru untuk karyawan.</p>
                    </div>
                </div>

            </div>

            {{-- ========================================================= --}}
            {{-- FORM SELECTION --}}
            {{-- ========================================================= --}}

            <div class="mt-4 rounded-2xl border border-gray-100 bg-white shadow-sm">

                <div class="p-5">

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        {{-- Plant Dropdown dengan data dari database --}}
                        @include('orientation.component-create.dropdwon-plant')

                        <!-- Peserta -->
                        @include('orientation.component-create.participant-modal')

                    </div>

                </div>

            </div>

            {{-- ========================================================= --}}
            {{-- RINCIAN KEGIATAN --}}
            {{-- ========================================================= --}}

            @include('orientation.component-create.activity-list')


            {{-- ========================================================= --}}
            {{-- INFORMATION & CHECKLIST --}}
            {{-- ========================================================= --}}

            @include('orientation.component-create.information')

            {{-- FOOTER --}}
            {{-- ========================================================= --}}

            @include('orientation.component-create.footer')

        </div>

    </div>

    {{-- JavaScript untuk submit form --}}
    <script>
        async function submitOrientation() {
            const plantId = window.selectedPlantId;
            if (!plantId) {
                alert('Silakan pilih plant terlebih dahulu');
                return;
            }

            const participantElement = document.querySelector('[x-data="participantModal()"]');
            const activityElement = document.getElementById('orientationActivities');
            const participants = Alpine.$data(participantElement)?.selectedParticipants || [];
            const activities = Alpine.$data(activityElement)?.kegiatanRows || [];

            if (participants.length === 0) {
                alert('Silakan pilih minimal 1 peserta');
                return;
            }

            if (activities.length === 0) {
                alert('Silakan tambahkan minimal 1 kegiatan');
                return;
            }

            const submitButton = document.querySelector('[onclick="submitOrientation()"]');
            submitButton.disabled = true;
            submitButton.classList.add('opacity-60', 'cursor-not-allowed');

            try {
                const response = await fetch(@json(route('orientation.store')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        plant_id: plantId,
                        participants: participants.map(participant => ({
                            nik: String(participant.nik),
                            nama: participant.nama || String(participant.nik),
                            jabatan: participant.jabatan || null,
                            dept: participant.dept || null
                        })),
                        activities: activities
                    })
                });
                const result = await response.json();

                if (!response.ok) {
                    const errors = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                    throw new Error(errors || 'Gagal menyimpan orientation.');
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Orientation berhasil dibuat',
                    text: result.message || 'Data orientation dan kegiatan berhasil disimpan.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc2626',
                    allowOutsideClick: false
                });

                window.location.assign(result.redirect);
            } catch (error) {
                alert(error.message || 'Gagal menyimpan orientation.');
            } finally {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        }
    </script>

</x-app-layout>
