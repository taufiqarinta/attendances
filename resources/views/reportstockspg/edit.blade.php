<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Report Stock SPG') }}
            </h2>
            <div class="text-sm text-gray-600">
                ID: {{ $reportstockspg->kode_report }}
            </div>
        </div>
    </x-slot>

    <style>
        body::after {
            content: "";
            position: fixed;
            right: 20px;
            bottom: 20px;
            width: 240px;
            height: 240px;
            background-image: url('{{ asset('corner.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.1;
            pointer-events: none;
            z-index: 5;
        }
        
        .max-w-9xl {
            position: relative;
            z-index: 10;
            background: transparent;
        }
        
        table {
            background: white;
            position: relative;
            z-index: 15;
        }
    </style>

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

            @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Report</h3>
                    
                    <!-- Header Information (Read Only) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                        <div>
                            <x-input-label for="display_kode_report" :value="__('Kode Report')" />
                            <x-text-input id="display_kode_report" 
                                        class="block mt-1 w-full bg-gray-100" 
                                        type="text" 
                                        :value="$reportstockspg->kode_report" 
                                        readonly />
                        </div>
                        
                        <div>
                            <x-input-label for="display_nama_spg" :value="__('Nama SPG')" />
                            <x-text-input id="display_nama_spg" 
                                        class="block mt-1 w-full bg-gray-100" 
                                        type="text" 
                                        :value="$reportstockspg->nama_spg" 
                                        readonly />
                        </div>
                        
                        <div>
                            <x-input-label for="display_nama_toko" :value="__('Toko')" />
                            <x-text-input id="display_nama_toko" 
                                        class="block mt-1 w-full bg-gray-100" 
                                        type="text" 
                                        :value="$reportstockspg->nama_toko" 
                                        readonly />
                        </div>
                        
                        <div>
                            <x-input-label for="display_tahun" :value="__('Tahun')" />
                            <x-text-input id="display_tahun" 
                                        class="block mt-1 w-full bg-gray-100" 
                                        type="text" 
                                        :value="$reportstockspg->tahun" 
                                        readonly />
                        </div>
                        
                        <div>
                            <x-input-label for="display_bulan" :value="__('Bulan')" />
                            <x-text-input id="display_bulan" 
                                        class="block mt-1 w-full bg-gray-100" 
                                        type="text" 
                                        :value="$reportstockspg->bulan" 
                                        readonly />
                        </div>
                        
                        <div>
                            <x-input-label for="display_minggu_ke" :value="__('Minggu Ke')" />
                            <x-text-input id="display_minggu_ke" 
                                        class="block mt-1 w-full bg-gray-100" 
                                        type="text" 
                                        :value="$reportstockspg->minggu_ke" 
                                        readonly />
                        </div>
                        
                        <div>
                            <x-input-label for="display_tanggal" :value="__('Tanggal Report')" />
                            <x-text-input id="display_tanggal" 
                                        class="block mt-1 w-full bg-gray-100" 
                                        type="text" 
                                        :value="\Carbon\Carbon::parse($reportstockspg->tanggal)->format('d-m-Y')" 
                                        readonly />
                        </div>
                        
                        <div>
                            <x-input-label for="display_dibuat" :value="__('Dibuat Oleh')" />
                            <x-text-input id="display_dibuat" 
                                        class="block mt-1 w-full bg-gray-100" 
                                        type="text" 
                                        :value="$reportstockspg->user->name" 
                                        readonly />
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form id="reportForm" method="POST" action="{{ route('reportstockspg.update', $reportstockspg) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Hidden fields untuk data header (tidak bisa diubah) -->
                        <input type="hidden" name="toko_id" value="{{ $reportstockspg->toko_id }}">
                        <input type="hidden" name="tahun" value="{{ $reportstockspg->tahun }}">
                        <input type="hidden" name="bulan" value="{{ $reportstockspg->bulan }}">
                        <input type="hidden" name="minggu_ke" value="{{ $reportstockspg->minggu_ke }}">
                        <input type="hidden" name="tanggal" value="{{ $reportstockspg->tanggal }}">

                        <!-- Items Table -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Edit Detail Stock</h3>
                                <div class="text-sm text-gray-600">
                                    Total Item: <span id="itemCount">{{ $reportstockspg->details->count() }}</span>
                                </div>
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
                                            <!-- Items from existing report -->
                                            @foreach($reportstockspg->details as $index => $detail)
                                            <tr class="item-row" id="row-{{ $index + 1 }}">
                                                <td class="column-nama">
                                                    <input type="hidden" name="items[{{ $index + 1 }}][item_code]" value="{{ $detail->item_code }}">
                                                    <select class="item-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                                            data-item-code="{{ $detail->item_code }}" 
                                                            readonly disabled style="background-color: #f9fafb; cursor: not-allowed;">
                                                        <option value="{{ $detail->item_code }}" selected>
                                                            {{ $detail->itemMaster->item_name ?? $detail->nama_barang }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td class="column-kode">
                                                    <input type="text" class="itemcode-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100" 
                                                        name="items[{{ $index + 1 }}][item_code_display]" 
                                                        value="{{ $detail->item_code }}" 
                                                        readonly>
                                                </td>
                                                <td class="column-ukuran">
                                                    <input type="text" class="ukuran-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100" 
                                                        name="items[{{ $index + 1 }}][ukuran]" 
                                                        value="{{ $detail->ukuran }}" 
                                                        readonly>
                                                </td>
                                                <td class="column-stock">
                                                    <input type="number" class="stock-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                                        name="items[{{ $index + 1 }}][stock]" 
                                                        min="0" 
                                                        value="{{ $detail->stock }}" 
                                                        required
                                                        onfocus="if(this.value=='0') this.value='';"
                                                        onblur="if(this.value=='') this.value='0';">
                                                </td>
                                                <td class="column-masuk">
                                                    <input type="number" class="qty-masuk-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                                        name="items[{{ $index + 1 }}][qty_masuk]" 
                                                        min="0" 
                                                        value="{{ $detail->qty_masuk }}" 
                                                        required
                                                        onfocus="if(this.value=='0') this.value='';"
                                                        onblur="if(this.value=='') this.value='0';">
                                                </td>
                                                <td class="column-catatan">
                                                    <input type="text" class="catatan-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                                        name="items[{{ $index + 1 }}][catatan]" 
                                                        value="{{ $detail->catatan }}" 
                                                        maxlength="500">
                                                </td>
                                                <td class="column-aksi">
                                                    @if($loop->count > 1)
                                                    <button type="button" class="remove-row-btn text-red-600 hover:text-red-900 text-sm" data-row="{{ $index + 1 }}">
                                                        Hapus
                                                    </button>
                                                    @else
                                                    <span class="text-gray-400 text-sm">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            @if($reportstockspg->details->count() === 0)
                            <div id="noItemsMessage" class="text-center py-8 text-gray-500">
                                Tidak ada item dalam report ini.
                            </div>
                            @endif
                            
                            <div class="mt-4">
                                <button type="button" id="addItemBtn" 
                                        class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    + Tambah Item Baru
                                </button>
                                <p class="text-sm text-gray-500 mt-2">
                                    <strong>Catatan:</strong> Item yang sudah ada tidak dapat diganti, hanya data stock, qty masuk, dan catatan yang dapat diubah.
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end mt-8 space-x-4">
                            <a href="{{ route('reportstockspg.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                                Kembali
                            </a>
                            <x-primary-button type="button" id="submitBtn">
                                {{ __('Update Report') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
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

        .column-nama { width: 300px; min-width: 300px; }
        .column-kode { width: 120px; min-width: 120px; }
        .column-ukuran { width: 100px; min-width: 100px; }
        .column-stock,
        .column-masuk { width: 100px; min-width: 100px; }
        .column-catatan { width: 150px; min-width: 150px; }
        .column-aksi { width: 80px; min-width: 80px; text-align: center; }

        .new-item {
            background-color: #f0f9ff;
        }
        
        .new-item td {
            border-bottom: 2px dashed #cbd5e0;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            let selectedItems = new Set();
            
            // Add existing items to selectedItems
            @foreach($reportstockspg->details as $detail)
                selectedItems.add('{{ $detail->item_code }}');
            @endforeach

            function getNextRowIndex() {
                const existingIndexes = [];
                $('#itemsBody tr').each(function() {
                    const nameAttr = $(this).find('select[name^="items["]').attr('name') || 
                                $(this).find('input[type="hidden"][name^="items["]').attr('name');
                    if (nameAttr) {
                        const match = nameAttr.match(/items\[(\d+)\]/);
                        if (match) {
                            existingIndexes.push(parseInt(match[1]));
                        }
                    }
                });
                
                if (existingIndexes.length === 0) return 1;
                
                const maxIndex = Math.max(...existingIndexes);
                
                // Cari gap di antara index yang ada
                for (let i = 1; i <= maxIndex; i++) {
                    if (!existingIndexes.includes(i)) {
                        return i;
                    }
                }
                
                return maxIndex + 1;
            }
            
            // Fungsi untuk menambah row baru
            function addNewRow() {
                const newRowIndex = getNextRowIndex();
                
                const newRow = `
                    <tr class="item-row new-item" id="row-${newRowIndex}">
                        <td class="column-nama">
                            <select class="item-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    name="items[${newRowIndex}][item_code]" required>
                                <option value="">Pilih Barang Baru</option>
                            </select>
                        </td>
                        <td class="column-kode">
                            <input type="text" class="itemcode-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100" 
                                name="items[${newRowIndex}][item_code_display]" 
                                readonly>
                        </td>
                        <td class="column-ukuran">
                            <input type="text" class="ukuran-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100" 
                                name="items[${newRowIndex}][ukuran]" 
                                readonly>
                        </td>
                        <td class="column-stock">
                            <input type="number"
                                class="stock-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                name="items[${newRowIndex}][stock]"
                                min="0"
                                value="0"
                                required
                                onfocus="if(this.value=='0') this.value='';"
                                onblur="if(this.value=='') this.value='0';">
                        </td>
                        <td class="column-masuk">
                            <input type="number"
                                class="qty-masuk-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                name="items[${newRowIndex}][qty_masuk]"
                                min="0"
                                value="0"
                                required
                                onfocus="if(this.value=='0') this.value='';"
                                onblur="if(this.value=='') this.value='0';">
                        </td>
                        <td class="column-catatan">
                            <input type="text" class="catatan-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                name="items[${newRowIndex}][catatan]"
                                maxlength="500">
                        </td>
                        <td class="column-aksi">
                            <button type="button" class="remove-row-btn text-red-600 hover:text-red-900 text-sm" data-row="${newRowIndex}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                `;
                
                $('#itemsBody').append(newRow);
                $('#noItemsMessage').hide();
                
                // Initialize Select2 for the new select
                initSelect2($(`#row-${newRowIndex} .item-select`));
                updateItemCount();
            }
            
            // Initialize Select2 untuk search item
            function initSelect2(selectElement) {
                selectElement.select2({
                    placeholder: 'Cari barang...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: selectElement.closest('.table-container'),
                    ajax: {
                        url: '{{ route("api.stock-items") }}', // PASTIKAN route ini sama dengan create
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page,
                                exclude: Array.from(selectedItems)
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
                    
                    // Update fields
                    row.find('.itemcode-input').val(data.item_code);
                    row.find('.ukuran-input').val(data.ukuran || '-');
                    
                    // Add to selected items
                    selectedItems.add(data.item_code);
                    
                    // Focus on stock input
                    row.find('.stock-input').focus();
                    
                }).on('select2:unselect', function() {
                    const row = $(this).closest('tr');
                    const itemCode = $(this).val();
                    
                    // Clear fields
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
            
            // Initialize existing items (yang sudah ada di report)
            // HAPUS deklarasi existingRow yang duplikat di loop ini
            @foreach($reportstockspg->details as $index => $detail)
                // Gunakan nama variabel yang berbeda atau langsung inisialisasi tanpa deklarasi berulang
                const row{{ $index + 1 }} = $('#row-{{ $index + 1 }}');
                const itemCode{{ $index + 1 }} = '{{ $detail->item_code }}';
                const itemName{{ $index + 1 }} = '{{ $detail->nama_barang }}';
                const ukuran{{ $index + 1 }} = '{{ $detail->ukuran }}';
                
                // Initialize readonly select untuk item existing
                row{{ $index + 1 }}.find('.item-select').select2({
                    data: [{
                        id: itemCode{{ $index + 1 }},
                        text: `${itemCode{{ $index + 1 }}} - ${itemName{{ $index + 1 }}} - ${ukuran{{ $index + 1 }}}`
                    }],
                    placeholder: itemName{{ $index + 1 }},
                    width: '100%',
                    dropdownParent: row{{ $index + 1 }}.closest('.table-container'),
                    disabled: true
                }).val(itemCode{{ $index + 1 }}).trigger('change');
            @endforeach
            
            // Tombol tambah item baru - PERBAIKAN DI SINI
            $('#addItemBtn').on('click', function() {
                console.log('Tombol diklik!');
                addNewRow();
            });
            
            // Event delegation untuk tombol hapus
            $(document).on('click', '.remove-row-btn', function() {
                const rowId = $(this).data('row');
                const row = $(`#row-${rowId}`);
                const isNewItem = row.hasClass('new-item');
                const itemCode = row.find('.item-select').val() || row.find('.itemcode-input').val();
                
                // Validasi minimal satu item tersisa
                if (!isNewItem) {
                    const originalRows = $('#itemsBody tr:not(.new-item)').length;
                    if (originalRows <= 1) {
                        alert('Report harus memiliki minimal satu item original.');
                        return;
                    }
                }
                
                // Hapus dari selected items jika ada
                if (itemCode && selectedItems.has(itemCode)) {
                    selectedItems.delete(itemCode);
                }
                
                // Hapus row
                row.remove();
                
                updateItemCount();
            });
            
            // Update item count
            function updateItemCount() {
                const count = $('#itemsBody tr').length;
                $('#itemCount').text(count);
            }
            
            // Form submission
            $('#submitBtn').on('click', function() {
                // Validasi minimal satu item
                if ($('#itemsBody tr').length === 0) {
                    alert('Report harus memiliki minimal satu item.');
                    return false;
                }
                
                // Validasi semua item
                let allValid = true;
                let errorRows = [];
                
                $('#itemsBody tr').each(function(index) {
                    const row = $(this);
                    const rowNumber = index + 1;
                    const isNewItem = row.hasClass('new-item');
                    const itemCode = row.find('select[name*="[item_code]"]').val() || 
                                row.find('input[type="hidden"][name*="[item_code]"]').val();
                    const stock = row.find('.stock-input').val();
                    const qtyMasuk = row.find('.qty-masuk-input').val();
                    
                    // Untuk item baru, validasi select
                    if (isNewItem && !itemCode) {
                        allValid = false;
                        errorRows.push(rowNumber);
                        row.addClass('bg-red-50');
                    }
                    
                    // Validasi stock dan qty_masuk untuk semua item
                    if (stock === '' || qtyMasuk === '') {
                        allValid = false;
                        if (!errorRows.includes(rowNumber)) {
                            errorRows.push(rowNumber);
                        }
                        row.addClass('bg-red-50');
                    } else {
                        row.removeClass('bg-red-50');
                    }
                });
                
                if (!allValid) {
                    alert('Harap lengkapi semua data item (pilih barang dan isi stock/qty untuk baris: ' + errorRows.join(', ') + ').');
                    return false;
                }
                
                // Konfirmasi sebelum update
                if (confirm('Apakah Anda yakin ingin mengupdate report ini?')) {
                    $('#reportForm').submit();
                }
            });
            
            updateItemCount();
        });
    </script>
</x-app-layout>