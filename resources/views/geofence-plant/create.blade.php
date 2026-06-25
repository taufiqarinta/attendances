<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Geofence Plant') }}
        </h2>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <div class="mb-6">
                        <a href="{{ route('geofence-plant.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                            &larr; Kembali ke daftar
                        </a>
                    </div>

                    <form action="{{ route('geofence-plant.store') }}" method="POST">
                        @csrf

                        @include('geofence-plant._form', [
                            'geofencePlant' => $geofencePlant,
                            'types' => $types,
                        ])

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('geofence-plant.index') }}"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Data
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>