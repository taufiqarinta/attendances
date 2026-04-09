<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Report Stock Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form id="reportForm" method="POST" action="{{ route('reportstockspg.store') }}">
                        @csrf
                        
                        <!-- Header Information -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 p-4 bg-gray-50 rounded-lg">
                        <!-- Pilih Toko -->
                        <div>
                            <x-input-label for="toko_id" :value="__('Pilih Toko')" />
                            <select id="toko_id" name="toko_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Pilih Toko</option>
                                @foreach($tokoList as $toko)
                                    <option value="{{ $toko->id }}" 
                                        {{ old('toko_id', $initialData['toko_id'] ?? '') == $toko->id ? 'selected' : '' }}>
                                        {{ $toko->nama_toko }} - {{ $toko->kota }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('toko_id')" class="mt-2" />
                        </div>
                        
                        <!-- Tahun, Bulan, Minggu -->
                        <div>
                            <x-input-label for="tahun" :value="__('Tahun')" />
                            <select id="tahun" name="tahun" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @for($year = date('Y') - 1; $year <= date('Y') + 1; $year++)
                                    <option value="{{ $year }}" 
                                        {{ old('tahun', $initialData['tahun'] ?? date('Y')) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                            <x-input-error :messages="$errors->get('tahun')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="bulan" :value="__('Bulan')" />
                            <select id="bulan" name="bulan" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @foreach([
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ] as $num => $name)
                                    <option value="{{ $num }}" 
                                        {{ old('bulan', $initialData['bulan'] ?? date('n')) == $num ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('bulan')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="minggu_ke" :value="__('Minggu Ke')" />
                            <select id="minggu_ke" name="minggu_ke" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @for($week = 1; $week <= 5; $week++)
                                    <option value="{{ $week }}" 
                                        {{ old('minggu_ke', $initialData['minggu_ke'] ?? 1) == $week ? 'selected' : '' }}>
                                        Minggu {{ $week }}
                                    </option>
                                @endfor
                            </select>
                            <x-input-error :messages="$errors->get('minggu_ke')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="tanggal" :value="__('Tanggal Report')" />
                            <x-text-input id="tanggal" 
                                        class="block mt-1 w-full" 
                                        type="date" 
                                        name="tanggal" 
                                        :value="old('tanggal', now()->toDateString())" 
                                        required />
                            <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-end">
                            <button type="button" id="loadPreviousStockBtn" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Ambil Stock Minggu Sebelumnya
                            </button>
                        </div>
                    </div>

                        <!-- Items Table -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Detail Stock</h3>
                                <button type="button" id="addItemBtn" 
                                        class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    + Tambah Item
                                </button>
                            </div>

                            <!-- Container untuk tabel dengan scroll horizontal -->
                            <div class="table-container">
                                <div class="overflow-x-scroll">
                                    <table class="responsive-table">
                                        <thead>
                                            <tr>
                                                <th class="column-nama">Nama Barang</th>
                                                <th class="column-kode">Kode Item</th>
                                                <th class="column-ukuran">Ukuran</th>
                                                <th class="column-stock">Stock</th>
                                                <th class="column-masuk">Qty Masuk (Box)</th>
                                                <th class="column-catatan">Catatan</th>
                                                <th class="column-aksi">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200" id="itemsBody">
                                            <!-- Items will be added here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="noItemsMessage" class="text-center py-8 text-gray-500">
                                Belum ada item. Klik "Tambah Item" untuk menambahkan.
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end mt-8 space-x-4">
                            <a href="{{ route('reportstockspg.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                                Batal
                            </a>
                            <x-primary-button type="button" id="submitBtn">
                                {{ __('Simpan Report') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Template untuk row baru -->
    <template id="itemRowTemplate">
        <tr class="item-row">
            <td class="column-nama">
                <select class="item-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        required>
                    <option value="">Pilih Barang</option>
                </select>
            </td>
            <td class="column-kode">
                <input type="text" class="itemcode-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100" 
                    readonly>
            </td>
            <td class="column-ukuran">
                <input type="text" class="ukuran-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100" 
                    readonly>
            </td>
            <td class="column-stock">
                <input type="number" class="stock-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                    min="0" value="0" required
                    onfocus="if(this.value=='0') this.value='';"
                    onblur="if(this.value=='') this.value='0';">
            </td>
            <td class="column-masuk">
                <input type="number" class="qty-masuk-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                    min="0" value="0" required
                    onfocus="if(this.value=='0') this.value='';"
                    onblur="if(this.value=='') this.value='0';">
            </td>
            <td class="column-catatan">
                <input type="text" class="catatan-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                    maxlength="500">
            </td>
            <td class="column-aksi">
                <button type="button" class="remove-item-btn text-red-600 hover:text-red-900 text-sm">
                    Hapus
                </button>
            </td>
        </tr>
    </template>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>

        /* Auto-notification styling */
        .auto-notification {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Select2 Styling */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: white;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 0.75rem;
            color: #374151;
            font-size: 0.875rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 0.5rem;
        }
        
        .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        /* Table Container Styling */
        .table-container {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: white;
            overflow: hidden;
        }

        .overflow-x-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .overflow-x-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-scroll::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 4px;
        }

        .overflow-x-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        .overflow-x-scroll::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        /* Responsive Table Styling */
        .responsive-table {
            width: 100%;
            min-width: 1000px;
            border-collapse: collapse;
        }

        .responsive-table th,
        .responsive-table td {
            padding: 0.75rem 0.5rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .responsive-table th {
            background-color: #f9fafb;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .responsive-table td {
            font-size: 0.875rem;
            color: #374151;
        }

        .responsive-table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Lebar kolom spesifik */
        .column-nama { 
            width: 300px; 
            min-width: 300px; 
        }
        .column-kode { 
            width: 120px; 
            min-width: 120px; 
        }
        .column-ukuran { 
            width: 100px; 
            min-width: 100px; 
        }
        .column-stock,
        .column-masuk { 
            width: 100px; 
            min-width: 100px; 
        }
        .column-catatan { 
            width: 150px; 
            min-width: 150px; 
        }
        .column-aksi { 
            width: 80px; 
            min-width: 80px; 
            text-align: center;
        }

        /* Styling untuk input dan select dalam tabel */
        .responsive-table input,
        .responsive-table select {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            width: 100%;
            box-sizing: border-box;
        }

        .responsive-table input[type="number"] {
            text-align: center;
        }

        .responsive-table .bg-gray-100 {
            background-color: #f9fafb;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .table-container {
                margin: 0;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
            
            .responsive-table {
                min-width: 1000px;
            }
            
            .max-w-9xl {
                max-width: 100%;
                padding: 0 0.5rem;
            }
            
            .p-6 {
                padding: 1rem;
            }
        }

        /* Counter styling */
        .flex.justify-between.items-center.mb-4 {
            align-items: center;
        }

        .text-sm.text-gray-600 {
            font-size: 0.875rem;
            color: #6b7280;
        }

        #itemCount {
            font-weight: 600;
            color: #4b5563;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        let itemCounter = 0;
        let selectedItems = new Set();

        const initialTokoId = $('#toko_id').val();
        if (initialTokoId) {
            // Trigger change event to load data
            setTimeout(() => {
                $('#toko_id').trigger('change');
            }, 500);
        } else {
            // Otherwise add one empty row
            addNewRow();
        }
        
        // Function untuk update semua index
        function updateAllIndexes() {
            const rows = $('#itemsBody tr');
            let newIndex = 1;
            
            rows.each(function() {
                const row = $(this);
                const oldIndex = row.data('original-index') || newIndex;
                
                // Update semua nama atribut
                row.find('[name*="items["]').each(function() {
                    const name = $(this).attr('name');
                    const newName = name.replace(/items\[(\d+)\]/, `items[${newIndex}]`);
                    $(this).attr('name', newName);
                });
                
                // Update data-row untuk tombol hapus
                row.find('.remove-row-btn').attr('data-row', newIndex);
                
                // Update row ID
                row.attr('id', `row-${newIndex}`);
                row.data('original-index', newIndex);
                
                newIndex++;
            });
            
            itemCounter = newIndex - 1;
            updateItemCount();
        }
        
        // Update item count display
        function updateItemCount() {
            const count = $('#itemsBody tr').length;
            $('#itemCount').text(count);
        }
        
        // Fungsi untuk menambah row baru
        function addNewRow(itemData = null) {
            itemCounter++;
            
            let itemCode = itemData ? itemData.item_code : '';
            let itemName = itemData ? itemData.nama_barang : '';
            let ukuran = itemData ? itemData.ukuran : '';
            let stock = itemData ? itemData.stock : 0;
            let qtyMasuk = itemData ? itemData.qty_masuk : 0;
            let catatan = itemData && itemData.catatan !== null && itemData.catatan !== undefined ? itemData.catatan : '';
            let selectedOption = itemData ? `<option value="${itemCode}" selected>${itemCode} - ${itemName} - ${ukuran}</option>` : '';
            
            const newRow = `
                <tr class="item-row" id="row-${itemCounter}">
                    <td class="column-nama">
                        <select class="item-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                name="items[${itemCounter}][item_code]" required>
                            <option value="">Pilih Barang</option>
                            ${selectedOption}
                        </select>
                    </td>
                    <td class="column-kode">
                        <input type="text" class="itemcode-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100" 
                            name="items[${itemCounter}][item_code_display]" value="${itemCode}" readonly>
                    </td>
                    <td class="column-ukuran">
                        <input type="text" class="ukuran-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100" 
                            name="items[${itemCounter}][ukuran]" value="${ukuran}" readonly>
                    </td>
                    <td class="column-stock">
                        <input type="number" class="stock-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                            name="items[${itemCounter}][stock]" min="0" value="${stock}" required
                            onfocus="if(this.value=='0') this.value='';"
                            onblur="if(this.value=='') this.value='0';">
                    </td>
                    <td class="column-masuk">
                        <input type="number" class="qty-masuk-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                            name="items[${itemCounter}][qty_masuk]" min="0" value="${qtyMasuk}" required
                            onfocus="if(this.value=='0') this.value='';"
                            onblur="if(this.value=='') this.value='0';">
                    </td>
                    <td class="column-catatan">
                        <input type="text" class="catatan-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                            name="items[${itemCounter}][catatan]" value="${catatan}" maxlength="500">
                    </td>
                    <td class="column-aksi">
                        <button type="button" class="remove-row-btn text-red-600 hover:text-red-900 text-sm" data-row="${itemCounter}">
                            Hapus
                        </button>
                    </td>
                </tr>
            `;
            
            $('#itemsBody').append(newRow);
            $('#noItemsMessage').hide();
            
            // Set data attribute untuk row baru
            const newRowElement = $(`#row-${itemCounter}`);
            newRowElement.data('original-index', itemCounter);
            
            // Initialize Select2 for the new select
            const selectElement = newRowElement.find('.item-select');
            
            if (itemData) {
                // Jika ada data, initialize Select2 dengan data yang sudah ada
                selectElement.select2({
                    placeholder: 'Cari barang...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: selectElement.closest('.table-container'),
                    data: [{
                        id: itemCode,
                        text: `${itemCode} - ${itemName} - ${ukuran}`
                    }],
                    ajax: {
                        url: '{{ route("api.stock-items") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page
                            };
                        },
                        processResults: function (apiData) {
                            return {
                                results: apiData.map(apiItem => ({
                                    id: apiItem.item_code,
                                    text: apiItem.text,
                                    item_code: apiItem.item_code,
                                    item_name: apiItem.item_name,
                                    ukuran: apiItem.ukuran
                                }))
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 1,
                    templateResult: formatItem,
                    templateSelection: formatItemSelection
                }).val(itemCode).trigger('change');
                
                selectedItems.add(itemCode);
            } else {
                // Initialize empty Select2
                initSelect2(selectElement);
            }
        }
        
        // Initialize Select2
        function initSelect2(selectElement) {
            selectElement.select2({
                placeholder: 'Cari barang...',
                allowClear: true,
                width: '100%',
                dropdownParent: selectElement.closest('.table-container'),
                ajax: {
                    url: '{{ route("api.stock-items") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(item => ({
                                id: item.item_code,
                                text: item.text,
                                item_code: item.item_code,
                                item_name: item.item_name,
                                ukuran: item.ukuran
                            }))
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                templateResult: formatItem,
                templateSelection: formatItemSelection
            }).on('select2:select', function(e) {
                const data = e.params.data;
                const row = $(this).closest('tr');
                
                // Check for duplicates
                if (selectedItems.has(data.item_code)) {
                    alert('Item ini sudah ada dalam daftar. Harap pilih item yang berbeda.');
                    $(this).val('').trigger('change');
                    return;
                }
                
                // Update Item Code and Ukuran fields
                row.find('.itemcode-input').val(data.item_code);
                row.find('.ukuran-input').val(data.ukuran || '-');
                
                // Add to selected items
                selectedItems.add(data.item_code);
            }).on('select2:unselect', function() {
                const row = $(this).closest('tr');
                const itemCode = row.find('.itemcode-input').val();
                
                // Clear Item Code and Ukuran fields
                row.find('.itemcode-input').val('');
                row.find('.ukuran-input').val('');
                
                // Remove from selected items
                if (itemCode) {
                    selectedItems.delete(itemCode);
                }
            });
        }
        
        function formatItem(item) {
            if (item.loading) return item.text;
            return $(`<div class="py-1">${item.text}</div>`);
        }
        
        function formatItemSelection(item) {
            return item.text || item.item_name || item.item_code || 'Pilih Barang';
        }
        
        // Tombol tambah item
        $('#addItemBtn').on('click', function() {
            addNewRow();
        });
        
        // Event delegation untuk tombol hapus
        $(document).on('click', '.remove-row-btn', function() {
            const rowId = $(this).data('row');
            const row = $(`#row-${rowId}`);
            const itemCode = row.find('.item-select').val();
            
            if (itemCode) {
                selectedItems.delete(itemCode);
            }
            
            row.remove();
            checkEmptyTable();
            updateAllIndexes(); // Update semua index setelah menghapus
        });
        
        // Check if table is empty
        function checkEmptyTable() {
            if ($('#itemsBody tr').length === 0) {
                $('#noItemsMessage').show();
            }
        }
        
        // Auto-load latest stock when toko is selected
        // $('#toko_id').on('change', function() {
        //     const tokoId = $(this).val();
            
        //     if (!tokoId) {
        //         return;
        //     }
            
        //     // Show loading
        //     $('#itemsBody').html('<tr><td colspan="7" class="text-center py-4"><div class="animate-pulse">Memuat stock terakhir...</div></td></tr>');
            
        //     $.ajax({
        //         url: '{{ route("api.latest-stock") }}',
        //         method: 'POST',
        //         data: {
        //             _token: '{{ csrf_token() }}',
        //             toko_id: tokoId
        //         },
        //         success: function(response) {
        //             if (response.success) {
        //                 // Clear existing rows
        //                 $('#itemsBody').empty();
        //                 selectedItems.clear();
        //                 itemCounter = 0;
                        
        //                 // Update form fields with latest report info
        //                 if (response.report) {
        //                     // Auto-suggest tahun, bulan, minggu berikutnya
        //                     let nextWeek = response.report.minggu_ke + 1;
        //                     let nextMonth = response.report.bulan;
        //                     let nextYear = response.report.tahun;
                            
        //                     // Jika minggu > 5, pindah ke bulan berikutnya
        //                     if (nextWeek > 5) {
        //                         nextWeek = 1;
        //                         nextMonth = response.report.bulan + 1;
        //                         if (nextMonth > 12) {
        //                             nextMonth = 1;
        //                             nextYear = response.report.tahun + 1;
        //                         }
        //                     }
                            
        //                     // Set tanggal default (hari ini)
        //                     $('#tahun').val(nextYear);
        //                     $('#bulan').val(nextMonth);
        //                     $('#minggu_ke').val(nextWeek);
        //                     $('#tanggal').val('{{ now()->toDateString() }}');
        //                 }
                        
        //                 // Add rows from latest stock
        //                 if (response.items && response.items.length > 0) {
        //                     response.items.forEach(function(item) {
        //                         addNewRow(item);
        //                     });
                            
        //                     // Update semua index
        //                     updateAllIndexes();
                            
        //                     // Show success message
        //                     if (response.report) {
        //                         const infoMsg = `Loaded stock dari report terakhir: ${response.report.kode_report} (Minggu ${response.report.minggu_ke}/${response.report.bulan}/${response.report.tahun})`;
        //                         showNotification(infoMsg, 'info');
        //                     }
        //                 } else {
        //                     // Jika tidak ada item, tambahkan 1 row kosong
        //                     addNewRow();
        //                     $('#noItemsMessage').show();
        //                 }
        //             } else {
        //                 // Jika tidak ada data stock sebelumnya, tambahkan 1 row kosong
        //                 $('#itemsBody').empty();
        //                 addNewRow();
        //                 $('#noItemsMessage').show();
        //                 showNotification('Belum ada data stock untuk toko ini. Silakan tambahkan item secara manual.', 'warning');
        //             }
        //         },
        //         error: function(xhr) {
        //             console.error('Error loading latest stock:', xhr.responseText);
        //             $('#itemsBody').empty();
        //             addNewRow();
        //             $('#noItemsMessage').show();
        //             showNotification('Terjadi kesalahan saat memuat data stock.', 'error');
        //         }
        //     });
        // });
        
        // Load previous week stock (backup functionality)
        $('#loadPreviousStockBtn').on('click', function() {
            const tokoId = $('#toko_id').val();
            const tahun = $('#tahun').val();
            const bulan = $('#bulan').val();
            const mingguKe = $('#minggu_ke').val();
            
            if (!tokoId) {
                alert('Harap pilih toko terlebih dahulu.');
                return;
            }
            
            if (!tahun || !bulan || !mingguKe) {
                alert('Harap lengkapi tahun, bulan, dan minggu.');
                return;
            }
            
            // Tampilkan loading
            $(this).prop('disabled', true).html('<span class="animate-spin mr-2">⟳</span> Memuat...');
            
            $.ajax({
                url: '{{ route("api.previous-stock") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    toko_id: tokoId,
                    tahun: tahun,
                    bulan: bulan,
                    minggu_ke: mingguKe
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        // Clear existing rows
                        $('#itemsBody').empty();
                        selectedItems.clear();
                        itemCounter = 0;
                        
                        // Add rows from previous stock
                        response.data.forEach(function(item) {
                            addNewRow(item);
                        });
                        
                        // Update semua index
                        updateAllIndexes();
                        
                        showNotification('Berhasil mengambil stock minggu sebelumnya. Total ' + response.data.length + ' item.', 'success');
                    } else {
                        showNotification('Tidak ada data stock sebelumnya untuk periode ini.', 'warning');
                    }
                },
                error: function(xhr) {
                    showNotification('Terjadi kesalahan saat mengambil data stock.', 'error');
                    console.error(xhr.responseText);
                },
                complete: function() {
                    // Reset button
                    $('#loadPreviousStockBtn').prop('disabled', false).html(`
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Ambil Stock Minggu Sebelumnya
                    `);
                }
            });
        });
        
        // Helper function untuk show notification
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            $('.auto-notification').remove();
            
            let bgColor = 'bg-blue-100 border-blue-400 text-blue-700';
            if (type === 'success') bgColor = 'bg-green-100 border-green-400 text-green-700';
            if (type === 'warning') bgColor = 'bg-yellow-100 border-yellow-400 text-yellow-700';
            if (type === 'error') bgColor = 'bg-red-100 border-red-400 text-red-700';
            
            const notification = $(`
                <div class="auto-notification mb-4 px-4 py-3 ${bgColor} border rounded">
                    ${message}
                </div>
            `);
            
            $('.max-w-9xl > div:first-child').prepend(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }
        
        // Form submission
        $('#submitBtn').on('click', function() {
            // Update indexes terakhir sebelum submit
            updateAllIndexes();
            
            // Validate at least one item has been selected
            if ($('#itemsBody tr').length === 0) {
                alert('Harap tambahkan minimal satu item.');
                return false;
            }
            
            // Validate all items are selected
            let allValid = true;
            let errorRows = [];
            
            $('#itemsBody tr').each(function(index) {
                const row = $(this);
                const itemId = row.find('.item-select').val();
                const stock = row.find('.stock-input').val();
                const qtyMasuk = row.find('.qty-masuk-input').val();
                
                if (!itemId || stock === '' || qtyMasuk === '') {
                    allValid = false;
                    errorRows.push(index + 1);
                    row.addClass('bg-red-50');
                } else {
                    row.removeClass('bg-red-50');
                }
            });
            
            if (!allValid) {
                alert('Harap lengkapi semua data item (pilih barang dan isi stock/qty untuk baris: ' + errorRows.join(', ') + ').');
                return false;
            }
            
            // Validate that no duplicate items
            const itemCodes = [];
            let hasDuplicates = false;
            
            $('#itemsBody tr').each(function() {
                const itemCode = $(this).find('.item-select').val();
                if (itemCode && itemCodes.includes(itemCode)) {
                    hasDuplicates = true;
                    $(this).addClass('bg-red-50');
                } else if (itemCode) {
                    itemCodes.push(itemCode);
                }
            });
            
            if (hasDuplicates) {
                alert('Terdapat item yang duplikat. Harap hapus item yang sama.');
                return false;
            }
            
            // Submit the form
            $('#reportForm').submit();
        });
        
        // Add counter element to header if not exists
        if ($('#itemCount').length === 0) {
            $('.flex.justify-between.items-center.mb-4').append(`
                <div class="text-sm text-gray-600">
                    Total Item: <span id="itemCount">0</span>
                </div>
            `);
        }
        
        // Initialize with one empty row if no toko selected
        // if ($('#toko_id').val()) {
        //     // If toko already selected (on edit page), load its data
        //     $('#toko_id').trigger('change');
        // } else {
        //     // Otherwise add one empty row
        //     addNewRow();
        // }
    });
    </script>
</x-app-layout>