<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Report Penjualan') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reportspg.edit', $reportspg) }}" 
                   class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">
                    Edit
                </a>
                <a href="{{ route('reportspg.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Header Information -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 p-6 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Kode Report</h4>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $reportspg->kode_report }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Tanggal Penjualan</h4>
                            <p class="mt-1 text-lg text-gray-900">{{ $reportspg->tanggal->format('d F Y') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Nama SPG</h4>
                            <p class="mt-1 text-lg text-gray-900">{{ $reportspg->nama_spg }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Nama Toko</h4>
                            <p class="mt-1 text-lg text-gray-900">{{ $reportspg->toko->nama_toko }}</p>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Penjualan</h3>
                        
                        <!-- Container untuk tabel dengan scroll horizontal -->
                        <div class="table-container">
                            <div class="overflow-x-scroll">
                                <table class="responsive-table">
                                    <thead>
                                        <tr>
                                            <th class="column-no">No</th>
                                            <th class="column-nama">Nama Barang</th>
                                            <th class="column-kode">Kode Item</th>
                                            <th class="column-ukuran">Ukuran</th>
                                            <th class="column-qty-terjual">Qty Terjual (Box)</th>
                                            <th class="column-qty-masuk">Qty Masuk (Box)</th>
                                            <th class="column-catatan">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($reportspg->details as $index => $detail)
                                        <tr>
                                            <td class="column-no text-center">{{ $loop->iteration }}</td>
                                            <td class="column-nama">{{ $detail->nama_barang }}</td>
                                            <td class="column-kode">{{ $detail->item_code }}</td>
                                            <td class="column-ukuran">{{ $detail->ukuran ?? '-' }}</td>
                                            <td class="column-qty-terjual text-center">{{ $detail->qty_terjual }}</td>
                                            <td class="column-qty-masuk text-center">{{ $detail->qty_masuk }}</td>
                                            <td class="column-catatan">{{ $detail->catatan ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-gray-500 py-4">
                                                Tidak ada data penjualan
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="4" class="text-right font-semibold">Total:</td>
                                            <td class="column-qty-terjual text-center font-semibold">
                                                {{ $reportspg->details->sum('qty_terjual') }}
                                            </td>
                                            <td class="column-qty-masuk text-center font-semibold">
                                                {{ $reportspg->details->sum('qty_masuk') }}
                                            </td>
                                            <td class="column-catatan"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($reportspg->buktiTransaksis && $reportspg->buktiTransaksis->count() > 0)
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Bukti Transaksi</h3>
                            <span class="text-sm text-gray-500">{{ $reportspg->buktiTransaksis->count() }} foto</span>
                        </div>
                        
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                                @foreach($reportspg->buktiTransaksis as $bukti)
                                <div class="group relative">
                                    <!-- Preview Image -->
                                    <div class="aspect-square overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow duration-200">
                                        <a href="{{ Storage::url('bukti_transaksi/' . $bukti->nama_file) }}" 
                                        target="_blank" 
                                        class="block h-full w-full hover:opacity-90 transition-opacity duration-200">
                                            <img src="{{ Storage::url('bukti_transaksi/' . $bukti->nama_file) }}" 
                                                alt="Bukti Transaksi {{ $loop->iteration }}"
                                                class="h-full w-full object-cover"
                                                onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2YzZjRmNiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIiBmaWxsPSIjOWNhM2FmIj5JbWFnZSBub3QgZm91bmQ8L3RleHQ+PC9zdmc+'">
                                        </a>
                                    </div>
                                    
                                    <!-- File Info -->
                                    <div class="mt-2 px-1">
                                        <div class="text-xs text-gray-500 truncate">
                                            {{ $bukti->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons on Hover -->
                                    <!-- <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex space-x-1">
                                        <a href="{{ Storage::url('bukti_transaksi/' . $bukti->nama_file) }}" 
                                        target="_blank"
                                        class="bg-blue-600 text-white p-1 rounded hover:bg-blue-700 transition-colors duration-200"
                                        title="Lihat Full Size">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ Storage::url('bukti_transaksi/' . $bukti->nama_file) }}" 
                                        download="{{ $bukti->nama_file }}"
                                        class="bg-green-600 text-white p-1 rounded hover:bg-green-700 transition-colors duration-200"
                                        title="Download">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                    </div> -->
                                </div>
                                @endforeach
                            </div>
                            
                            <!-- Empty State (shouldn't show but just in case) -->
                            @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h4 class="mt-2 text-sm font-medium text-gray-900">Tidak ada bukti transaksi</h4>
                                <p class="mt-1 text-sm text-gray-500">Foto bukti transaksi belum diupload.</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Information -->
                    <!-- <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <div>
                                <p>Dibuat pada: {{ $reportspg->created_at->format('d/m/Y H:i') }}</p>
                                <p class="mt-1">Diperbarui pada: {{ $reportspg->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p>Jumlah Item: {{ $reportspg->details->count() }}</p>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>

    <style>
        .aspect-square {
            aspect-ratio: 1 / 1;
        }

        .group:hover .group-hover\:opacity-100 {
            opacity: 1;
        }

        .transition-shadow {
            transition: box-shadow 0.2s ease-in-out;
        }

        .transition-opacity {
            transition: opacity 0.2s ease-in-out;
        }

        .transition-colors {
            transition: background-color 0.2s ease-in-out;
        }

        /* Custom scrollbar for modal if needed */
        .modal-scroll {
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .modal-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .modal-scroll::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .modal-scroll::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Lightbox/Modal Styles */
        .lightbox-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.9);
        }

        .lightbox-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 80vh;
        }

        .lightbox-caption {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            text-align: center;
            color: #ccc;
            padding: 10px 0;
            height: 150px;
        }

        .close-lightbox {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }

        .close-lightbox:hover {
            color: #bbb;
            text-decoration: none;
        }

        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -50px;
            color: white;
            font-weight: bold;
            font-size: 20px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }

        .prev {
            left: 0;
            border-radius: 3px 0 0 3px;
        }

        .prev:hover, .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
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

        /* Lebar kolom spesifik - SAMA DENGAN CREATE/EDIT */
        .column-no { 
            width: 50px; 
            min-width: 50px; 
        }
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

        /* Alignment untuk kolom khusus */
        .column-no,
        .column-qty-terjual,
        .column-qty-masuk {
            text-align: center;
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
</x-app-layout>