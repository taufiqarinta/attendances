<?php

namespace App\Http\Controllers;

use App\Models\ReportStockSPG;
use App\Models\ReportStockSPGDetail;
use App\Models\DaftarToko;
use App\Models\ItemMaster;
use App\Models\Merk;
use App\Models\UsersMerks;
use App\Models\ItemMasterTambahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportStockSPGExport;
use Illuminate\Support\Facades\Validator;

class ReportStockSPGController extends Controller
{
    /**
     * Helper function untuk check authorization berdasarkan role
     */
    private function checkAuthorization(ReportStockSPG $report)
    {
        $user = Auth::user();
        
        // Jika user adalah admin (role_as = 1), izinkan akses
        if ($user->role_as == 1) {
            return true;
        }
        
        // Jika user biasa (role_as = 0), cek apakah data miliknya
        if ($report->user_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        return true;
    }

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = ReportStockSPG::with(['details', 'toko']);
        
        if ($user->role_as == 0) {
            $query->where('user_id', $user->id);
        }
        
        // Set default tahun ke tahun sekarang jika kosong
        $tahun = $request->tahun ?? date('Y');
        
        // Set default bulan ke bulan sekarang jika kosong
        $bulan = $request->bulan ?? date('n');
        
        // Filter berdasarkan tahun
        $query->where('tahun', $tahun);
        
        // Filter berdasarkan bulan - jika ada pilihan bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $bulan);
        }
        
        // Filter berdasarkan minggu
        if ($request->filled('minggu_ke')) {
            $query->where('minggu_ke', $request->minggu_ke);
        }
        
        // Filter berdasarkan search (kode report, nama toko, nama spg)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_report', 'like', '%' . $search . '%')
                ->orWhere('nama_spg', 'like', '%' . $search . '%')
                ->orWhereHas('toko', function($q) use ($search) {
                    $q->where('nama_toko', 'like', '%' . $search . '%');
                });
            });
        }
        
        $reports = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('minggu_ke', 'desc')
            ->paginate(10);
            
        // Ambil daftar toko untuk filter (admin only)
        $tokoList = $user->role_as == 1 ? DaftarToko::orderBy('nama_toko')->get() : [];
        
        // Simpan filter untuk view
        $filters = [
            'tahun' => $tahun,
            'bulan' => $request->bulan ?? date('n'),
            'minggu_ke' => $request->minggu_ke,
            'search' => $request->search, // <-- Tambahkan search ke filters
        ];
        
        return view('reportstockspg.index', compact('reports', 'filters', 'tokoList'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Ambil daftar toko berdasarkan user
        if ($user->role_as == 1) {
            // Admin bisa pilih semua toko
            $tokoList = DaftarToko::where('status', 1)
            ->orderBy('nama_toko')->get();
        } else {
            $tokoList = DaftarToko::where('kode_spg', $user->id_customer)
                ->where('status', 1)
                ->orderBy('nama_toko')
                ->get();
        }
        
        // Cek apakah user memiliki toko
        if ($tokoList->isEmpty()) {
            return redirect()->route('reportstockspg.index')
                ->with('error', 'Anda tidak memiliki toko yang ditugaskan. Silakan hubungi admin.');
        }
        
        // Ambil data dari kedua sumber
        $itemsMaster = ItemMaster::orderBy('item_name')->get(['item_code', 'item_name', 'ukuran']);
        $itemsTambahan = ItemMasterTambahan::orderBy('item_name')->get(['item_code', 'item_name', 'ukuran']);
        
        // Gabungkan kedua koleksi
        $items = $itemsMaster->merge($itemsTambahan)->sortBy('item_name');
        
        // Jika ada parameter toko_id, load latest data
        $initialData = null;
        if ($request->has('toko_id')) {
            $tokoId = $request->toko_id;
            $latestReport = ReportStockSPG::where('toko_id', $tokoId)
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->orderBy('minggu_ke', 'desc')
                ->first();
                
            if ($latestReport) {
                $nextPeriod = ReportStockSPG::getNextPeriod($tokoId);
                $initialData = [
                    'toko_id' => $tokoId,
                    'tahun' => $nextPeriod['tahun'],
                    'bulan' => $nextPeriod['bulan'],
                    'minggu_ke' => $nextPeriod['minggu_ke'],
                    'previous_report' => $latestReport
                ];
            }
        }
        
        return view('reportstockspg.create', compact('items', 'tokoList', 'initialData'));
    }

    public function checkDuplicate(Request $request)
    {
        $request->validate([
            'toko_id' => 'required|exists:daftar_toko,id',
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|between:1,12',
            'minggu_ke' => 'required|integer|between:1,5'
        ]);
        
        $exists = ReportStockSPG::where('toko_id', $request->toko_id)
            ->where('tahun', $request->tahun)
            ->where('bulan', $request->bulan)
            ->where('minggu_ke', $request->minggu_ke)
            ->exists();
        
        return response()->json(['exists' => $exists]);
    }


    public function store(Request $request)
    {
        // Validasi untuk item_code dari kedua tabel
        $validated = $request->validate([
            'toko_id' => 'required|exists:daftar_toko,id',
            'tahun' => 'required|integer|min:2023|max:' . (date('Y') + 1),
            'bulan' => 'required|integer|min:1|max:12',
            'minggu_ke' => 'required|integer|min:1|max:5',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_code' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existsInMaster = ItemMaster::where('item_code', $value)->exists();
                    $existsInTambahan = ItemMasterTambahan::where('item_code', $value)->exists();
                    
                    if (!$existsInMaster && !$existsInTambahan) {
                        $fail('Item code tidak valid.');
                    }
                },
            ],
            'items.*.stock' => 'required|integer|min:0',
            'items.*.qty_masuk' => 'required|integer|min:0',
            'items.*.catatan' => 'nullable|string',
        ]);

        // Cek apakah sudah ada report untuk toko, tahun, bulan, dan minggu ini
        // $existingReport = ReportStockSPG::where('toko_id', $request->toko_id)
        //     ->where('tahun', $request->tahun)
        //     ->where('bulan', $request->bulan)
        //     ->where('minggu_ke', $request->minggu_ke)
        //     ->first();
            
        // if ($existingReport) {
        //     return redirect()->back()
        //         ->with('error', 'Report stock untuk toko ini pada minggu ke-' . $request->minggu_ke . ' bulan ' . $request->bulan . ' tahun ' . $request->tahun . ' sudah ada.')
        //         ->withInput();
        // }

        try {
            // Mulai transaksi
            \DB::beginTransaction();

            // Ambil data toko
            $toko = DaftarToko::findOrFail($request->toko_id);

            // Buat header report
            $report = ReportStockSPG::create([
                'kode_report' => ReportStockSPG::generateKodeReport(),
                'user_id' => Auth::id(),
                'nama_spg' => Auth::user()->name,
                'toko_id' => $request->toko_id,
                'nama_toko' => $toko->nama_toko,
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'minggu_ke' => $request->minggu_ke,
                'tanggal' => $request->tanggal,
            ]);

            // Simpan detail items
            foreach ($request->items as $itemData) {
                // Cari item dari ItemMaster terlebih dahulu
                $itemMaster = ItemMaster::where('item_code', $itemData['item_code'])->first();
                
                // Jika tidak ditemukan di ItemMaster, cari di ItemMasterTambahan
                if (!$itemMaster) {
                    $itemMaster = ItemMasterTambahan::where('item_code', $itemData['item_code'])->first();
                }
                
                if ($itemMaster) {
                    ReportStockSPGDetail::create([
                        'report_id' => $report->id,
                        'item_code' => $itemData['item_code'],
                        'nama_barang' => $itemMaster->item_name,
                        'ukuran' => $itemMaster->ukuran,
                        'stock' => $itemData['stock'],
                        'qty_masuk' => $itemData['qty_masuk'],
                        'catatan' => $itemData['catatan'] ?? null,
                    ]);
                }
            }

            \DB::commit();

            return redirect()->route('reportstockspg.show', $report)
                ->with('success', 'Report stock berhasil disimpan.');
                
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ReportStockSPG $reportstockspg)
    {
        // Gunakan helper function untuk check authorization
        $this->checkAuthorization($reportstockspg);
        
        $reportstockspg->load('details', 'toko');
        return view('reportstockspg.show', compact('reportstockspg'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReportStockSPG $reportstockspg)
    {
        $this->checkAuthorization($reportstockspg);
        
        $user = Auth::user();
        
        // Ambil daftar toko
        if ($user->role_as == 1) {
            $tokoList = DaftarToko::orderBy('nama_toko')->get();
        } else {
            $tokoList = DaftarToko::where('nama_spg', $user->name)
                ->orWhere('kode_spg', $user->id_customer)
                ->orderBy('nama_toko')
                ->get();
        }
        
        $reportstockspg->load('details');
        
        // HAPUS $items dari compact
        return view('reportstockspg.edit', compact('reportstockspg', 'tokoList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReportStockSPG $reportstockspg)
    {
        // Gunakan helper function untuk check authorization
        $this->checkAuthorization($reportstockspg);

        // Validasi untuk item_code dari kedua tabel
        $validated = $request->validate([
            'toko_id' => 'required|exists:daftar_toko,id',
            'tahun' => 'required|integer|min:2023|max:' . (date('Y') + 1),
            'bulan' => 'required|integer|min:1|max:12',
            'minggu_ke' => 'required|integer|min:1|max:5',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_code' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existsInMaster = ItemMaster::where('item_code', $value)->exists();
                    $existsInTambahan = ItemMasterTambahan::where('item_code', $value)->exists();
                    
                    if (!$existsInMaster && !$existsInTambahan) {
                        $fail('Item code tidak valid.');
                    }
                },
            ],
            'items.*.stock' => 'required|integer|min:0',
            'items.*.qty_masuk' => 'required|integer|min:0',
            'items.*.catatan' => 'nullable|string',
        ]);

        // Cek apakah sudah ada report lain untuk toko, tahun, bulan, dan minggu ini
        // $existingReport = ReportStockSPG::where('toko_id', $request->toko_id)
        //     ->where('tahun', $request->tahun)
        //     ->where('bulan', $request->bulan)
        //     ->where('minggu_ke', $request->minggu_ke)
        //     ->where('id', '!=', $reportstockspg->id)
        //     ->first();
            
        // if ($existingReport) {
        //     return redirect()->back()
        //         ->with('error', 'Report stock untuk toko ini pada minggu ke-' . $request->minggu_ke . ' bulan ' . $request->bulan . ' tahun ' . $request->tahun . ' sudah ada.')
        //         ->withInput();
        // }

        try {
            // Mulai transaksi
            \DB::beginTransaction();

            // Ambil data toko
            $toko = DaftarToko::findOrFail($request->toko_id);

            // Update header report
            $reportstockspg->update([
                'toko_id' => $request->toko_id,
                'nama_toko' => $toko->nama_toko,
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'minggu_ke' => $request->minggu_ke,
                'tanggal' => $request->tanggal,
            ]);

            // Hapus detail lama
            $reportstockspg->details()->delete();

            // Simpan detail items baru
            foreach ($request->items as $itemData) {
                // Cari item dari ItemMaster terlebih dahulu
                $itemMaster = ItemMaster::where('item_code', $itemData['item_code'])->first();
                
                // Jika tidak ditemukan di ItemMaster, cari di ItemMasterTambahan
                if (!$itemMaster) {
                    $itemMaster = ItemMasterTambahan::where('item_code', $itemData['item_code'])->first();
                }
                
                if ($itemMaster) {
                    ReportStockSPGDetail::create([
                        'report_id' => $reportstockspg->id,
                        'item_code' => $itemData['item_code'],
                        'nama_barang' => $itemMaster->item_name,
                        'ukuran' => $itemMaster->ukuran,
                        'stock' => $itemData['stock'],
                        'qty_masuk' => $itemData['qty_masuk'],
                        'catatan' => $itemData['catatan'] ?? null,
                    ]);
                }
            }

            \DB::commit();

            return redirect()->route('reportstockspg.show', $reportstockspg)
                ->with('success', 'Report stock berhasil diperbarui.');
                
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReportStockSPG $reportstockspg)
    {
        // Gunakan helper function untuk check authorization
        $this->checkAuthorization($reportstockspg);

        $reportstockspg->delete();
        
        return redirect()->route('reportstockspg.index')
            ->with('success', 'Report stock berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        
        // Set default values jika tidak ada filter
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('n');
        $minggu_ke = $request->minggu_ke ?? null;
        $search = $request->search ?? null; // Tambahkan search untuk filter
        
        // Validasi
        $validator = Validator::make([
            'tahun' => $tahun,
            'bulan' => $bulan,
            'minggu_ke' => $minggu_ke,
        ], [
            'tahun' => 'required|integer|min:2023|max:' . (date('Y') + 1),
            'bulan' => 'required|integer|min:1|max:12',
            'minggu_ke' => 'nullable|integer|min:1|max:5',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $filters = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'minggu_ke' => $minggu_ke,
            'search' => $search,
            'user_role' => $user->role_as,
        ];
        
        // Jika user bukan admin, filter berdasarkan user_id
        if ($user->role_as == 0) {
            $filters['user_id'] = $user->id;
        }
        
        // Buat nama file yang lebih deskriptif
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $filename = 'Report_Stock_SPG_' . $tahun . '_' . ($bulanNama[$bulan] ?? $bulan);
        
        if ($minggu_ke) {
            $filename .= '_Minggu_' . $minggu_ke;
        }
        
        $filename .= '.xlsx';
        
        return Excel::download(new ReportStockSPGExport($filters), $filename);
    }

    // public function exportExcel(Request $request)
    // {
    //     $user = Auth::user();
        
    //     // Set default values jika tidak ada filter
    //     $tahun = $request->tahun ?? date('Y');
    //     $bulan = $request->bulan ?? date('n');
    //     $minggu_ke = $request->minggu_ke ?? null;
        
    //     // Validasi
    //     $validator = Validator::make([
    //         'tahun' => $tahun,
    //         'bulan' => $bulan,
    //         'minggu_ke' => $minggu_ke,
    //     ], [
    //         'tahun' => 'required|integer|min:2023|max:' . (date('Y') + 1),
    //         'bulan' => 'required|integer|min:1|max:12',
    //         'minggu_ke' => 'nullable|integer|min:1|max:5',
    //     ]);
        
    //     if ($validator->fails()) {
    //         return redirect()->back()
    //             ->withErrors($validator)
    //             ->withInput();
    //     }
        
    //     $filters = [
    //         'tahun' => $tahun,
    //         'bulan' => $bulan,
    //         'minggu_ke' => $minggu_ke,
    //         'user_role' => $user->role_as,
    //     ];
        
    //     // Jika user bukan admin, filter berdasarkan user_id
    //     if ($user->role_as == 0) {
    //         $filters['user_id'] = $user->id;
    //     }
        
    //     // Buat nama file yang lebih deskriptif
    //     $bulanNama = [
    //         1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    //         5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    //         9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    //     ];
        
    //     $filename = 'Report_Stock_SPG_' . $tahun . '_' . ($bulanNama[$bulan] ?? $bulan);
        
    //     if ($minggu_ke) {
    //         $filename .= '_Minggu_' . $minggu_ke;
    //     }
        
    //     $filename .= '.xlsx';
        
    //     return Excel::download(new ReportStockSPGExport($filters), $filename);
    // }

    /**
     * API untuk get previous week stock
     */
    public function getPreviousStock(Request $request)
    {
        $request->validate([
            'toko_id' => 'required|exists:daftar_toko,id',
            'tahun' => 'required|integer',
            'bulan' => 'required|integer',
            'minggu_ke' => 'required|integer',
        ]);
        
        $previousReport = ReportStockSPG::getPreviousStock(
            $request->toko_id,
            $request->tahun,
            $request->bulan,
            $request->minggu_ke
        );
        
        if ($previousReport) {
            $previousReport->load('details');
            return response()->json([
                'success' => true,
                'data' => $previousReport->details->map(function($detail) {
                    return [
                        'item_code' => $detail->item_code,
                        'nama_barang' => $detail->nama_barang,
                        'ukuran' => $detail->ukuran,
                        'stock' => $detail->stock,
                        'qty_masuk' => $detail->qty_masuk,
                        'catatan' => $detail->catatan,
                    ];
                })
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada data stock sebelumnya'
        ]);
    }

    // public function getLatestStock(Request $request)
    // {
    //     $request->validate([
    //         'toko_id' => 'required|exists:daftar_toko,id',
    //     ]);
        
    //     // Cari report stock terakhir untuk toko ini
    //     $latestReport = ReportStockSPG::where('toko_id', $request->toko_id)
    //         ->orderBy('tahun', 'desc')
    //         ->orderBy('bulan', 'desc')
    //         ->orderBy('minggu_ke', 'desc')
    //         ->orderBy('created_at', 'desc')
    //         ->first();
        
    //     if ($latestReport) {
    //         $latestReport->load('details');
    //         return response()->json([
    //             'success' => true,
    //             'report' => [
    //                 'id' => $latestReport->id,
    //                 'kode_report' => $latestReport->kode_report,
    //                 'tahun' => $latestReport->tahun,
    //                 'bulan' => $latestReport->bulan,
    //                 'minggu_ke' => $latestReport->minggu_ke,
    //                 'tanggal' => $latestReport->tanggal->format('Y-m-d'),
    //             ],
    //             'items' => $latestReport->details->map(function($detail) {
    //                 return [
    //                     'item_code' => $detail->item_code,
    //                     'nama_barang' => $detail->nama_barang,
    //                     'ukuran' => $detail->ukuran,
    //                     'stock' => $detail->stock,
    //                     'qty_masuk' => $detail->qty_masuk,
    //                     'catatan' => $detail->catatan,
    //                 ];
    //             })
    //         ]);
    //     }
        
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Belum ada data stock untuk toko ini'
    //     ]);
    // }

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