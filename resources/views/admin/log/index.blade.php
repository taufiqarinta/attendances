<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Log Aktivitas') }}
        </h2>
    </x-slot>

    <div class="px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="p-2 mb-4 text-sm text-white bg-green-500 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex flex-col mb-4 space-y-2 md:flex-row md:items-center md:justify-between md:space-y-0">
                <!-- Filter tanggal -->
                <form method="GET" class="flex items-center mb-4 space-x-2">
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="px-2 py-1 text-xs border rounded">
                    <span class="text-xs">s/d</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="px-2 py-1 text-xs border rounded">

                    <button type="submit" class="px-3 py-1 text-xs text-white bg-blue-500 rounded">Filter</button>

                    <!-- Tombol Reset -->
                    <a href="{{ route('admin.log') }}"
                        class="px-3 py-1 text-xs text-white bg-gray-500 rounded">Reset</a>
                </form>
            </div>

            <div class="w-full">
                <table class="w-full text-xs text-gray-700 table-fixed">
                    <thead class="bg-gray-50">
                        <tr class="text-center">
                            <th class="w-6 p-2">No</th>
                            <th class="p-2">Aktivitas</th>
                            <th class="p-2">Log</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($logs as $index => $log)
                            @php
                                if ($log->status == 1) {
                                    $logTime = $log->updated_at;
                                    $message = "<span class='text-green-500'>Order dengan kode {$log->kode} telah dikonfirmasi oleh "
                                    . ($log->approval ? $log->approval->name : 'Admin') 
                                    . "</span>";
                                } elseif ($log->status == 0) {
                                    if ($log->created_at == $log->updated_at) {
                                        $logTime = $log->created_at;
                                        $message = "<span class='text-orange-500'>{$log->user->name} telah menyimpan data order - {$log->kode}</span>";
                                    } else {
                                        $logTime = $log->updated_at;
                                        $message = "<span class='text-blue-500'>{$log->user->name} telah mengubah data order - {$log->kode}</span>";
                                    }
                                } else {
                                    $logTime = $log->updated_at;
                                    $message = "<span class='text-gray-500'>Status tidak diketahui</span>";
                                }

                                $logTime = \Carbon\Carbon::parse($logTime)->format('d-m-Y H:i');
                            @endphp

                            <tr>
                                <td class="p-2">{{ ($logs->currentPage() - 1) * $logs->perPage() + $index + 1 }}</td>
                                <td class="p-2">{!! $message !!}</td>
                                <td class="p-2">{{ $logTime }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-2">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
