<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Forecast Order') }}
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

            <form method="GET" action="{{ route('admin.transaksi') }}">
                <div class="flex items-center mb-4 space-x-4">
                    <!-- Filter Bulan Dropdown Multiselect -->
                    <div class="relative inline-block text-left">
                        <button type="button" onclick="toggleDropdown()" id="bulanDropdownButton"
                            class="inline-flex justify-between w-48 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded shadow-sm hover:bg-gray-50 focus:outline-none">
                            <span id="bulanDropdownLabel">Pilih</span>
                            <svg class="w-5 h-5 ml-2 -mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.584l3.71-4.354a.75.75 0 111.14.976l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="bulanDropdownMenu"
                            class="absolute left-0 z-10 hidden w-48 mt-2 origin-top-right bg-white border border-gray-200 divide-y divide-gray-100 rounded-md shadow-lg">
                            <div class="p-2 overflow-y-auto max-h-60">
                                @php
                                    $bulanList = [
                                        'Januari',
                                        'Februari',
                                        'Maret',
                                        'April',
                                        'Mei',
                                        'Juni',
                                        'Juli',
                                        'Agustus',
                                        'September',
                                        'Oktober',
                                        'November',
                                        'Desember',
                                    ];
                                @endphp

                                @foreach ($bulanList as $i => $namaBulan)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="bulan[]" value="{{ $i + 1 }}"
                                            class="text-blue-600 rounded form-checkbox" onchange="updateLabel()">
                                        <span class="text-sm text-gray-700">{{ $namaBulan }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Filter dan Reset -->
                    <div class="flex items-center ml-4 space-x-2">
                        <button type="submit" id="filterButton"
                            class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">
                            Filter Forecast
                        </button>

                        <a href="{{ url('admin/transaksi') }}"
                            class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 border border-gray-300 rounded hover:bg-gray-300">
                            Reset
                        </a>

                        @if (request()->has('bulan') && count(request()->get('bulan')) > 0)
                            <a href="{{ route('admin.generateTotalForecast', ['bulan' => request()->get('bulan'), 'target' => 'ppic']) }}"
                                class="px-4 py-2 ml-2 text-sm font-semibold text-white bg-green-600 rounded hover:bg-green-700">
                                Generate Total Forecast For PPIC
                            </a>

                            <a href="{{ route('admin.generateTotalForecast', ['bulan' => request()->get('bulan'), 'target' => 'sales']) }}"
                                class="px-4 py-2 ml-2 text-sm font-semibold text-white bg-green-600 rounded hover:bg-green-700">
                                Generate Total Forecast For SALES
                            </a>
                        @endif
                    </div>
                </div>
            </form>


            <div class="flex flex-col mb-4 space-y-2 md:flex-row md:items-center md:justify-between md:space-y-0">
                <!-- Kiri: Form Filter -->
                <form method="GET" action="{{ route('admin.transaksi') }}" class="w-full md:w-auto">
                    <div class="flex flex-col space-y-2 md:flex-row md:items-end md:space-x-2 md:space-y-0">
                        <div class="w-full md:w-auto">
                            <label class="block mb-1 text-sm">Tanggal Awal</label>
                            <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}"
                                class="w-full p-2 border rounded md:w-auto">
                        </div>
                        <div class="w-full md:w-auto">
                            <label class="block mb-1 text-sm">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                                class="w-full p-2 border rounded md:w-auto">
                        </div>
                        <div class="flex space-x-2">
                            <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded">Filter</button>
                            <a href="{{ route('admin.transaksi') }}" class="px-4 py-2 bg-gray-300 rounded">Reset</a>
                        </div>
                    </div>
                </form>

                <!-- Kanan: Tombol Generate Forecast Period jika filter aktif -->
                @if ($tanggalAwal && $tanggalAkhir)
                    <div class="mt-2 md:mt-0">
                        <a href="{{ route('admin.generateForecast', ['tanggal_awal' => $tanggalAwal, 'tanggal_akhir' => $tanggalAkhir]) }}"
                            class="inline-block px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                            Generate Forecast Period
                        </a>
                    </div>
                @endif
            </div>

            <div class="w-full">
                <table class="w-full text-xs text-gray-700 table-fixed">
                    <thead class="bg-gray-50">
                        <tr class="text-center">
                            <th class="w-6 p-2">No</th>
                            <th class="p-2">Kode Order</th>
                            <th class="p-2">Nama Customer</th>
                            <th class="p-2">Nama Cabang</th>
                            <th class="p-2">Tanggal</th>
                            <th class="p-2">Forecast Period</th>
                            <th class="p-2">Status</th>
                            <th class="p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="p-2">{{ $loop->iteration }}</td>
                                <td class="p-2">{{ $order->kode }}</td>
                                <td class="p-2">{{ $order->user->name }}</td>
                                <td class="p-2">{{ $order->cabang->nama_cabang ?? '-' }}</td>
                                <td class="p-2">{{ \Carbon\Carbon::parse($order->tanggal)->format('d-m-Y') }}</td>
                                {{-- <td class="p-2">{{ $order->forecast_period }}</td> --}}
                                <td class="p-2">{{ $order->forecast }}</td>
                                <td class="p-2">
                                    @if ($order->status == 0)
                                        <span class="px-2 py-0.5 text-xs text-red-600 bg-red-100 rounded">Pending</span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 text-xs text-green-600 bg-green-100 rounded">Confirm</span>
                                    @endif
                                </td>
                                <td class="p-2">
                                    <div class="flex justify-center gap-1">
                                        <button onclick="downloadPDF({{ $order->id }})"
                                            class="text-red-600 hover:text-red-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <!-- Icon PDF (contoh icon file) -->
                                                <path
                                                    d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6zM13 9V3.5L18.5 9H13z" />
                                            </svg>
                                        </button>
                                        <button onclick="viewOrder({{ $order->id }})"
                                            class="text-blue-600 hover:text-blue-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <!-- Eye Icon -->
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button onclick="editOrder({{ $order->id }})"
                                            class="text-blue-600 hover:text-blue-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <!-- Edit Path -->
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5m-5-11l5 5M18 2l4 4m-2 2L9 19H4v-5L16 4z" />
                                            </svg>
                                        </button>
                                        <button onclick="deleteOrder({{ $order->id }})"
                                            @if ($order->status == 1) disabled @endif
                                            class="{{ $order->status == 1 ? 'text-gray-400 cursor-not-allowed pointer-events-none' : 'text-red-600 hover:text-red-800' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <!-- Trash Icon Path -->
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4m16 0h-4" />
                                            </svg>
                                        </button>

                                        <button
                                            @if ($order->status == 0) onclick="confirmPaid({{ $order->id }})"
        class="text-green-600 hover:text-green-800"
    @else
        class="text-gray-400 cursor-not-allowed"
        disabled @endif>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <!-- Check Icon -->
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <button onclick="sendEmail({{ $order->id }})"
                                            @if ($order->status == 1) disabled @endif
                                            class="{{ $order->status == 1 ? 'text-gray-400 cursor-not-allowed' : 'text-red-600 hover:text-red-800' }}">

                                            {{-- Icon surat --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-4 h-4"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M2.25 4.5A2.25 2.25 0 0 1 4.5 2.25h15a2.25 2.25 0 0 1 2.25 2.25v15a2.25 2.25 0 0 1-2.25 2.25h-15A2.25 2.25 0 0 1 2.25 19.5v-15ZM4.5 5.318v13.364h15V5.318l-7.5 4.909L4.5 5.318Zm1.36-.818h12.28L12 9.023 5.86 4.5Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $orders->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="edit-permintaan-modal"
        class="fixed inset-0 z-50 flex items-start justify-center hidden overflow-y-auto bg-gray-900 bg-opacity-50">
        <div class="w-full max-w-3xl max-h-screen p-6 my-10 overflow-y-auto bg-white rounded-lg shadow">
            <h3 class="mb-4 text-xl font-semibold">Edit Permintaan</h3>
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')

                <div id="edit-permintaan-list">
                    <!-- JS akan render baris di sini -->
                </div>

                <button type="button" id="add-edit-baris" class="mb-4 text-blue-600 hover:underline">
                    + Tambah Baris Permintaan
                </button>

                <div class="flex justify-end space-x-2">
                    <button type="button"
                        onclick="document.getElementById('edit-permintaan-modal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="delete-order-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
            <p class="mb-6 text-sm text-gray-700">Apakah Anda yakin ingin menghapus order ini?</p>
            <form id="delete-order-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Bayar -->
    <div id="paid-order-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Konfirmasi Forecast Order</h3>
            <p class="mb-6 text-sm text-gray-700">
                Apakah Anda yakin untuk konfirmasi forecast input pelanggan?
            </p>
            <form id="paid-order-form" method="POST">
                @csrf
                @method('PUT')
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closePaidModal()"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal View -->
    <div id="view-order-modal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden overflow-y-auto bg-gray-900 bg-opacity-50">
        <div
            class="w-full max-w-md md:max-w-xl lg:max-w-2xl max-h-[90vh] p-6 my-10 overflow-y-auto bg-white rounded-lg shadow">
            <h3 class="mb-4 text-xl font-semibold">Detail Order</h3>
            <div id="view-order-content" class="w-full">
                <!-- Tabel akan dimuat di sini -->
            </div>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="document.getElementById('view-order-modal').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-300 rounded">
                    Tutup
                </button>
            </div>
        </div>
    </div>


    <!-- Script Cascade Merk -> Ukuran -> Motif -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        const ROUTES = {
            transaksiEdit: '{{ route('transaksi.edit', ['order' => '__ORDER_ID__']) }}',
            transaksiUpdate: '{{ route('transaksi.update', ['order' => '__ORDER_ID__']) }}',
            transaksiDelete: '{{ route('transaksi.delete', ['order' => '__ORDER_ID__']) }}',
            transaksiMarkPaid: '{{ route('order.markPaid', ['order' => '__ORDER_ID__']) }}',
            getUkurans: '{{ route('get-ukurans', ['merkId' => '__MERK_ID__']) }}',
            getMotifs: '{{ route('get-motifs', ['ukuranId' => '__UKURAN_ID__']) }}'
        };
    </script>
    <script>
        function fetchUkurans(index, merkId) {
            fetch(`/admin/get-ukurans/${merkId}`)
                .then(res => res.json())
                .then(data => {
                    let ukuranSelect = document.getElementById('ukuran-' + index);
                    ukuranSelect.innerHTML = '<option value="">Pilih Ukuran</option>';
                    data.forEach(item => {
                        ukuranSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });

                    let motifSelect = document.getElementById('motif-' + index);
                    motifSelect.innerHTML = '<option value="">Pilih Motif</option>';
                });
        }

        function fetchMotifs(index, ukuranId) {
            fetch(`/admin/get-motifs/${ukuranId}`)
                .then(res => res.json())
                .then(data => {
                    let motifSelect = document.getElementById('motif-' + index);
                    motifSelect.innerHTML = '<option value="">Pilih Motif</option>';
                    data.forEach(item => {
                        motifSelect.innerHTML += `<option value="${item.name}">${item.name}</option>`;
                    });
                });
        }
    </script>
    <script>
        function editOrder(orderId) {
            // 1. Buka modal
            document.getElementById('edit-permintaan-modal').classList.remove('hidden');

            // 2. Reset container
            let listContainer = document.getElementById('edit-permintaan-list');
            listContainer.innerHTML = '';

            // 3. Ganti action form update
            let form = document.getElementById('edit-form');
            form.action = `/admin/transaksi/${orderId}/update`;

            // 4. Fetch data permintaans
            fetch(`/admin/transaksi/${orderId}/edit`)
                .then(res => res.json())
                .then(data => {
                    console.log(data);
                    data.permintaans.forEach((item, index) => {
                        addEditBaris(item, index);
                    });
                });

            // 5. Tambah baris baru jika klik + baris
            document.getElementById('add-edit-baris').onclick = function() {
                let index = document.querySelectorAll('.edit-permintaan-item').length;
                addEditBaris({}, index);
            };
        }

        function addEditBaris(item, index) {
            let listContainer = document.getElementById('edit-permintaan-list');

            let div = document.createElement('div');
            div.className = 'p-4 mb-4 border rounded-lg edit-permintaan-item';

            div.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                     <input type="hidden" name="permintaans[${index}][id]" value="${item.id ?? ''}">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Merk</label>
                        <select name="permintaans[${index}][merk_id]"
                            onchange="fetchUkuransEdit(${index}, this.value)"
                            class="w-full p-2 border rounded-lg" required>
                            <option value="">Pilih Merk</option>
                            @foreach ($merks as $merk)
                                <option value="{{ $merk->id }}">{{ $merk->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Ukuran</label>
                        <select name="permintaans[${index}][ukuran_id]"
                            id="ukuran-edit-${index}"
                            onchange="fetchMotifsEdit(${index}, this.value)"
                            class="w-full p-2 border rounded-lg" required>
                            <option value="">Pilih Ukuran</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Motif</label>
                        <select name="permintaans[${index}][motif]"
                            id="motif-edit-${index}"
                            class="w-full p-2 border rounded-lg" required>
                            <option value="">Pilih Motif</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Estimasi</label>
                        <input type="number" min="1"
                            name="permintaans[${index}][estimasi]"
                            value="${item.estimasi ?? ''}"
                            class="w-full p-2 border rounded-lg" required>
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="mt-2 text-red-600 hover:underline">
                    Hapus Baris Ini
                </button>
            `;

            listContainer.appendChild(div);

            // Preselect Merk, fetch Ukurans + Motifs jika ada data
            if (item.merk_id) {
                div.querySelector(`select[name="permintaans[${index}][merk_id]"]`).value = item.merk_id;
                fetchUkuransEdit(index, item.merk_id, item.ukuran_id, item.motif);
            }
        }

        function fetchUkuransEdit(index, merkId, selectedUkuranId = null, selectedMotif = null) {
            fetch(`/admin/get-ukurans/${merkId}`)
                .then(res => res.json())
                .then(data => {
                    let ukuranSelect = document.getElementById('ukuran-edit-' + index);
                    ukuranSelect.innerHTML = '<option value="">Pilih Ukuran</option>';
                    data.forEach(item => {
                        ukuranSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                    if (selectedUkuranId) {
                        ukuranSelect.value = selectedUkuranId;
                        fetchMotifsEdit(index, selectedUkuranId, selectedMotif);
                    }
                });
        }

        function fetchMotifsEdit(index, ukuranId, selectedMotif = null) {
            fetch(`/admin/get-motifs/${ukuranId}`)
                .then(res => res.json())
                .then(data => {
                    let motifSelect = document.getElementById('motif-edit-' + index);
                    motifSelect.innerHTML = '<option value="">Pilih Motif</option>';
                    data.forEach(item => {
                        motifSelect.innerHTML += `<option value="${item.name}">${item.name}</option>`;
                    });
                    if (selectedMotif) {
                        motifSelect.value = selectedMotif;
                    }
                });
        }
    </script>
    <script>
        function deleteOrder(orderId) {
            // Buka modal
            document.getElementById('delete-order-modal').classList.remove('hidden');
            // Pasang action form
            const form = document.getElementById('delete-order-form');
            form.action = `/admin/transaksi/${orderId}/delete`;
        }

        function closeDeleteModal() {
            document.getElementById('delete-order-modal').classList.add('hidden');
        }
    </script>

    <script>
        let selectedOrderId = null;

        function confirmPaid(orderId) {
            // Simpan ID order yang mau di-mark Paid
            selectedOrderId = orderId;
            // Tampilkan modal
            document.getElementById('paid-order-modal').classList.remove('hidden');
        }

        function closePaidModal() {
            document.getElementById('paid-order-modal').classList.add('hidden');
            selectedOrderId = null;
        }

        // Ketika form di modal disubmit
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('paid-order-form');
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop submit normal form

                if (!selectedOrderId) return;

                fetch(`/admin/send-wa/transaksi/${selectedOrderId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.wa_url) {
                            window.open(data.wa_url, '_blank');
                            location.reload();
                        } else {
                            alert(data.message || 'Gagal update status.');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        alert('Terjadi kesalahan.');
                    })
                    .finally(() => {
                        closePaidModal();
                    });
            });
        });
    </script>

    <script>
        function sendEmail(orderId) {
            if (!orderId) return;

            fetch(`/admin/send-email/transaksi/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Email berhasil dikirim ke customer!');
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal mengirim email.');
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('Terjadi kesalahan.');
                });
        }
    </script>

    <script>
        function viewOrder(orderId) {
            // Buka modal
            document.getElementById('view-order-modal').classList.remove('hidden');

            // Kosongkan konten
            const container = document.getElementById('view-order-content');
            container.innerHTML = 'Memuat data...';

            // Fetch data
            fetch(`/permintaan/${orderId}/view`)
                .then(res => res.json())
                .then(order => {
                    if (order.permintaans.length === 0) {
                        container.innerHTML = '<p class="text-sm text-gray-500">Tidak ada permintaan.</p>';
                        return;
                    }

                    let totalQty = 0;

                    let tableHtml = `
          <table class="w-full border border-collapse table-fixed">
<thead>
  <tr>
    <th class="px-4 py-2 text-left border whitespace-nowrap">Merk</th>
    <th class="px-4 py-2 text-left border">Motif</th>
    <th class="px-4 py-2 text-left border">Ukuran</th>
    <th class="px-4 py-2 text-right border">Prioritas</th>
    <th class="px-4 py-2 text-right border">Qty</th>
  </tr>
</thead>
<tbody>
      `;

                    order.permintaans.forEach(item => {
                        tableHtml += `
          <tr>
            <td class="px-4 py-2 border">${item.merk?.name || '-'}</td>
            <td class="px-4 py-2 border">${item.motif || '-'}</td>
            <td class="px-4 py-2 border">${item.ukuran?.name || '-'}</td>
            <td class="px-4 py-2 text-right border">
                ${item.prioritas ? item.prioritas.id_prioritas + ' - ' + item.prioritas.nama_prioritas : '-'}
            </td>
            <td class="px-4 py-2 text-right border">${item.estimasi || 0}</td>
          </tr>
        `;
                        totalQty += parseInt(item.estimasi) || 0;
                    });

                    tableHtml += `
          <tr>
            <td class="px-4 py-2 font-semibold border" colspan="4">Total</td>
            <td class="px-4 py-2 font-semibold text-right border">${totalQty}</td>
          </tr>
        </tbody>
        </table>
      `;

                    container.innerHTML = tableHtml;
                })
                .catch(() => {
                    container.innerHTML = '<p class="text-sm text-red-500">Gagal memuat data.</p>';
                });
        }
    </script>

    <script>
        function downloadPDF(orderId) {
            window.open(`/admin/transaksi/${orderId}/pdf`, '_blank');
        }
    </script>

    <script>
        function toggleDropdown() {
            const menu = document.getElementById('bulanDropdownMenu');
            menu.classList.toggle('hidden');
        }

        function updateLabel() {
            const checkboxes = document.querySelectorAll('input[name="bulan[]"]:checked');
            const label = document.getElementById('bulanDropdownLabel');
            const filterButton = document.getElementById('filterButton');

            if (checkboxes.length === 0) {
                label.innerText = 'Pilih';
                filterButton.classList.add('hidden');
            } else {
                const months = Array.from(checkboxes).map(cb => cb.nextElementSibling.innerText);
                label.innerText = months.join(', ');
                filterButton.classList.remove('hidden');
            }
        }

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function(e) {
            const button = document.getElementById('bulanDropdownButton');
            const menu = document.getElementById('bulanDropdownMenu');
            if (!button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // Validasi saat submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const selectedMonths = document.querySelectorAll('input[name="bulan[]"]:checked');
            if (selectedMonths.length === 0) {
                e.preventDefault();
                alert('Minimal harus memilih 1 bulan!');
            }
        });
    </script>

</x-app-layout>
