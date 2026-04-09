<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Report by Hour') }}
        </h2>

        <form method="GET" action="{{ route('admin.report') }}" class="mb-4 text-center">
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
        {{-- <form method="GET" action="{{ route('admin.monitoring') }}" class="mb-4 text-center">
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
        </form> --}}
        @if (request('bulan') && request('tahun'))
            <h1 class="mb-2 text-xl font-bold text-center">TIME REPORT</h1>
            <h2 class="mb-2 text-xl font-semibold text-center">PERIODE
                {{ strtoupper(\Carbon\Carbon::create()->month(request('bulan'))->format('F')) }} {{ request('tahun') }}
            </h2>
            {{-- <h3 class="mb-4 text-xl font-medium text-center">FINANCE ACCOUNTING TAX TANRISE</h3> --}}

            <form method="GET" action="{{ route('admin.monitoring.export') }}" class="mb-2 text-center">
                <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded">Export Excel</button>
            </form>

            {{-- <div class="p-2 overflow-auto bg-gray-100 border rounded-lg"> --}}
            <div class="overflow-auto bg-gray-100 border rounded-lg max-h-[600px]">
                <table class="w-full text-sm border border-collapse border-gray-400 table-auto">
                    <thead class="sticky top-0 z-50">
                        {{-- Baris Nama Project --}}
                        <tr class="text-white">
                            <td class="bg-white text-black sticky left-0 z-50 text-center" rowspan="3">Departement
                            </td>
                            <td class="bg-white text-black sticky left-[115px] z-50 border border-gray-300 text-center"
                                rowspan="3">Nama
                            </td>
                            @foreach ($projects as $project)
                                @php
                                    $relatedSubprojects = $subprojects->where('project_id', $project->id);
                                    $groupedByPt = $relatedSubprojects->groupBy('nama_pt');
                                    $colspan = $relatedSubprojects->count();
                                @endphp
                                @if ($colspan > 0)
                                    <th class="p-2 bg-gray-800 border border-gray-300 text-center sticky top-0 z-[40]"
                                        colspan="{{ $colspan }}">
                                        {{ $project->name }}
                                    </th>
                                @endif
                            @endforeach
                            <th class="p-2 bg-gray-600 border border-gray-300 sticky top-0 z-[40]" rowspan="3">TOTAL
                                JAM</th>

                            <th class="p-2 bg-gray-500 border border-gray-300" rowspan="4">KETERANGAN</th>
                        </tr>

                        {{-- Baris Nama PT --}}
                        <tr class="text-white text-sm bg-green-700">
                            {{-- <td class="bg-gray-100 sticky left-0 z-50"></td> --}}
                            @foreach ($projects as $project)
                                @php
                                    $relatedSubprojects = $subprojects->where('project_id', $project->id);
                                    $groupedByPt = $relatedSubprojects->groupBy('nama_pt');
                                @endphp
                                @foreach ($groupedByPt as $nama_pt => $group)
                                    <th class="p-2 bg-green-700 border border-gray-300 sticky top-[36px] z-[30]"
                                        colspan="{{ $group->count() }}">
                                        {{ $nama_pt }}
                                    </th>
                                @endforeach
                            @endforeach
                        </tr>

                        {{-- Baris Nama Sub Project --}}
                        <tr class="text-xs text-white uppercase bg-green-700">
                            {{-- <td class="bg-gray-100 sticky left-0 z-50"></td> --}}
                            @foreach ($projects as $project)
                                @php
                                    $relatedSubprojects = $subprojects->where('project_id', $project->id);
                                    $groupedByPt = $relatedSubprojects->groupBy('nama_pt');
                                @endphp
                                @foreach ($groupedByPt as $group)
                                    @foreach ($group as $subproject)
                                        <th class="px-2 bg-green-700 border border-gray-300 sticky top-[72px] z-[20]">
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
                                    @if ($index === 0)
                                        {{-- <td class="p-2 font-semibold uppercase border border-gray-300"
                                            rowspan="{{ $rowspan }}">
                                            {{ $departement }}
                                        </td> --}}
                                        <td class="p-2 font-semibold uppercase border border-gray-300 sticky left-0 bg-white z-10"
                                            rowspan="{{ $rowspan }}">
                                            {{ $departement }}
                                        </td>
                                    @endif
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
                                    @php
                                        $targetJam = $employee->total_jam ?? 160; // Default kalau gak ada
                                        $totalJamColor =
                                            $totalJamUser >= $targetJam
                                                ? 'bg-green-600 text-white'
                                                : 'bg-red-500 text-white';
                                    @endphp
                                    <td
                                        class="sticky left-[115px] p-2 font-semibold border border-gray-300 {{ $totalJamColor }}">
                                        {{ $employee->name }}</td>

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
                                            <td class="w-[300px] text-center border border-gray-300 font-semibold whitespace-nowrap cursor-pointer text-blue-600 hover:underline"
                                                onclick="showDetailModal({{ $employee->id }}, {{ $project->id }}, {{ $subproject->id }}, '{{ $subproject->nama_pt }}', '{{ $subproject->nama_sub_project }}')">
                                                {{ $totalJamSubproject }}
                                            </td>
                                        @endforeach
                                    @endforeach


                                    <td class="font-bold text-center border border-gray-300 {{ $totalJamColor }}">
                                        {{ $totalJamUser }}</td>

                                    @php
                                        $bulan = request('bulan', now()->format('m'));
                                        $tahun = request('tahun', now()->format('Y'));

                                        $startOfMonth = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
                                        $endOfMonth = $startOfMonth->copy()->endOfMonth();

                                        // Ambil semua aktivitas di bulan yang dipilih
                                        $records = $employee
                                            ->dailyActivities()
                                            ->whereBetween('tanggal', [
                                                $startOfMonth->toDateString(),
                                                $endOfMonth->toDateString(),
                                            ])
                                            ->get();

                                        // Group berdasarkan tanggal (format Y-m-d), lalu ambil satu aktivitas per tanggal
                                        $uniquePerDay = $records
                                            ->groupBy(fn($r) => \Carbon\Carbon::parse($r->tanggal)->format('Y-m-d'))
                                            ->map(fn($items) => $items->first());

                                        // Hitung berdasarkan record unik per tanggal
                                        $countCuti = $uniquePerDay->where('cuti', 1)->count();
                                        $countIjin = $uniquePerDay->where('ijin', 1)->count();
                                        $countSakit = $uniquePerDay->where('sakit', 1)->count();
                                        $countKerja = $uniquePerDay
                                            ->filter(fn($r) => !$r->cuti && !$r->ijin && !$r->sakit)
                                            ->count();

                                        // Tanggal yang diisi (unik)
                                        $tanggalDiisi = $uniquePerDay
                                            ->pluck('tanggal')
                                            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
                                            ->unique();

                                        // Semua tanggal dalam bulan
                                        $tanggalLengkap = collect(
                                            \Carbon\CarbonPeriod::create($startOfMonth, $endOfMonth),
                                        )->map(fn($d) => $d->format('Y-m-d'));

                                        // Tanggal libur = tanggal yang gak diisi
                                        $tanggalLibur = $tanggalLengkap->diff($tanggalDiisi);
                                        $countLibur = $tanggalLibur->count();
                                    @endphp

                                    <td class="text-xs text-center border border-gray-300 leading-5">
                                        <span class="block text-green-700">Kerja: {{ $countKerja }}</span>
                                        <span class="block text-yellow-600">Cuti: {{ $countCuti }}</span>
                                        <span class="block text-orange-500">Ijin Setengah Hari: {{ $countIjin }}</span>
                                        <span class="block text-red-600">Sakit: {{ $countSakit }}</span>
                                        <span class="block text-gray-600">Libur: {{ $countLibur }}</span>
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
                <h3 class="text-lg font-bold">Detail Jam per Minggu</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-black">X</button>
            </div>

            <!-- Search Input -->
            <div class="mb-4">
                <input type="number" id="searchTanggal" oninput="filterTanggal()"
                    placeholder="Cari Tanggal (misal: 29)" class="w-full px-4 py-2 border border-gray-300 rounded"
                    min="1" max="31">
            </div>

            <!-- Table -->
            <table class="w-full text-sm text-left border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">Tanggal</th>
                        <th class="p-2 border">Keterangan</th>
                        <th class="p-2 border">Waktu</th>
                    </tr>
                </thead>
                <tbody id="modalTableBody">
                    <!-- Data will be injected here -->
                </tbody>
            </table>

            <div id="noDataMessage" class="mt-4 text-center text-red-500 font-semibold hidden">
                Tidak ada data
            </div>

            <!-- Pagination -->
            {{-- <div class="flex justify-center mt-4 gap-2" id="paginationControls">
                <!-- Dynamic pagination -->
            </div> --}}

            <div id="paginationControls" class="flex justify-center mt-4 gap-2">
                <button onclick="changePage(-1)" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Prev</button>
                <span id="pageInfo" class="px-2 py-1"></span>
                <button onclick="changePage(1)" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Next</button>
            </div>
        </div>
    </div>

    <script>
        // const detailActivities = @json($detailActivities);
        // let currentPage = 1;
        // const rowsPerPage = 5;
        // let allFilteredData = [];
        // let originalFilteredData = []; // simpan backup

        // function normalize(str) {
        //     return (str ?? '').toString().trim().toLowerCase();
        // }

        // function showDetailModal(userId, projectId, subprojectId, namaPt, namaSubProject) {
        //     const detail = detailActivities?.[userId]?.[projectId]?.[subprojectId] || {};
        //     allFilteredData = [];

        //     Object.values(detail).forEach(detailWeek => {
        //         const filteredDetail = detailWeek.filter(entry =>
        //             normalize(entry.nama_pt) === normalize(namaPt) &&
        //             normalize(entry.nama_sub_project) === normalize(namaSubProject)
        //         );

        //         if (filteredDetail.length > 0) {
        //             allFilteredData.push(...filteredDetail);
        //         }
        //     });

        //     originalFilteredData = [...allFilteredData]; // backup data asli

        //     currentPage = 1;
        //     renderTableData();
        //     // renderPagination();
        //     document.getElementById('searchTanggal').value = '';
        //     document.getElementById('detailModal').classList.remove('hidden');
        // }

        // function renderTableData() {
        //     const tbody = document.getElementById('modalTableBody');
        //     const noDataDiv = document.getElementById('noDataMessage');
        //     tbody.innerHTML = '';

        //     const start = (currentPage - 1) * rowsPerPage;
        //     const end = start + rowsPerPage;
        //     const paginatedItems = allFilteredData.slice(start, end);

        //     if (paginatedItems.length === 0) {
        //         noDataDiv.classList.remove('hidden');
        //         return;
        //     } else {
        //         noDataDiv.classList.add('hidden');
        //     }

        //     paginatedItems.forEach(entry => {
        //         const row = document.createElement('tr');

        //         const tanggalCell = document.createElement('td');
        //         tanggalCell.className = 'p-2 text-center border';
        //         tanggalCell.textContent = `${entry.tanggal} (week ${entry.week})`;
        //         row.appendChild(tanggalCell);

        //         const keteranganCell = document.createElement('td');
        //         keteranganCell.className = 'p-2 text-center border';
        //         keteranganCell.textContent = entry.keterangan ?? '-';
        //         row.appendChild(keteranganCell);

        //         const jamCell = document.createElement('td');
        //         jamCell.className = 'p-2 text-center border';
        //         const jam = parseFloat(entry.jam);
        //         jamCell.textContent = isNaN(jam) ? '0 jam' : `${jam % 1 === 0 ? jam : jam.toFixed(1)} jam`;
        //         row.appendChild(jamCell);

        //         tbody.appendChild(row);
        //     });
        // }

        // function renderPagination() {
        //     const totalPages = Math.ceil(allFilteredData.length / rowsPerPage);
        //     const paginationContainer = document.getElementById('paginationControls');
        //     paginationContainer.innerHTML = '';

        //     for (let i = 1; i <= totalPages; i++) {
        //         const btn = document.createElement('button');
        //         btn.className = `px-3 py-1 border rounded ${i === currentPage ? 'bg-blue-500 text-white' : 'bg-gray-100'}`;
        //         btn.textContent = i;
        //         btn.onclick = () => {
        //             currentPage = i;
        //             renderTableData();
        //             renderPagination();
        //         };
        //         paginationContainer.appendChild(btn);
        //     }
        // }

        // function filterDataByTanggal() {
        //     const tanggalInput = document.getElementById('searchTanggal').value.trim();
        //     const noDataDiv = document.getElementById('noDataMessage');

        //     if (tanggalInput === '') {
        //         allFilteredData = [...originalFilteredData]; // restore backup
        //         noDataDiv.classList.add('hidden');
        //         currentPage = 1;
        //         renderTableData();
        //         // renderPagination();
        //         return;
        //     }

        //     const targetDate = parseInt(tanggalInput);
        //     if (isNaN(targetDate)) return;

        //     allFilteredData = originalFilteredData.filter(entry => {
        //         const day = parseInt(entry.tanggal.split('/')[0]); // ambil tanggal (dd)
        //         return day === targetDate;
        //     });

        //     currentPage = 1;
        //     renderTableData();
        //     // renderPagination();

        //     // Tampilkan pesan jika kosong
        //     if (allFilteredData.length === 0) {
        //         const bulan = {{ request('bulan') }};
        //         const tahun = {{ request('tahun') }};
        //         const monthName = new Date(tahun, bulan - 1).toLocaleString('id-ID', {
        //             month: 'long'
        //         });

        //         noDataDiv.textContent =
        //             `Tidak ada data pada tanggal ${targetDate} ${monthName.charAt(0).toUpperCase() + monthName.slice(1)} ${tahun}`;
        //         noDataDiv.classList.remove('hidden');
        //     } else {
        //         noDataDiv.classList.add('hidden');
        //     }
        // }

        // function closeModal() {
        //     document.getElementById('detailModal').classList.add('hidden');
        // }

        const detailActivities = @json($detailActivities);
        let lastModalData = []; // Untuk simpan data yang ditampilkan di modal
        let currentPage = 1;
        const perPage = 5;
        let filteredData = null; // Simpan hasil filter di sini

        function normalize(str) {
            return (str ?? '').toString().trim().toLowerCase();
        }

        function showDetailModal(userId, projectId, subprojectId, namaPt, namaSubProject) {
            const tbody = document.getElementById('modalTableBody');
            tbody.innerHTML = '';
            lastModalData = [];
            currentPage = 1;

            const detail = detailActivities?.[userId]?.[projectId]?.[subprojectId] || {};

            Object.values(detail).forEach(detailWeek => {
                const filteredDetail = detailWeek.filter(entry =>
                    normalize(entry.nama_pt) === normalize(namaPt) &&
                    normalize(entry.nama_sub_project) === normalize(namaSubProject)
                );

                filteredDetail.forEach(entry => {
                    const tanggal = new Date(entry.tanggal.split('/').reverse().join('-'));
                    const dayOfMonth = tanggal.getDate();

                    lastModalData.push({
                        tanggal: entry.tanggal,
                        day: dayOfMonth,
                        week: entry.week,
                        keterangan: entry.keterangan ?? '-',
                        jam: parseFloat(entry.jam) || 0
                    });
                });
            });

            renderTable(lastModalData); // render semua data saat awal buka modal
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function renderTable(dataInput, searchKeyword = null) {
            const tbody = document.getElementById('modalTableBody');
            tbody.innerHTML = '';

            const data = dataInput ?? (filteredData ?? lastModalData); // ambil data yang sesuai
            const totalPages = Math.ceil(data.length / perPage);
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const paginatedData = data.slice(start, end);

            document.getElementById('paginationControls').style.display = data.length > perPage ? 'flex' : 'none';
            document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;

            if (paginatedData.length === 0 && searchKeyword !== null) {
                const bulan = {{ request('bulan') }};
                const tahun = {{ request('tahun') }};
                const bulanNama = new Date(tahun, bulan - 1).toLocaleString('default', {
                    month: 'long'
                });

                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 3;
                cell.className = 'p-4 text-center text-red-600 font-semibold';
                cell.textContent =
                    `Tidak ada data pada tanggal ${searchKeyword} ${capitalizeFirstLetter(bulanNama)} ${tahun}`;
                row.appendChild(cell);
                tbody.appendChild(row);
                return;
            }

            paginatedData.forEach(entry => {
                const row = document.createElement('tr');

                const tanggalCell = document.createElement('td');
                tanggalCell.className = 'p-2 text-center border';
                tanggalCell.textContent = `${entry.tanggal} (week ${entry.week})`;
                row.appendChild(tanggalCell);

                const ketCell = document.createElement('td');
                ketCell.className = 'p-2 text-center border';
                ketCell.textContent = entry.keterangan;
                row.appendChild(ketCell);

                const jamCell = document.createElement('td');
                jamCell.className = 'p-2 text-center border';
                jamCell.textContent = `${(entry.jam % 1 === 0 ? entry.jam : entry.jam.toFixed(1))} jam`;
                row.appendChild(jamCell);

                tbody.appendChild(row);
            });
        }

        function changePage(direction) {
            const dataToUse = filteredData ?? lastModalData;
            const totalPages = Math.ceil(dataToUse.length / perPage);
            currentPage += direction;

            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages) currentPage = totalPages;

            renderTable(dataToUse);
        }

        function filterTanggal() {
            const keyword = document.getElementById('searchTanggal').value.trim();
            currentPage = 1;

            if (!keyword) {
                filteredData = null;
                renderTable(lastModalData);
                return;
            }

            const searchNum = parseInt(keyword, 10);
            filteredData = lastModalData.filter(row => row.day === searchNum);

            renderTable(filteredData, keyword);
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>

</x-app-layout>
