php artisan serve --host=192.168.90.151 --port=8000

  <div
                    class="relative overflow-hidden bg-gradient-to-r from-pink-50 to-rose-50 border border-pink-100 shadow-sm rounded-lg p-6">

                    {{-- Decorative --}}
                    <div class="absolute -top-8 -right-8 w-32 h-32
        bg-pink-100 rounded-full opacity-50">
                    </div>

                    <div class="absolute -bottom-10 -left-10 w-28 h-28
        bg-rose-100 rounded-full opacity-50">
                    </div>

                    {{-- Header --}}
                    <div class="relative flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3-1.343 3-3S13.657 2 12 2
                        9 3.343 9 5s1.343 3 3 3z
                        M5 22h14M7 22v-5a5 5 0 0110 0v5
                        M8 12h8" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">
                                    Ada yang Berulang Tahun! 🎉
                                </h3>
                                <p class="text-sm text-gray-500">
                                    Yuk, ucapkan selamat & doa terbaik untuk mereka hari ini 💐
                                </p>
                            </div>
                        </div>
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-medium text-gray-500" id="birthdayDate">
                                {{ now()->translatedFormat('d F Y') }}
                            </p>
                        </div>
                    </div>

                    {{-- Birthday List --}}
                    <div class="relative mt-6" id="birthdayListContainer">
                        {{-- Loading state --}}
                        <div id="birthdayLoading" class="flex items-center justify-center min-h-[130px]">
                            <div class="text-center">
                                <svg class="animate-spin h-8 w-8 text-pink-400 mx-auto mb-2"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <p class="text-xs text-gray-400">Memuat data ulang tahun...</p>
                            </div>
                        </div>

                        {{-- Content akan di-render oleh JavaScript --}}
                        <div id="birthdayContent" class="hidden"></div>
                    </div>
                </div>
