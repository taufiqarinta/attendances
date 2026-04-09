<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Report Penjualan SPG') }}
            </h2>
            <div class="flex space-x-2">
                <!-- Tombol Export untuk admin -->
                <a href="{{ route('reportspg.export-excel') }}?{{ http_build_query($filters) }}" 
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('reportspg.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    + Report Baru
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
            
            @if(session('info'))
                <div class="mb-4 px-4 py-3 bg-blue-100 border border-blue-400 text-blue-700 rounded">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Filter Section -->
            <div class="mb-6">
                <form method="GET" action="{{ route('reportspg.index') }}" class="space-y-0">
                    
                    <div class="flex flex-wrap items-end gap-3">
                        <!-- Search -->
                        <div class="w-full md:flex-1 min-w-[200px]">
                            <input type="text" 
                                name="search" 
                                value="{{ $filters['search'] ?? '' }}" 
                                placeholder="Cari kode report, nama SPG, atau nama toko..." 
                                class="w-full pl-3 pr-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Tanggal Mulai -->
                        <div class="min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Awal</label>
                            <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Tanggal Akhir -->
                        <div class="min-w-[150px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                            <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Btn Cari -->
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 whitespace-nowrap h-[40px]">
                            Cari
                        </button>

                        <!-- Btn Reset -->
                        @if(!empty(array_filter($filters)))
                            <a href="{{ route('reportspg.index') }}" 
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 whitespace-nowrap h-[40px]">
                                Reset
                            </a>
                        @endif
                    </div>

                </form>
            </div>


            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($reports->count() > 0)
                        <!-- Info pencarian -->
                        @if(!empty($filters['search']) || !empty($filters['start_date']) || !empty($filters['end_date']) || !empty($filters['nama_spg']))
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-blue-800">
                                        @if(!empty($filters['search']))
                                            <span class="font-medium">Pencarian:</span> "{{ $filters['search'] }}"
                                        @endif
                                        @if(!empty($filters['start_date']) && !empty($filters['end_date']))
                                            | <span class="font-medium">Periode:</span> 
                                            {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }} - 
                                            {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}
                                        @elseif(!empty($filters['start_date']))
                                            | <span class="font-medium">Dari:</span> 
                                            {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }}
                                        @elseif(!empty($filters['end_date']))
                                            | <span class="font-medium">Sampai:</span> 
                                            {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}
                                        @endif
                                        @if(!empty($filters['nama_spg']))
                                            | <span class="font-medium">SPG:</span> "{{ $filters['nama_spg'] }}"
                                        @endif
                                    </div>
                                    <div class="text-sm font-medium">
                                        {{ $reports->total() }} data ditemukan
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Container untuk tabel dengan scroll horizontal -->
                        <div class="table-container">
                            <div class="overflow-x-scroll">
                                <table class="responsive-table">
                                    <thead>
                                        <tr>
                                            <th class="column-kode">Kode Report</th>
                                            <th class="column-tanggal">Tanggal Penjualan</th>
                                            <th class="column-spg">Nama SPG</th>
                                            <th class="column-toko">Nama Toko</th>
                                            <th class="column-item">Jumlah Item</th>
                                            <!-- <th class="column-terjual">Total Terjual</th> -->
                                            <!-- <th class="column-masuk">Total Masuk</th> -->
                                            <th class="column-aksi">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $report)
                                        <tr>
                                            <td class="column-kode font-semibold">
                                                <div class="font-mono">{{ $report->kode_report }}</div>
                                            </td>
                                            <td class="column-tanggal">
                                                {{ $report->tanggal->format('d/m/Y') }}
                                            </td>
                                            <td class="column-spg">
                                                {{ $report->nama_spg }}
                                            </td>
                                            <td class="column-toko">
                                                @if($report->toko)
                                                    <div class="font-medium">{{ $report->toko->nama_toko }}</div>
                                                    <!-- <div class="text-xs text-gray-500">{{ $report->toko->kota }}</div> -->
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="column-item text-center">
                                                <span class="inline-flex items-center justify-center px-2 py-1 bg-green-100 text-green-800 rounded-md text-sm font-medium">
                                                    {{ $report->details->count() }} Item
                                                </span>
                                            </td>
                                            <!-- <td class="column-terjual text-center">
                                                <span class="inline-flex items-center justify-center px-2 py-1 bg-green-100 text-green-800 rounded-md text-sm font-medium">
                                                    {{ $report->details->sum('qty_terjual') }} Box
                                                </span>
                                            </td> -->
                                            <!-- <td class="column-masuk text-center">
                                                <span class="inline-flex items-center justify-center px-2 py-1 bg-green-100 text-green-800 rounded-md text-sm font-medium">
                                                    {{ $report->details->sum('qty_masuk') }} Box
                                                </span>
                                            </td> -->
                                            <td class="column-aksi">
                                                <div class="action-buttons">
                                                    <a href="{{ route('reportspg.show', $report) }}" 
                                                       class="btn btn-blue" title="Lihat Detail">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Detail
                                                    </a>
                                                    <a href="{{ route('reportspg.edit', $report) }}" 
                                                       class="btn btn-yellow" title="Edit Report">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('reportspg.destroy', $report) }}" 
                                                          method="POST" 
                                                          class="inline-form" 
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus report ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-red" title="Hapus Report">
                                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-6">
                            {{ $reports->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                @if(!empty(array_filter($filters)))
                                    Tidak ditemukan report dengan filter yang diberikan
                                @else
                                    Belum ada report penjualan
                                @endif
                            </h3>
                            <p class="text-gray-500 mb-6">
                                @if(!empty(array_filter($filters)))
                                    Coba ubah kriteria pencarian Anda
                                @else
                                    Mulai dengan membuat report penjualan pertama Anda
                                @endif
                            </p>
                            <div class="flex justify-center gap-3">
                                @if(!empty(array_filter($filters)))
                                    <a href="{{ route('reportspg.index') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                        Reset Filter
                                    </a>
                                @endif
                                <a href="{{ route('reportspg.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    Buat Report Baru
                                </a>
                            </div>
                        </div>
                    @endif
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
            min-width: 1000px; /* <-- Tambah min-width karena ada kolom baru */
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
        .column-kode { 
            width: 150px; 
            min-width: 150px; 
        }
        .column-tanggal { 
            width: 100px; 
            min-width: 100px; 
        }
        .column-spg { 
            width: 150px; 
            min-width: 150px; 
        }
        .column-toko { 
            width: 200px; 
            min-width: 200px; /* <-- Kolom baru untuk toko */
        }
        .column-item { 
            width: 100px; 
            min-width: 100px; 
            text-align: center;
        }
        .column-terjual,
        .column-masuk { 
            width: 120px; 
            min-width: 120px; 
            text-align: center;
        }
        .column-aksi { 
            width: 80px; /* <-- Tambah sedikit lebar karena ada icon */
            min-width: 80px; 
        }

        /* Styling untuk tombol aksi */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.4rem 0.5rem;
            font-size: 0.75rem;
            text-align: center;
            border-radius: 0.25rem;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
            width: 100%;
        }

        .inline-form {
            display: block;
            margin: 0;
        }

        .btn-blue {
            background-color: #2563eb;
            color: white;
        }

        .btn-blue:hover {
            background-color: #1d4ed8;
        }

        .btn-yellow {
            background-color: #f59e0b;
            color: white;
        }

        .btn-yellow:hover {
            background-color: #d97706;
        }

        .btn-red {
            background-color: #dc2626;
            color: white;
        }

        .btn-red:hover {
            background-color: #b91c1c;
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
            
            .grid-cols-1 {
                grid-template-columns: 1fr;
            }
            
            .md\:grid-cols-4 {
                grid-template-columns: repeat(1, 1fr);
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .md\:grid-cols-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</x-app-layout>