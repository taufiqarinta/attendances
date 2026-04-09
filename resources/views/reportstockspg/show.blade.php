<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Report Stock') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reportstockspg.edit', $reportstockspg) }}" 
                   class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">
                    Edit
                </a>
                <a href="{{ route('reportstockspg.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                    Kembali
                </a>
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
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $reportstockspg->kode_report }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Periode</h4>
                            <p class="mt-1 text-lg text-gray-900">
                                Minggu {{ $reportstockspg->minggu_ke }} - 
                                {{ \Carbon\Carbon::create()->month($reportstockspg->bulan)->format('F') }} 
                                {{ $reportstockspg->tahun }}
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Toko</h4>
                            <p class="mt-1 text-lg text-gray-900">{{ $reportstockspg->nama_toko }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Tanggal Report</h4>
                            <p class="mt-1 text-lg text-gray-900">{{ $reportstockspg->tanggal->format('d F Y') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Nama SPG</h4>
                            <p class="mt-1 text-lg text-gray-900">{{ $reportstockspg->nama_spg }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Jumlah Item</h4>
                            <p class="mt-1 text-lg text-gray-900">{{ $reportstockspg->details->count() }} item</p>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Stock</h3>
                        
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
                                            <th class="column-stock">Stock</th>
                                            <th class="column-masuk">Qty Masuk (Box)</th>
                                            <th class="column-catatan">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($reportstockspg->details as $index => $detail)
                                        <tr>
                                            <td class="column-no text-center">{{ $loop->iteration }}</td>
                                            <td class="column-nama">{{ $detail->nama_barang }}</td>
                                            <td class="column-kode">{{ $detail->item_code }}</td>
                                            <td class="column-ukuran">{{ $detail->ukuran ?? '-' }}</td>
                                            <td class="column-stock text-center">{{ $detail->stock }}</td>
                                            <td class="column-masuk text-center">{{ $detail->qty_masuk }}</td>
                                            <td class="column-catatan">{{ $detail->catatan ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-gray-500 py-4">
                                                Tidak ada data stock
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="4" class="text-right font-semibold">Total:</td>
                                            <td class="column-stock text-center font-semibold">
                                                {{ $reportstockspg->details->sum('stock') }}
                                            </td>
                                            <td class="column-masuk text-center font-semibold">
                                                {{ $reportstockspg->details->sum('qty_masuk') }}
                                            </td>
                                            <td class="column-catatan"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Information -->
                    <!-- <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <div>
                                <p>Dibuat pada: {{ $reportstockspg->created_at->format('d/m/Y H:i') }}</p>
                                <p class="mt-1">Diperbarui pada: {{ $reportstockspg->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>

    <style>
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
        .column-no { 
            width: 50px; 
            min-width: 50px; 
        }
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