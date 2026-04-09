<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\DaftarToko;
use App\Models\ItemMaster;
use App\Models\ItemMasterTambahan;
use App\Models\Merk;
use App\Models\UsersMerks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockOpnameExport;
use Illuminate\Support\Facades\Validator;


class StockOpnameController extends Controller
{
    /**
     * Helper function untuk check authorization berdasarkan role
     */
    private function checkAuthorization(StockOpname $opname)
    {
        $user = Auth::user();
        
        // Jika user adalah admin (role_as = 1), izinkan akses
        if ($user->role_as == 1) {
            return true;
        }
        
        // Jika user biasa (role_as = 0), cek apakah data miliknya
        if ($opname->user_id != $user->id) {
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

        $query = StockOpname::with(['details', 'toko']);
        
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
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan search (kode opname, nama toko, nama spg)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_opname', 'like', '%' . $search . '%')
                  ->orWhere('nama_spg', 'like', '%' . $search . '%')
                  ->orWhereHas('toko', function($q) use ($search) {
                      $q->where('nama_toko', 'like', '%' . $search . '%');
                  });
            });
        }
        
        $opnames = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Ambil daftar toko untuk filter (admin only)
        $tokoList = $user->role_as == 1 ? DaftarToko::orderBy('nama_toko')->get() : [];
        
        // Simpan filter untuk view
        $filters = [
            'tahun' => $tahun,
            'bulan' => $request->bulan ?? date('n'),
            'status' => $request->status,
            'search' => $request->search,
        ];
        
        return view('stockopname.index', compact('opnames', 'filters', 'tokoList'));
    }

    /**
     * Show the form for creating a new resource.
     */
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
            return redirect()->route('stockopname.index')
                ->with('error', 'Anda tidak memiliki toko yang ditugaskan. Silakan hubungi admin.');
        }
        
        // Ambil data dari kedua sumber
        $itemsMaster = ItemMaster::orderBy('item_name')->get(['item_code', 'item_name', 'ukuran']);
        $itemsTambahan = ItemMasterTambahan::orderBy('item_name')->get(['item_code', 'item_name', 'ukuran']);
        
        // Gabungkan kedua koleksi
        $items = $itemsMaster->merge($itemsTambahan)->sortBy('item_name');
        
        // Jika ada parameter toko_id, ambil periode berikutnya
        $initialData = null;
        if ($request->has('toko_id')) {
            $tokoId = $request->toko_id;
            $nextPeriod = StockOpname::getNextPeriod($tokoId);
            
            $initialData = [
                'toko_id' => $tokoId,
                'tahun' => $nextPeriod['tahun'],
                'bulan' => $nextPeriod['bulan'],
            ];
        }
        
        return view('stockopname.create', compact('items', 'tokoList', 'initialData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        // dd($request->all());
        // Validasi untuk item_code dari kedua tabel
        $validated = $request->validate([
            'toko_id' => 'required|exists:daftar_toko,id',
            'tahun' => 'required|integer|min:2023|max:' . (date('Y') + 1),
            'bulan' => 'required|integer|min:1|max:12',
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
            'items.*.keterangan' => 'nullable|string',
        ]);

        // Cek apakah sudah ada stock opname untuk toko, tahun, dan bulan ini
        // $existingOpname = StockOpname::where('toko_id', $request->toko_id)
        //     ->where('tahun', $request->tahun)
        //     ->where('bulan', $request->bulan)
        //     ->first();
            
        // if ($existingOpname) {
        //     return redirect()->back()
        //         ->with('error', 'Stock opname untuk toko ini pada bulan ' . $request->bulan . ' tahun ' . $request->tahun . ' sudah ada.')
        //         ->withInput();
        // }

        try {
            // Mulai transaksi
            \DB::beginTransaction();

            // Ambil data toko
            $toko = DaftarToko::findOrFail($request->toko_id);

            // Buat header stock opname
            $opname = StockOpname::create([
                'kode_opname' => StockOpname::generateKodeOpname(),
                'user_id' => Auth::id(),
                'nama_spg' => Auth::user()->name,
                'toko_id' => $request->toko_id,
                'nama_toko' => $toko->nama_toko,
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'tanggal' => $request->tanggal,
                'status' => 'draft',
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
                    StockOpnameDetail::create([
                        'opname_id' => $opname->id,
                        'item_code' => $itemData['item_code'],
                        'nama_barang' => $itemMaster->item_name,
                        'ukuran' => $itemMaster->ukuran,
                        'stock' => $itemData['stock'],
                        'keterangan' => $itemData['keterangan'] ?? null,
                    ]);
                }
            }

            \DB::commit();

            return redirect()->route('stockopname.show', $opname)
                ->with('success', 'Stock opname berhasil disimpan.');
                
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function checkDuplicate(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'toko_id' => 'required|integer',
                'tahun' => 'required|integer',
                'bulan' => 'required|integer|between:1,12'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Data tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Cek apakah sudah ada stock opname untuk toko, tahun, dan bulan ini
            $exists = StockOpname::where('toko_id', $request->toko_id)
                ->where('tahun', $request->tahun)
                ->where('bulan', $request->bulan)
                ->exists();
            
            return response()->json([
                'exists' => $exists,
                'message' => $exists 
                    ? 'Stock opname sudah ada untuk periode ini' 
                    : 'OK'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in checkDuplicate: ' . $e->getMessage());
            
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockOpname $stockopname)
    {
        // Gunakan helper function untuk check authorization
        $this->checkAuthorization($stockopname);
        
        $stockopname->load('details', 'toko');
        return view('stockopname.show', compact('stockopname'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockOpname $stockopname)
    {
        // Gunakan helper function untuk check authorization
        $this->checkAuthorization($stockopname);
        
        $user = Auth::user();
        
        // Ambil daftar toko
        if ($user->role_as == 1) {
            $tokoList = DaftarToko::orderBy('nama_toko')->get();
        } else {
            $tokoList = DaftarToko::where('kode_spg', $user->id_customer)
                ->orderBy('nama_toko')
                ->get();
        }
        
        // Ambil data dari kedua sumber
        $itemsMaster = ItemMaster::orderBy('item_name')->get(['item_code', 'item_name', 'ukuran']);
        $itemsTambahan = ItemMasterTambahan::orderBy('item_name')->get(['item_code', 'item_name', 'ukuran']);
        
        // Gabungkan kedua koleksi
        $items = $itemsMaster->merge($itemsTambahan)->sortBy('item_name');
        
        $stockopname->load('details');
        
        return view('stockopname.edit', compact('stockopname', 'items', 'tokoList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockOpname $stockopname)
    {
        // dd($request->all());
        // Gunakan helper function untuk check authorization
        $this->checkAuthorization($stockopname);

        // Validasi untuk item_code dari kedua tabel
        $validated = $request->validate([
            'toko_id' => 'required|exists:daftar_toko,id',
            'tahun' => 'required|integer|min:2023|max:' . (date('Y') + 1),
            'bulan' => 'required|integer|min:1|max:12',
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
            'items.*.keterangan' => 'nullable|string',
        ]);

        // // Cek apakah sudah ada stock opname lain untuk toko, tahun, dan bulan ini
        // $existingOpname = StockOpname::where('toko_id', $request->toko_id)
        //     ->where('tahun', $request->tahun)
        //     ->where('bulan', $request->bulan)
        //     ->where('id', '!=', $stockopname->id)
        //     ->first();
            
        // if ($existingOpname) {
        //     return redirect()->back()
        //         ->with('error', 'Stock opname untuk toko ini pada bulan ' . $request->bulan . ' tahun ' . $request->tahun . ' sudah ada.')
        //         ->withInput();
        // }

        try {
            // Mulai transaksi
            \DB::beginTransaction();

            // Ambil data toko
            $toko = DaftarToko::findOrFail($request->toko_id);

            // Update header stock opname
            $stockopname->update([
                'toko_id' => $request->toko_id,
                'nama_toko' => $toko->nama_toko,
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'tanggal' => $request->tanggal,
            ]);

            // Hapus detail lama
            $stockopname->details()->delete();

            // Simpan detail items baru
            foreach ($request->items as $itemData) {
                // Cari item dari ItemMaster terlebih dahulu
                $itemMaster = ItemMaster::where('item_code', $itemData['item_code'])->first();
                
                // Jika tidak ditemukan di ItemMaster, cari di ItemMasterTambahan
                if (!$itemMaster) {
                    $itemMaster = ItemMasterTambahan::where('item_code', $itemData['item_code'])->first();
                }
                
                if ($itemMaster) {
                    StockOpnameDetail::create([
                        'opname_id' => $stockopname->id,
                        'item_code' => $itemData['item_code'],
                        'nama_barang' => $itemMaster->item_name,
                        'ukuran' => $itemMaster->ukuran,
                        'stock' => $itemData['stock'],
                        'keterangan' => $itemData['keterangan'] ?? null,
                    ]);
                }
            }

            \DB::commit();

            return redirect()->route('stockopname.show', $stockopname)
                ->with('success', 'Stock opname berhasil diperbarui.');
                
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
    public function destroy(StockOpname $stockopname)
    {
        // Gunakan helper function untuk check authorization
        $this->checkAuthorization($stockopname);

        $stockopname->delete();
        
        return redirect()->route('stockopname.index')
            ->with('success', 'Stock opname berhasil dihapus.');
    }

    /**
     * Export data to Excel
     */
    public function exportExcel(Request $request)
    {
        // dd($request->all());
        $user = Auth::user();
        
        // Set default values jika tidak ada filter
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('n');
        
        // Validasi
        $validator = Validator::make([
            'tahun' => $tahun,
            'bulan' => $bulan,
        ], [
            'tahun' => 'required|integer|min:2023|max:' . (date('Y') + 1),
            'bulan' => 'required|integer|min:1|max:12',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $filters = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'user_role' => $user->role_as,
        ];
        
        // Jika user bukan admin, filter berdasarkan user_id
        if ($user->role_as == 0) {
            $filters['user_id'] = $user->id;
        }
        
        // Buat nama file
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $filename = 'Stock_Opname_' . $tahun . '_' . ($bulanNama[$bulan] ?? $bulan) . '.xlsx';
        
        return Excel::download(new StockOpnameExport($filters), $filename);
    }

    /**
     * Update status stock opname
     */
    public function updateStatus(Request $request, StockOpname $stockopname)
    {
        // Hanya admin yang bisa update status
        if (Auth::user()->role_as != 1) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);
        
        $stockopname->update([
            'status' => $request->status,
        ]);
        
        return redirect()->back()
            ->with('success', 'Status stock opname berhasil diperbarui.');
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