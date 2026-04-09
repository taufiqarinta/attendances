<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Report Penjualan Baru') }}
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
                    <form id="reportForm" method="POST" action="{{ route('reportspg.store') }}">
                        @csrf
                        
                        <!-- Header Information -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 p-4 bg-gray-50 rounded-lg">
                            <!-- Pilih Toko -->
                            <div>
                                <x-input-label for="toko_id" :value="__('Pilih Toko')" />
                                <select id="toko_id" name="toko_id" 
                                        class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                        required>
                                    <option value="">Pilih Toko</option>
                                    @foreach($tokoList as $toko)
                                        <option value="{{ $toko->id }}" 
                                            {{ old('toko_id') == $toko->id ? 'selected' : '' }}>
                                            {{ $toko->nama_toko }} - {{ $toko->kota }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('toko_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="tanggal" :value="__('Tanggal Penjualan')" />
                                <x-text-input id="tanggal" 
                                            class="block mt-1 w-full" 
                                            type="date" 
                                            name="tanggal" 
                                            :value="old('tanggal', now()->toDateString())" 
                                            required />
                                <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label :value="__('Nama SPG')" />
                                <x-text-input class="block mt-1 w-full bg-gray-100" 
                                            type="text" 
                                            :value="auth()->user()->name" 
                                            disabled />
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Detail Penjualan</h3>
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
                                                <th class="column-qty-terjual">Qty Terjual (Box)</th>
                                                <th class="column-qty-masuk">Qty Masuk (Box)</th>
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
                            <a href="{{ route('reportspg.index') }}" 
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
            <td class="column-qty-terjual">
                <input type="number"
                    class="qty-terjual-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    min="0" value="0" required
                    onfocus="if(this.value == '0') this.value='';">
            </td>

            <td class="column-qty-masuk">
                <input type="number"
                    class="qty-masuk-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    min="0" value="0" required
                    onfocus="if(this.value == '0') this.value='';">
            </td>

            <td class="column-catatan">
                <input type="text" class="catatan-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" >
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

        /* Lebar kolom spesifik - SESUAI PERMINTAAN */
        .column-nama { 
            width: 350px; 
            min-width: 350px; 
        }
        .column-kode { 
            width: 120px; 
            min-width: 120px; 
        }
        .column-ukuran { 
            width: 100px; 
            min-width: 100px; 
        }
        .column-qty-terjual, 
        .column-qty-masuk { 
            width: 90px; 
            min-width: 90px; 
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
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            let rowIndex = 0;
            let selectedItems = new Set();
            
            // Initialize Select2 for toko dropdown
            $('#toko_id').select2({
                placeholder: 'Pilih Toko',
                allowClear: false,
                width: '100%'
            });
            
            // Fungsi untuk menambah row baru
            function addNewRow() {
                rowIndex++;
                
                const newRow = `
                    <tr class="item-row" id="row-${rowIndex}">
                        <td class="column-nama">
                            <select class="item-select w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                    name="items[${rowIndex}][item_code]" required>
                                <option value="">Pilih Barang</option>
                            </select>
                        </td>
                        <td class="column-kode">
                            <input type="text" class="itemcode-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100" 
                                   name="items[${rowIndex}][item_code_display]" readonly>
                        </td>
                        <td class="column-ukuran">
                            <input type="text" class="ukuran-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100" 
                                   name="items[${rowIndex}][ukuran]" readonly>
                        </td>
                        <td class="column-qty-terjual">
                            <input type="number" class="qty-terjual-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   name="items[${rowIndex}][qty_terjual]" min="0" value="0" required
                                   onfocus="if(this.value=='0') this.value='';"
                                    onblur="if(this.value=='') this.value='0';">
                        </td>

                        <td class="column-qty-masuk">
                            <input type="number"
                                class="qty-masuk-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                name="items[${rowIndex}][qty_masuk]"
                                min="0"
                                value="0"
                                required
                                onfocus="if(this.value=='0') this.value='';"
                                onblur="if(this.value=='') this.value='0';">
                        </td>

                        <td class="column-catatan">
                            <input type="text" class="catatan-input w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                   name="items[${rowIndex}][catatan]">
                        </td>
                        <td class="column-aksi">
                            <button type="button" class="remove-row-btn text-red-600 hover:text-red-900 text-sm" data-row="${rowIndex}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                `;
                
                $('#itemsBody').append(newRow);
                $('#noItemsMessage').hide();
                
                // Initialize Select2 for the new select
                initSelect2($(`#row-${rowIndex} .item-select`));
            }
            
            // Initialize Select2 for item dropdown
            function initSelect2(selectElement) {
                selectElement.select2({
                    placeholder: 'Cari barang...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: selectElement.closest('.table-container'),
                    ajax: {
                        url: '{{ route("api.items") }}',
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
                    
                    // Update Item Code and Ukuran fields
                    row.find('.itemcode-input').val(data.item_code);
                    row.find('.ukuran-input').val(data.ukuran || '-');
                    
                    // Add to selected items
                    selectedItems.add(data.item_code);
                }).on('select2:unselect', function() {
                    const row = $(this).closest('tr');
                    const itemCode = $(this).val();
                    
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
                console.log('Add button clicked');
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
                updateRowIndexes();
            });
            
            // Check if table is empty
            function checkEmptyTable() {
                if ($('#itemsBody tr').length === 0) {
                    $('#noItemsMessage').show();
                }
            }
            
            // Update row indexes untuk form data
            function updateRowIndexes() {
                $('#itemsBody tr').each(function(index) {
                    const row = $(this);
                    const newIndex = index + 1;
                    
                    // Update semua input names
                    row.find('select[name^="items["]').attr('name', `items[${newIndex}][item_code]`);
                    row.find('input[name^="items["][name$="item_code_display"]').attr('name', `items[${newIndex}][item_code_display]`);
                    row.find('input[name^="items["][name$="ukuran"]').attr('name', `items[${newIndex}][ukuran]`);
                    row.find('input[name^="items["][name$="qty_terjual"]').attr('name', `items[${newIndex}][qty_terjual]`);
                    row.find('input[name^="items["][name$="qty_masuk"]').attr('name', `items[${newIndex}][qty_masuk]`);
                    row.find('input[name^="items["][name$="catatan"]').attr('name', `items[${newIndex}][catatan]`);
                    
                    // Update data-row attribute untuk tombol hapus
                    row.find('.remove-row-btn').attr('data-row', newIndex);
                });
            }
            
            // Form submission
            $('#submitBtn').on('click', function() {
                // Validate toko dipilih
                const tokoId = $('#toko_id').val();
                if (!tokoId) {
                    alert('Harap pilih toko.');
                    $('#toko_id').focus();
                    return false;
                }
                
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
                    const qtyTerjual = row.find('.qty-terjual-input').val();
                    const qtyMasuk = row.find('.qty-masuk-input').val();
                    
                    if (!itemId || qtyTerjual === '' || qtyMasuk === '') {
                        allValid = false;
                        errorRows.push(index + 1);
                        row.addClass('bg-red-50');
                    } else {
                        row.removeClass('bg-red-50');
                    }
                });
                
                if (!allValid) {
                    alert('Harap lengkapi semua data item (pilih barang dan isi qty untuk baris: ' + errorRows.join(', ') + ').');
                    return false;
                }
                
                // Tambahkan field tersembunyi untuk item_code yang dipilih
                $('#itemsBody tr').each(function(index) {
                    const row = $(this);
                    const itemCode = row.find('.item-select').val();
                    
                    // Tambahkan hidden input untuk item_code
                    if (!row.find('input[name$="[item_code]"]').length) {
                        row.append(`<input type="hidden" name="items[${index + 1}][item_code]" value="${itemCode}">`);
                    }
                });
                
                // Submit the form
                $('#reportForm').submit();
            });
            
            // Initialize with one row
            $('#addItemBtn').click();
        });
    </script>
</x-app-layout>