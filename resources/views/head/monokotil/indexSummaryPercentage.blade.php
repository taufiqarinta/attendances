<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Report by Percentage') }}
        </h2>

        <form method="GET" action="{{ route('head.reportPercentage') }}" class="mb-4 text-center">
            <select name="bulan" class="p-2 border rounded">
                <option value="">Pilih Bulan</option>
                @foreach (range(1, 12) as $b)
                    <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($b)->format('F') }}
                    </option>
                @endforeach
            </select>

            <select name="tahun" class="p-2 border rounded">
                <option value="">Pilih Tahun</option>
                @for ($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>

            <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded">Tampilkan</button>
        </form>

    </x-slot>

    <div class="container p-4 mx-auto">
        @if (request('bulan') && request('tahun'))
            <h1 class="mb-2 text-xl font-bold text-center">TIME REPORT</h1>
            <h2 class="mb-2 text-xl font-semibold text-center">PERIODE
                {{ strtoupper(\Carbon\Carbon::create()->month(request('bulan'))->format('F')) }} {{ request('tahun') }}
            </h2>
            {{-- <h3 class="mb-4 text-xl font-medium text-center">FINANCE ACCOUNTING TAX TANRISE</h3> --}}

            {{-- <form method="GET" action="{{ route('admin.monitoring.export') }}" class="mb-2 text-center">
                <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded">Export Excel</button>
            </form> --}}

            <div class="p-2 overflow-auto bg-gray-100 border rounded-lg max-h-[600px]">
                <table class="w-full text-sm border border-collapse border-gray-400 table-auto">
                    <thead class="sticky top-0 z-50">
                        <tr class="text-white">
                            {{-- <td class="bg-white text-black sticky left-0 z-50 text-center" rowspan="3">Departement
                            </td> --}}
                            <td class="bg-white text-black sticky left-0 z-50 border border-gray-300 text-center"
                                rowspan="3">Nama
                            </td>
                            @foreach ($projects as $project)
                                @php
                                    $relatedSubprojects = $subprojects->where('project_id', $project->id);
                                    $groupedByPt = $relatedSubprojects->groupBy('nama_pt');
                                @endphp
                                @foreach ($groupedByPt as $nama_pt => $group)
                                    <th class="p-2 bg-green-700 border border-gray-300 sticky top-0 z-[30]"
                                        colspan="{{ $group->count() }}">
                                        {{ $nama_pt }}
                                    </th>
                                @endforeach
                            @endforeach
                            <th class="p-2 bg-gray-500 border border-gray-300 sticky top-0 z-[30]" rowspan="4">TOTAL
                                JAM</th>
                        </tr>
                        <tr class="text-xs text-white uppercase bg-green-700">
                            {{-- <td class="sticky left-0 z-10 bg-gray-100"></td> --}}
                            @foreach ($projects as $project)
                                @php
                                    $relatedSubprojects = $subprojects->where('project_id', $project->id);
                                    $groupedByPt = $relatedSubprojects->groupBy('nama_pt');
                                @endphp
                                @foreach ($groupedByPt as $group)
                                    @foreach ($group as $subproject)
                                        <th class="px-2 bg-green-700 border border-gray-300 sticky top-[40px] z-[20]">
                                            {{ $subproject->nama_sub_project }}
                                        </th>
                                    @endforeach
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grouped = $users->groupBy(fn($user) => $user->departement->name ?? 'LAINNYA');
                        @endphp

                        @foreach ($grouped as $departement => $employees)
                            @php $rowspan = $employees->count(); @endphp
                            @foreach ($employees as $index => $employee)
                                <tr>
                                    {{-- @if ($index === 0)
                                        <td class="sticky left-0 z-10 p-2 font-semibold uppercase bg-white border border-gray-300"
                                            rowspan="{{ $rowspan }}">
                                            {{ $departement }}
                                        </td>
                                    @endif --}}
                                    <td class="p-2 font-semibold bg-white border border-gray-300 sticky left-0">{{ $employee->name }}, ({{ $departement }})</td>

                                    @foreach ($projects as $project)
                                        @php
                                            $relatedSubprojects = $subprojects->where('project_id', $project->id);
                                        @endphp
                                        @foreach ($relatedSubprojects as $subproject)
                                            @php
                                                $totalJamSubproject = 0;
                                                if (
                                                    isset(
                                                        $groupedActivities[$employee->id][$project->id][
                                                            $subproject->id
                                                        ],
                                                    )
                                                ) {
                                                    $totalJamSubproject = array_sum(
                                                        $groupedActivities[$employee->id][$project->id][
                                                            $subproject->id
                                                        ],
                                                    );
                                                }
                                            @endphp
                                            {{-- The following line was originally clickable, now commented out --}}
                                            {{-- <td class="w-[300px] text-center border border-gray-300 font-semibold whitespace-nowrap cursor-pointer text-blue-600 hover:underline"
                                                onclick="showDetailModal({{ $employee->id }}, {{ $project->id }}, {{ $subproject->id }})">
                                                {{ $totalJamSubproject }}
                                            </td> --}}
                                            @php
                                                $percent = 0;
                                                if ($employee->total_jam > 0) {
                                                    $percent = round(
                                                        ($totalJamSubproject / $employee->total_jam) * 100,
                                                        1,
                                                    );
                                                }
                                            @endphp

                                            {{-- The following line is also made non-clickable --}}
                                            <td
                                                class="w-[300px] text-center border border-gray-300 font-semibold whitespace-nowrap">
                                                {{ $percent }}%
                                            </td>
                                        @endforeach
                                    @endforeach

                                    @php
                                        $totalJamUser = 0;
                                        if (isset($groupedActivities[$employee->id])) {
                                            foreach ($groupedActivities[$employee->id] as $projectData) {
                                                foreach ($projectData as $subprojectData) {
                                                    foreach ($subprojectData as $weekValue) {
                                                        $totalJamUser += $weekValue;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    {{-- <td class="font-bold text-center border border-gray-300">{{ $totalJamUser }}</td> --}}

                                    @php
                                        $persenTotal =
                                            $employee->total_jam > 0
                                                ? round(($totalJamUser / $employee->total_jam) * 100, 1)
                                                : 0;
                                    @endphp
                                    <td class="font-bold text-center border border-gray-300">
                                        {{ $persenTotal }}%
                                    </td>

                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Modal --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="relative max-w-md p-6 mx-auto mt-20 bg-white rounded-lg shadow-lg">
            <div class="flex justify-between mb-4">
                <h3 class="text-lg font-bold">Detail Jam per Minggu(Week)</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-black">X</button>
            </div>
            <table class="w-full text-sm text-left border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">Week</th>
                        <th class="p-2 border">Jam</th>
                    </tr>
                </thead>
                <tbody id="modalTableBody">

                </tbody>
            </table>
        </div>
    </div>

    <script>
        const groupedActivities = @json($groupedActivities);

        function showDetailModal(userId, projectId, subprojectId) {
            const weeks = ['w1', 'w2', 'w3', 'w4', 'w5'];
            const tbody = document.getElementById('modalTableBody');
            tbody.innerHTML = '';

            const data = groupedActivities?.[userId]?.[projectId]?.[subprojectId] || {};

            weeks.forEach(week => {
                const jam = data[week] ?? 0;
                const row = `<tr>
                <td class="p-2 text-center border">${week}</td>
                <td class="p-2 text-center border">${jam}</td>
            </tr>`;
                tbody.innerHTML += row;
            });
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>

</x-app-layout>
