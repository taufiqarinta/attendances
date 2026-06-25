<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Geofence Plant') }}
        </h2>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <div class="mb-6 flex items-center justify-between">
                        <a href="{{ route('geofence-plant.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                            &larr; Kembali ke daftar
                        </a>
                        <a href="{{ route('geofence-plant.edit', $geofencePlant->id) }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Edit Data
                        </a>
                    </div>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Kode Plant</dt>
                            <dd class="text-gray-800">{{ $geofencePlant->plant }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Nama Plant</dt>
                            <dd class="text-gray-800">{{ $geofencePlant->plant_name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Jenis Absensi</dt>
                            <dd class="text-gray-800">{{ $geofencePlant->type === 'IN' ? 'Masuk (IN)' : 'Pulang (OUT)' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Radius</dt>
                            <dd class="text-gray-800">{{ $geofencePlant->radius }} meter</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Latitude</dt>
                            <dd class="text-gray-800">{{ $geofencePlant->latitude }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Longitude</dt>
                            <dd class="text-gray-800">{{ $geofencePlant->longitude }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Exclude Department</dt>
                            <dd class="text-gray-800">{{ $geofencePlant->exclude_department ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Exclude NIK</dt>
                            <dd class="text-gray-800">{{ $geofencePlant->exclude_nik ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Status</dt>
                            <dd class="text-gray-800">{{ $geofencePlant->status_label }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Dibuat</dt>
                            <dd class="text-gray-800">{{ $geofencePlant->created_at }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Diperbarui</dt>
                            <dd class="text-gray-800">{{ $geofencePlant->updated_at }}</dd>
                        </div>
                    </dl>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>