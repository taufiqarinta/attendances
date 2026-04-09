<?php

namespace App\Http\Controllers;

use App\Models\FormReportSPG;
use App\Models\FormReportSPGDetail;
use App\Models\BuktiTransaksi;
use App\Models\ItemMaster;
use App\Models\Merk;
use App\Models\UsersMerks;
use App\Models\ItemMasterTambahan;
use App\Models\DaftarToko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportSPGExport;

class ReportSPGController extends Controller
{
    /**
     * Helper function untuk check authorization berdasarkan role
     */
    private function checkAuthorization(FormReportSPG $reportspg)
    {
        $user = Auth::user();
        // dd($user->id);
        
        // Jika user adalah admin (role_as = 1), izinkan akses
        if ($user->role_as == 1) {
            return true;
        }
        
        // Jika user biasa (role_as = 0), cek apakah data miliknya
        if ($reportspg->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        return true;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Jika role_as = 1 (admin), tampilkan semua data
        // Jika role_as = 0 (user biasa), tampilkan hanya data user tersebut
        $query = FormReportSPG::with(['details', 'toko']);
            
        if ($user->role_as == 0) {
            $query->where('user_id', $user->id);
        }
        
        // Filter berdasarkan tanggal jika ada
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        
        // Filter berdasarkan nama SPG jika role admin
        if ($user->role_as == 1 && $request->filled('nama_spg')) {
            $query->where('nama_spg', 'like', '%' . $request->nama_spg . '%');
        }
        
        // Filter berdasarkan kode report
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_report', 'like', '%' . $search . '%')
                ->orWhere('nama_spg', 'like', '%' . $search . '%')
                ->orWhereHas('toko', function($q) use ($search) {
                    $q->where('nama_toko', 'like', '%' . $search . '%')
                        ->orWhere('kota', 'like', '%' . $search . '%');
                });
            });
        }
        
        $reports = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Simpan filter untuk view
        $filters = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'nama_spg' => $request->nama_spg,
            'search' => $request->search, // <-- Tambahkan search ke filters
        ];
        
        return view('reportspg.index', compact('reports', 'filters'));
    }

    /**
     * Export data to Excel
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        
        // Validasi tanggal
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'nama_spg' => 'nullable|string|max:255',
        ]);
        
        $filters = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'nama_spg' => $request->nama_spg,
            'user_role' => $user->role_as,
            'user_id' => $user->id,
        ];
        
        $filename = 'report_spg_' . date('Ymd_His') . '.xlsx';
        
        return Excel::download(new ReportSPGExport($filters), $filename);
    }


    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     // Cek apakah sudah ada report untuk hari ini
    //     // Hanya berlaku untuk user biasa, admin bisa buat berapapun
    //     $user = Auth::user();

    //     $tokoList = DaftarToko::where('kode_spg', $user->id_customer)
    //         ->orderBy('nama_toko')
    //         ->get(['id', 'nama_toko', 'kota']);

        
    //     // if ($user->role_as == 0) {
    //     //     $todayReport = FormReportSPG::where('user_id', $user->id)
    //     //         ->whereDate('tanggal', now()->toDateString())
    //     //         ->first();
                
    //     //     if ($todayReport) {
    //     //         return redirect()->route('reportspg.show', $todayReport)
    //     //             ->with('info', 'Anda sudah membuat report untuk hari ini. Anda dapat mengedit report tersebut.');
    //     //     }
    //     // }
        
    //     $items = ItemMaster::orderBy('item_name')->get(['item_code', 'item_name', 'ukuran']);
    //     return view('reportspg.create', compact('items', 'tokoList'));
    // }

    public function create()
    {
        $user = Auth::user();

        $tokoList = DaftarToko::where('kode_spg', $user->id_customer)
            ->where('status', 1)
            ->orderBy('nama_toko')
            ->get(['id', 'nama_toko', 'kota']);

        // Ambil data dari ItemMaster
        $itemsMaster = ItemMaster::orderBy('item_name')->get(['item_code', 'item_name', 'ukuran']);
        
        // Ambil data dari ItemMasterTambahan
        $itemsTambahan = ItemMasterTambahan::orderBy('item_name')->get(['item_code', 'item_name', 'ukuran']);
        
        // Gabungkan kedua koleksi
        $items = $itemsMaster->merge($itemsTambahan);
        
        // Urutkan berdasarkan nama barang
        $items = $items->sortBy('item_name');

        return view('reportspg.create', compact('items', 'tokoList'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'toko_id' => 'required',
            'no_sales' => 'nullable|boolean',
            'total_customer' => 'nullable|integer|min:0',
            'customer_transaksi' => 'nullable|integer|min:0',
            'customer_lost_sale' => 'nullable|integer|min:0',
            'analisa_lost_sale' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.item_code' => 'required_without:no_sales',
            'items.*.qty_terjual' => 'required_without:no_sales|integer|min:0',
            'items.*.qty_masuk' => 'required_without:no_sales|integer|min:0',
            'items.*.catatan' => 'nullable|string',
        ]);

        try {
            \DB::beginTransaction();

            // Buat header report
            $report = FormReportSPG::create([
                'kode_report' => FormReportSPG::generateKodeReport(),
                'tanggal' => $request->tanggal,
                'user_id' => Auth::id(),
                'nama_spg' => Auth::user()->name,
                'toko_id' => $request->toko_id,
                'no_sales' => $request->has('no_sales') ? true : false,
                'total_customer' => $request->total_customer,
                'customer_transaksi' => $request->customer_transaksi,
                'customer_lost_sale' => $request->customer_lost_sale,
                'analisa_lost_sale' => $request->analisa_lost_sale,
            ]);

            // Hanya simpan detail items jika tidak ada no sales
            if (!$request->has('no_sales') && !empty($request->items)) {
                foreach ($request->items as $itemData) {
                    $itemMaster = ItemMaster::where('item_code', $itemData['item_code'])->first();
                    
                    if (!$itemMaster) {
                        $itemMaster = ItemMasterTambahan::where('item_code', $itemData['item_code'])->first();
                    }
                    
                    if ($itemMaster) {
                        FormReportSPGDetail::create([
                            'report_id' => $report->id,
                            'item_code' => $itemData['item_code'],
                            'nama_barang' => $itemMaster->item_name,
                            'ukuran' => $itemMaster->ukuran,
                            'qty_terjual' => $itemData['qty_terjual'],
                            'qty_masuk' => $itemData['qty_masuk'],
                            'catatan' => $itemData['catatan'] ?? null,
                        ]);
                    }
                }
            }

            \DB::commit();

            return redirect()->route('reportspg.show', $report)
                ->with('success', 'Report penjualan berhasil disimpan.');
                
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, FormReportSPG $reportspg)
    {
        // Gunakan helper function untuk check authorization
        $this->checkAuthorization($reportspg);

        // Validasi untuk item_code dari kedua tabel
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'no_sales' => 'nullable|boolean',
            'total_customer' => 'nullable|integer|min:0',
            'customer_transaksi' => 'nullable|integer|min:0',
            'customer_lost_sale' => 'nullable|integer|min:0',
            'analisa_lost_sale' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.item_code' => [
                'required_without:no_sales',
                function ($attribute, $value, $fail) {
                    $existsInMaster = ItemMaster::where('item_code', $value)->exists();
                    $existsInTambahan = ItemMasterTambahan::where('item_code', $value)->exists();
                    
                    if (!$existsInMaster && !$existsInTambahan) {
                        $fail('Item code tidak valid.');
                    }
                },
            ],
            'items.*.qty_terjual' => 'required_without:no_sales|integer|min:0',
            'items.*.qty_masuk' => 'required_without:no_sales|integer|min:0',
            'items.*.catatan' => 'nullable|string',
        ]);

        try {
            // Mulai transaksi
            \DB::beginTransaction();

            // Update header report
            $updateData = [
                'tanggal' => $request->tanggal,
                'no_sales' => $request->has('no_sales') ? true : false,
                'total_customer' => $request->total_customer,
                'customer_transaksi' => $request->customer_transaksi,
                'customer_lost_sale' => $request->customer_lost_sale,
                'analisa_lost_sale' => $request->analisa_lost_sale,
            ];
            
            $reportspg->update($updateData);

            // Hapus detail lama
            $reportspg->details()->delete();

            // Simpan detail items baru hanya jika bukan no sales
            if (!$request->has('no_sales') && !empty($request->items)) {
                foreach ($request->items as $itemData) {
                    $itemMaster = ItemMaster::where('item_code', $itemData['item_code'])->first();
                    
                    if (!$itemMaster) {
                        $itemMaster = ItemMasterTambahan::where('item_code', $itemData['item_code'])->first();
                    }
                    
                    if ($itemMaster) {
                        FormReportSPGDetail::create([
                            'report_id' => $reportspg->id,
                            'item_code' => $itemData['item_code'],
                            'nama_barang' => $itemMaster->item_name,
                            'ukuran' => $itemMaster->ukuran,
                            'qty_terjual' => $itemData['qty_terjual'],
                            'qty_masuk' => $itemData['qty_masuk'],
                            'catatan' => $itemData['catatan'] ?? null,
                        ]);
                    }
                }
            }

            \DB::commit();

            return redirect()->route('reportspg.show', $reportspg)
                ->with('success', 'Report penjualan berhasil diperbarui.');
                
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Fix Sebelum ada inputan lost dkk
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'tanggal' => 'required|date',
    //         'toko_id' => 'required',
    //         'no_sales' => 'nullable|boolean',
    //         'items' => 'nullable|array',
    //         'items.*.item_code' => 'required_without:no_sales',
    //         'items.*.qty_terjual' => 'required_without:no_sales|integer|min:0',
    //         'items.*.qty_masuk' => 'required_without:no_sales|integer|min:0',
    //         'items.*.catatan' => 'nullable|string',
    //     ]);

    //     try {
    //         \DB::beginTransaction();

    //         // Buat header report
    //         $report = FormReportSPG::create([
    //             'kode_report' => FormReportSPG::generateKodeReport(),
    //             'tanggal' => $request->tanggal,
    //             'user_id' => Auth::id(),
    //             'nama_spg' => Auth::user()->name,
    //             'toko_id' => $request->toko_id,
    //             'no_sales' => $request->has('no_sales') ? true : false, // Simpan status no sales
    //         ]);

    //         // Hanya simpan detail items jika tidak ada no sales
    //         if (!$request->has('no_sales') && !empty($request->items)) {
    //             foreach ($request->items as $itemData) {
    //                 // Cari item dari ItemMaster terlebih dahulu
    //                 $itemMaster = ItemMaster::where('item_code', $itemData['item_code'])->first();
                    
    //                 // Jika tidak ditemukan di ItemMaster, cari di ItemMasterTambahan
    //                 if (!$itemMaster) {
    //                     $itemMaster = ItemMasterTambahan::where('item_code', $itemData['item_code'])->first();
    //                 }
                    
    //                 if ($itemMaster) {
    //                     FormReportSPGDetail::create([
    //                         'report_id' => $report->id,
    //                         'item_code' => $itemData['item_code'],
    //                         'nama_barang' => $itemMaster->item_name,
    //                         'ukuran' => $itemMaster->ukuran,
    //                         'qty_terjual' => $itemData['qty_terjual'],
    //                         'qty_masuk' => $itemData['qty_masuk'],
    //                         'catatan' => $itemData['catatan'] ?? null,
    //                     ]);
    //                 }
    //             }
    //         }

    //         \DB::commit();

    //         return redirect()->route('reportspg.show', $report)
    //             ->with('success', 'Report penjualan berhasil disimpan.');
                
    //     } catch (\Exception $e) {
    //         \DB::rollBack();
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
    //             ->withInput();
    //     }
    // }

    // fix sebelum ada upload foto
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'tanggal' => 'required|date',
    //         'toko_id' => 'required',
    //         'items' => 'required|array|min:1',
    //         'items.*.item_code' => 'required',
    //         'items.*.qty_terjual' => 'required|integer|min:0',
    //         'items.*.qty_masuk' => 'required|integer|min:0',
    //         'items.*.catatan' => 'nullable|string',
    //     ]);

    //     try {
    //         \DB::beginTransaction();

    //         // Buat header report
    //         $report = FormReportSPG::create([
    //             'kode_report' => FormReportSPG::generateKodeReport(),
    //             'tanggal' => $request->tanggal,
    //             'user_id' => Auth::id(),
    //             'nama_spg' => Auth::user()->name,
    //             'toko_id' => $request->toko_id,
    //         ]);

    //         // Simpan detail items
    //         foreach ($request->items as $itemData) {
    //             // Cari item dari ItemMaster terlebih dahulu
    //             $itemMaster = ItemMaster::where('item_code', $itemData['item_code'])->first();
                
    //             // Jika tidak ditemukan di ItemMaster, cari di ItemMasterTambahan
    //             if (!$itemMaster) {
    //                 $itemMaster = ItemMasterTambahan::where('item_code', $itemData['item_code'])->first();
    //             }
                
    //             if ($itemMaster) {
    //                 FormReportSPGDetail::create([
    //                     'report_id' => $report->id,
    //                     'item_code' => $itemData['item_code'],
    //                     'nama_barang' => $itemMaster->item_name,
    //                     'ukuran' => $itemMaster->ukuran,
    //                     'qty_terjual' => $itemData['qty_terjual'],
    //                     'qty_masuk' => $itemData['qty_masuk'],
    //                     'catatan' => $itemData['catatan'] ?? null,
    //                 ]);
    //             }
    //         }

    //         \DB::commit();

    //         return redirect()->route('reportspg.show', $report)
    //             ->with('success', 'Report penjualan berhasil disimpan.');
                
    //     } catch (\Exception $e) {
    //         \DB::rollBack();
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
    //             ->withInput();
    //     }
    // }

    // untuk yang ada fotonya tapi dinonaktifkan dulu
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'tanggal' => 'required|date',
    //         'toko_id' => 'required',
    //         'items' => 'required|array|min:1',
    //         'items.*.item_code' => 'required',
    //         'items.*.qty_terjual' => 'required|integer|min:0',
    //         'items.*.qty_masuk' => 'required|integer|min:0',
    //         'items.*.catatan' => 'nullable|string',
    //         'bukti_transaksi' => 'required|array|min:1',
    //         'bukti_transaksi.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     try {
    //         \DB::beginTransaction();

    //         // Generate kode report terlebih dahulu
    //         $kodeReport = FormReportSPG::generateKodeReport();
            
    //         // Buat header report
    //         $report = FormReportSPG::create([
    //             'kode_report' => $kodeReport,
    //             'tanggal' => $request->tanggal,
    //             'user_id' => Auth::id(),
    //             'nama_spg' => Auth::user()->name,
    //             'toko_id' => $request->toko_id,
    //         ]);

    //         // Simpan detail items
    //         foreach ($request->items as $itemData) {
    //             $itemMaster = ItemMaster::where('item_code', $itemData['item_code'])->first();
                
    //             if (!$itemMaster) {
    //                 $itemMaster = ItemMasterTambahan::where('item_code', $itemData['item_code'])->first();
    //             }
                
    //             if ($itemMaster) {
    //                 FormReportSPGDetail::create([
    //                     'report_id' => $report->id,
    //                     'item_code' => $itemData['item_code'],
    //                     'nama_barang' => $itemMaster->item_name,
    //                     'ukuran' => $itemMaster->ukuran,
    //                     'qty_terjual' => $itemData['qty_terjual'],
    //                     'qty_masuk' => $itemData['qty_masuk'],
    //                     'catatan' => $itemData['catatan'] ?? null,
    //                 ]);
    //             }
    //         }

    //         // Simpan bukti transaksi
    //         if ($request->hasFile('bukti_transaksi')) {
    //             $timestamp = time();
                
    //             foreach ($request->file('bukti_transaksi') as $index => $file) {
    //                 // Format: KODEREPORT_TIMESTAMP_RANDOM.EXTENSION
    //                 $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);
    //                 $extension = $file->getClientOriginalExtension();
                    
    //                 // Format nama file: RPS202512120005_1702789200_abc123.jpg
    //                 $filename = $kodeReport . '_' . $timestamp . '_' . $randomString . '.' . $extension;
                    
    //                 // Debug: Lihat nama file yang akan disimpan
    //                 \Log::info('Saving file:', [
    //                     'original_name' => $file->getClientOriginalName(),
    //                     'saved_as' => $filename,
    //                     'kode_report' => $kodeReport,
    //                     'timestamp' => $timestamp,
    //                     'random_string' => $randomString,
    //                 ]);
                    
    //                 // Simpan file ke public/storage/bukti_transaksi
    //                 $path = $file->storeAs('bukti_transaksi', $filename, 'public');
                    
    //                 // Simpan ke database
    //                 BuktiTransaksi::create([
    //                     'form_reportspg_id' => $report->id,
    //                     'nama_file' => $filename,
    //                 ]);
                    
    //                 // Tambah delay kecil untuk timestamp unik per file
    //                 usleep(1000); // 1ms delay
    //             }
    //         }

    //         \DB::commit();

    //         // Jika request AJAX, return JSON response
    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Report penjualan beserta bukti transaksi berhasil disimpan.',
    //                 'redirect' => route('reportspg.show', $report)
    //             ]);
    //         }

    //         return redirect()->route('reportspg.show', $report)
    //             ->with('success', 'Report penjualan beserta bukti transaksi berhasil disimpan.');
                
    //     } catch (\Exception $e) {
    //         \DB::rollBack();
            
    //         \Log::error('Error saving report:', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
            
    //         // Jika request AJAX, return JSON error
    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    //             ], 500);
    //         }
            
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
    //             ->withInput();
    //     }
    // }
    
    public function show(FormReportSPG $reportspg)
    {
        // Gunakan helper function untuk check authorization
        $this->checkAuthorization($reportspg);
        
        $reportspg->load('details');
        return view('reportspg.show', compact('reportspg'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FormReportSPG $reportspg)
    {
        // Gunakan helper function untuk check authorization
        $this->checkAuthorization($reportspg);
        
        // Ambil data dari kedua sumber
        $itemsMaster = ItemMaster::orderBy('item_name')->get(['item_code', 'item_name', 'ukuran']);
        $itemsTambahan = ItemMasterTambahan::orderBy('item_name')->get(['item_code', 'item_name', 'ukuran']);
        
        // Gabungkan kedua koleksi
        $items = $itemsMaster->merge($itemsTambahan)->sortBy('item_name');
        
        $reportspg->load('details');
        
        return view('reportspg.edit', compact('reportspg', 'items'));
    }

    // Fix sebelum ada inputan lost dkk
    // public function update(Request $request, FormReportSPG $reportspg)
    // {
    //     // Gunakan helper function untuk check authorization
    //     $this->checkAuthorization($reportspg);

    //     // Validasi untuk item_code dari kedua tabel
    //     $validated = $request->validate([
    //         'tanggal' => 'required|date',
    //         'no_sales' => 'nullable|boolean',
    //         'items' => 'nullable|array',
    //         'items.*.item_code' => [
    //             'required_without:no_sales',
    //             function ($attribute, $value, $fail) {
    //                 $existsInMaster = ItemMaster::where('item_code', $value)->exists();
    //                 $existsInTambahan = ItemMasterTambahan::where('item_code', $value)->exists();
                    
    //                 if (!$existsInMaster && !$existsInTambahan) {
    //                     $fail('Item code tidak valid.');
    //                 }
    //             },
    //         ],
    //         'items.*.qty_terjual' => 'required_without:no_sales|integer|min:0',
    //         'items.*.qty_masuk' => 'required_without:no_sales|integer|min:0',
    //         'items.*.catatan' => 'nullable|string',
    //     ]);

    //     try {
    //         // Mulai transaksi
    //         \DB::beginTransaction();

    //         // Update header report
    //         $updateData = [
    //             'tanggal' => $request->tanggal,
    //             'no_sales' => $request->has('no_sales') ? true : false,
    //         ];
            
    //         $reportspg->update($updateData);

    //         // Hapus detail lama
    //         $reportspg->details()->delete();

    //         // Simpan detail items baru hanya jika bukan no sales
    //         if (!$request->has('no_sales') && !empty($request->items)) {
    //             foreach ($request->items as $itemData) {
    //                 // Cari item dari ItemMaster terlebih dahulu
    //                 $itemMaster = ItemMaster::where('item_code', $itemData['item_code'])->first();
                    
    //                 // Jika tidak ditemukan di ItemMaster, cari di ItemMasterTambahan
    //                 if (!$itemMaster) {
    //                     $itemMaster = ItemMasterTambahan::where('item_code', $itemData['item_code'])->first();
    //                 }
                    
    //                 if ($itemMaster) {
    //                     FormReportSPGDetail::create([
    //                         'report_id' => $reportspg->id,
    //                         'item_code' => $itemData['item_code'],
    //                         'nama_barang' => $itemMaster->item_name,
    //                         'ukuran' => $itemMaster->ukuran,
    //                         'qty_terjual' => $itemData['qty_terjual'],
    //                         'qty_masuk' => $itemData['qty_masuk'],
    //                         'catatan' => $itemData['catatan'] ?? null,
    //                     ]);
    //                 }
    //             }
    //         }

    //         \DB::commit();

    //         return redirect()->route('reportspg.show', $reportspg)
    //             ->with('success', 'Report penjualan berhasil diperbarui.');
                
    //     } catch (\Exception $e) {
    //         \DB::rollBack();
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
    //             ->withInput();
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FormReportSPG $reportspg)
    {
        // Gunakan helper function untuk check authorization
        $this->checkAuthorization($reportspg);

        $reportspg->delete();
        
        return redirect()->route('reportspg.index')
            ->with('success', 'Report penjualan berhasil dihapus.');
    }

    public function getItems(Request $request)
    {
        $search = $request->input('q');
        $user = Auth::user();
        $userId = $user->id_customer;
        
        // Ambil code_item dari merk yang dimiliki user
        if ($user->role_as == 1) {
            // Admin: bisa lihat semua item dari ItemMaster
            $showTambahanItems = true; // Admin bisa lihat tambahan
            $merkCodes = [];
            $isAdmin = true;
        } else {
            // Role lain: hanya merk yang sesuai user
            $merkCodes = Merk::select('merks.code_item')
                ->join('users_merks', 'users_merks.id_merks', '=', 'merks.id')
                ->where('users_merks.id_customer', $userId)
                ->pluck('code_item')
                ->toArray();
            
            $showTambahanItems = in_array('TAMBAHAN', $merkCodes);
            $isAdmin = false;
            
            // Hapus 'TAMBAHAN' dari array merkCodes untuk filter ItemMaster
            $merkCodes = array_filter($merkCodes, function($code) {
                return $code !== 'TAMBAHAN';
            });
        }
        
        // Query untuk ItemMaster
        $masterQuery = ItemMaster::select('item_code', 'item_name', 'ukuran');
        
        // Filter berdasarkan merk user (kecuali admin)
        if (!$isAdmin && !empty($merkCodes)) {
            $masterQuery->where(function($query) use ($merkCodes) {
                foreach ($merkCodes as $index => $merkCode) {
                    if ($index === 0) {
                        $query->whereRaw("SUBSTRING(item_code, 2, 1) = ?", [$merkCode]);
                    } else {
                        $query->orWhereRaw("SUBSTRING(item_code, 2, 1) = ?", [$merkCode]);
                    }
                }
            });
        }
        
        // Jika ada search, terapkan ke query master
        if ($search) {
            $searchTerm = '%' . $search . '%';
            $masterQuery->where(function($query) use ($searchTerm) {
                $query->where('item_code', 'like', $searchTerm)
                    ->orWhere('item_name', 'like', $searchTerm)
                    ->orWhere('ukuran', 'like', $searchTerm);
            });
        }
        
        // Query untuk ItemMasterTambahan (hanya jika user memiliki akses TAMBAHAN atau admin)
        $tambahanQuery = null;
        if ($showTambahanItems || $isAdmin) {
            $tambahanQuery = ItemMasterTambahan::where('status', 1)
                ->select('item_code', 'item_name', 'ukuran');
            
            // Jika ada search, terapkan ke query tambahan juga
            if ($search) {
                $searchTerm = '%' . $search . '%';
                $tambahanQuery->where(function($query) use ($searchTerm) {
                    $query->where('item_code', 'like', $searchTerm)
                        ->orWhere('item_name', 'like', $searchTerm)
                        ->orWhere('ukuran', 'like', $searchTerm);
                });
            }
        }
        
        // Tentukan query yang akan digunakan
        if ($tambahanQuery) {
            // Gabungkan dengan UNION
            $query = $masterQuery->union($tambahanQuery);
        } else {
            $query = $masterQuery;
        }
        
        // Eksekusi query dengan order dan limit
        $items = $query->orderBy('item_name')
            ->limit(20)
            ->get();
        
        // Format hasil
        $formattedItems = $items->map(function($item) {
            return [
                'id' => $item->item_code,
                'text' => "{$item->item_code} - {$item->item_name} - {$item->ukuran}",
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'ukuran' => $item->ukuran
            ];
        });
        
        return response()->json($formattedItems);
    }

}