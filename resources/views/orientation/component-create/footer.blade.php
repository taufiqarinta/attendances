<div class="sticky bottom-4 mt-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-lg">

    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">

        <div class="text-center sm:text-left">
            <h3 class="text-sm font-semibold text-gray-800">Siap Menyimpan Orientation?</h3>
            <p class="text-xs text-gray-500">Pastikan seluruh data sudah benar sebelum disimpan.</p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ url()->previous() }}"
                class="flex-1 sm:flex-none rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-700 text-center transition hover:bg-gray-50">
                Batal
            </a>
            <button onclick="submitOrientation()"
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-red-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan
            </button>
        </div>

    </div>

</div>
