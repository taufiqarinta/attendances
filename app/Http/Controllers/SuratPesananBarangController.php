<?php

namespace App\Http\Controllers;

use App\Models\SuratPesananBarang;
use App\Models\DaftarAgen;
use App\Models\SuratPesananBarangDetail;
use App\Exports\SuratPesananBarangExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\DaftarHarga;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratPesananBarangController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = SuratPesananBarang::with(['details', 'creator']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                ->orWhere('pengirim', 'like', "%{$search}%")
                ->orWhere('penerima', 'like', "%{$search}%")
                ->orWhere('pemesan', 'like', "%{$search}%")
                ->orWhereHas('creator', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('tanggal_surat', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('tanggal_surat', '<=', $request->end_date);
        }

        $suratPesananBarangs = $query->orderBy('created_at', 'desc')->paginate(10);

        // Pass filter values to view
        $filters = $request->only(['search', 'status', 'start_date', 'end_date']);

        $suratPesananBarangs->appends($filters);

        return view('suratpesananbarang.index', compact('suratPesananBarangs', 'filters'));
    }

    public function create()
    {
        // if (auth()->user()->department !== 'SLS') {
        //     abort(403, 'Hanya Sales yang dapat membuat surat pesanan barang');
        // }

        $nomorSurat = SuratPesananBarang::generateNomorSurat();
        $tanggalSurat = date('Y-m-d');
        $tanggalKirim = date('Y-m-d');

        // Get unique ukuran for dropdown
        $ukuranList = DaftarHarga::distinct()->pluck('ukuran')->filter();

        $daftarAgens = DaftarAgen::orderBy('nama_agen')->get();

        return view('suratpesananbarang.create', compact('nomorSurat', 'tanggalSurat', 'tanggalKirim', 'ukuranList','daftarAgens'));
    }

    public function store(Request $request)
    {
        // if (auth()->user()->department !== 'SLS') {
        //     abort(403, 'Hanya Sales yang dapat membuat surat pesanan barang');
        // }

        \Log::info('Store method called', ['user_id' => auth()->id(), 'data' => $request->all()]);

        try {
            $validated = $request->validate([
                'pengirim' => 'required|string|max:255',
                'penerima' => 'nullable|string|max:255',
                'dikirim_ke' => 'required|string',
                'tanggal_kirim' => 'required|date',
                'pemesan' => 'nullable|string',
                'jenis_harga' => 'required|in:franco,loco',
                'nomor_do' => 'required|string|max:128',
                'total_jumlah_box' => 'required|numeric|min:0',
                'details' => 'required|array|min:1',
                'details.*.ukuran' => 'required|string|max:255',
                'details.*.nama_product' => 'required|string|max:255',
                'details.*.brand' => 'required|string|max:255',
                'details.*.kw' => 'required|string|max:255',
                'details.*.jumlah_box' => 'required|numeric|min:0',
                'details.*.harga_satuan_box' => 'required|numeric|min:0',
                'details.*.disc' => 'nullable|numeric|min:0',
                'details.*.biaya_tambahan_ekspedisi' => 'nullable|numeric|min:0',
                'details.*.keterangan' => 'nullable|string',
            ]);

            \Log::info('Validation passed', ['validated_data' => $validated]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            $suratPesananBarang = SuratPesananBarang::create([
                'nomor_surat' => SuratPesananBarang::generateNomorSurat(),
                'tanggal_surat' => date('Y-m-d'),
                'tanggal_kirim' => $validated['tanggal_kirim'],
                'pengirim' => $validated['pengirim'],
                'penerima' => $validated['penerima'],
                'dikirim_ke' => $validated['dikirim_ke'],
                'status' => 'pending',
                'pemesan' => $validated['pemesan'] ?? null,
                'jenis_harga' => $validated['jenis_harga'],
                'nomor_do' => $validated['nomor_do'],
                'total_jumlahbox' => $validated['total_jumlah_box'],
                'created_by' => auth()->id(),
            ]);

            \Log::info('SuratPesananBarang created', ['id' => $suratPesananBarang->id]);

            foreach ($validated['details'] as $index => $detail) {
                \Log::info('Processing detail', ['index' => $index, 'detail' => $detail]);
                
                $detailModel = new SuratPesananBarangDetail([
                    'ukuran' => $detail['ukuran'],
                    'nama_product' => $detail['nama_product'],
                    'brand' => $detail['brand'],
                    'kw' => $detail['kw'],
                    'jumlah_box' => $detail['jumlah_box'],
                    'harga_satuan_box' => $detail['harga_satuan_box'],
                    'disc' => $detail['disc'] ?? 0,
                    'biaya_tambahan_ekspedisi' => $detail['biaya_tambahan_ekspedisi'] ?? 0,
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);

                $detailModel->calculateTotal();
                $suratPesananBarang->details()->save($detailModel);
                
                \Log::info('Detail saved', ['detail_id' => $detailModel->id]);
            }

            $suratPesananBarang->calculateTotal();
            \Log::info('Total calculated', ['total' => $suratPesananBarang->total_keseluruhan]);

            DB::commit();
            \Log::info('Transaction committed successfully');

            return redirect()->route('suratpesananbarang.index')
                ->with('success', 'Surat Pesanan Barang berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Transaction failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    // Add method to get products by ukuran
    public function getProductsByUkuran($ukuran)
    {
        $products = DaftarHarga::where('ukuran', $ukuran)
            ->orderBy('type', 'asc')
            ->distinct()
            ->pluck('type')
            ->filter()
            ->values();

        return response()->json($products);
    }

    // Add method to get brands by ukuran and product
    public function getBrandsByProduct($ukuran, $product)
    {
        $brands = DaftarHarga::where('ukuran', $ukuran)
            ->where('type', $product)
            ->select('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand')
            ->filter()
            ->values();

        return response()->json($brands);
    }

    public function getDefaultBrand($ukuran, $product)
    {
        $defaultBrand = DaftarHarga::where('ukuran', $ukuran)
            ->where('type', $product)
            ->orderBy('id', 'desc')
            ->value('brand');

        return response()->json(['default_brand' => $defaultBrand]);
    }

    // Add method to get KW by ukuran, product and brand
    public function getKwByBrand($ukuran, $product, $brand)
    {
        $kwList = DaftarHarga::where('ukuran', $ukuran)
            ->where('type', $product)
            ->where('brand', $brand)
            ->distinct()
            ->pluck('kw')
            ->filter()
            ->values();

        return response()->json($kwList);
    }

    // Add method to get harga by selection
    public function getHargaBySelection($ukuran, $product, $brand, $kw, $jenisHarga)
    {
        $harga = DaftarHarga::where('ukuran', $ukuran)
            ->where('type', $product)
            ->where('brand', $brand)
            ->where('kw', $kw)
            ->first();

        if ($harga) {
            $hargaValue = $jenisHarga === 'franco' ? $harga->harga_franco : $harga->harga_loco;
            return response()->json(['harga' => $hargaValue]);
        }

        return response()->json(['harga' => 0]);
    }

    public function show(SuratPesananBarang $suratpesananbarang)
    {
        $suratpesananbarang->load(['details', 'creator', 'approver']);
        return view('suratpesananbarang.show', compact('suratpesananbarang'));
    }

    public function edit(SuratPesananBarang $suratpesananbarang)
    {
        $user = auth()->user();

        // Get unique ukuran for dropdown
        $ukuranList = DaftarHarga::distinct()->pluck('ukuran')->filter();
        $daftarAgens = DaftarAgen::orderBy('nama_agen')->get();

        return view('suratpesananbarang.edit', compact('suratpesananbarang', 'ukuranList', 'daftarAgens'));
    }

    public function update(Request $request, SuratPesananBarang $suratpesananbarang)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'pengirim' => 'required|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'dikirim_ke' => 'required|string',
            'tanggal_kirim' => 'required|date',
            'pemesan' => 'nullable|string',
            'jenis_harga' => 'required|in:franco,loco',
            'nomor_do' => 'required|string|max:128',
            'total_jumlah_box' => 'required|numeric|min:0',
            'details' => 'required|array|min:1',
            'details.*.id' => 'nullable|exists:surat_pesanan_barang_details,id',
            'details.*.ukuran' => 'required|string|max:255',
            'details.*.nama_product' => 'required|string|max:255',
            'details.*.brand' => 'required|string|max:255',
            'details.*.kw' => 'required|string|max:255',
            'details.*.jumlah_box' => 'required|numeric|min:0',
            'details.*.harga_satuan_box' => 'required|numeric|min:0',
            'details.*.disc' => 'nullable|numeric|min:0',
            'details.*.biaya_tambahan_ekspedisi' => 'nullable|numeric|min:0',
            'details.*.keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $suratpesananbarang->update([
                'tanggal_kirim' => $validated['tanggal_kirim'],
                'pengirim' => $validated['pengirim'],
                'penerima' => $validated['penerima'],
                'dikirim_ke' => $validated['dikirim_ke'],
                'pemesan' => $validated['pemesan'],
                'jenis_harga' => $validated['jenis_harga'],
                'nomor_do' => $validated['nomor_do'],
                'total_jumlahbox' => $validated['total_jumlah_box'],
            ]);

            $existingDetailIds = [];

            foreach ($validated['details'] as $detail) {
                if (isset($detail['id'])) {
                    $detailModel = SuratPesananBarangDetail::find($detail['id']);
                    $detailModel->update([
                        'ukuran' => $detail['ukuran'],
                        'nama_product' => $detail['nama_product'],
                        'brand' => $detail['brand'],
                        'kw' => $detail['kw'],
                        'jumlah_box' => $detail['jumlah_box'],
                        'harga_satuan_box' => $detail['harga_satuan_box'],
                        'disc' => $detail['disc'] ?? 0,
                        'biaya_tambahan_ekspedisi' => $detail['biaya_tambahan_ekspedisi'] ?? 0,
                        'keterangan' => $detail['keterangan'] ?? null,
                    ]);
                    $detailModel->calculateTotal();
                    $detailModel->save();
                    $existingDetailIds[] = $detail['id'];
                } else {
                    $detailModel = new SuratPesananBarangDetail([
                        'ukuran' => $detail['ukuran'],
                        'nama_product' => $detail['nama_product'],
                        'brand' => $detail['brand'],
                        'kw' => $detail['kw'],
                        'jumlah_box' => $detail['jumlah_box'],
                        'harga_satuan_box' => $detail['harga_satuan_box'],
                        'disc' => $detail['disc'] ?? 0,
                        'biaya_tambahan_ekspedisi' => $detail['biaya_tambahan_ekspedisi'] ?? 0,
                        'keterangan' => $detail['keterangan'] ?? null,
                    ]);
                    $detailModel->calculateTotal();
                    $suratpesananbarang->details()->save($detailModel);
                    $existingDetailIds[] = $detailModel->id;
                }
            }

            $suratpesananbarang->details()->whereNotIn('id', $existingDetailIds)->delete();
            $suratpesananbarang->calculateTotal();

            DB::commit();

            return redirect()->route('suratpesananbarang.index')
                ->with('success', 'Surat Pesanan Barang berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(SuratPesananBarang $suratpesananbarang)
    {
        // if (auth()->user()->department !== 'SLS') {
        //     abort(403, 'Hanya Sales yang dapat menghapus surat pesanan barang');
        // }

        // if ($suratpesananbarang->created_by !== auth()->id()) {
        //     abort(403, 'Anda tidak memiliki akses untuk menghapus surat ini');
        // }

        if (in_array($suratpesananbarang->status, ['approved', 'rejected'])) {
            abort(403, 'Surat yang sudah diapprove/reject tidak dapat dihapus');
        }

        $suratpesananbarang->delete();

        return redirect()->route('suratpesananbarang.index')
            ->with('success', 'Surat Pesanan Barang berhasil dihapus');
    }

    public function submit(SuratPesananBarang $suratpesananbarang)
    {
        if (auth()->user()->department !== 'SLS') {
            abort(403, 'Hanya Sales yang dapat submit surat pesanan barang');
        }

        if ($suratpesananbarang->created_by !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk submit surat ini');
        }

        if ($suratpesananbarang->status !== 'draft') {
            abort(403, 'Hanya surat dengan status draft yang dapat disubmit');
        }

        $suratpesananbarang->update(['status' => 'pending']);

        return redirect()->route('suratpesananbarang.index')
            ->with('success', 'Surat Pesanan Barang berhasil disubmit untuk approval');
    }

    public function diketahui(SuratPesananBarang $suratpesananbarang)
    {
        
        $suratpesananbarang->update([
            'status' => 'diketahui',
            'diketahui_by' => auth()->id(),
            'diketahui_at' => now(),
        ]);

        return redirect()->route('suratpesananbarang.index')
            ->with('success', 'Surat Pesanan Barang berhasil diapprove');
    }

    public function approve(SuratPesananBarang $suratpesananbarang)
    {
        if (auth()->user()->department !== 'FNC') {
            abort(403, 'Hanya Finance yang dapat approve surat pesanan barang');
        }

        // if ($suratpesananbarang->status !== 'pending') {
        //     abort(403, 'Hanya surat dengan status pending yang dapat diapprove');
        // }

        $suratpesananbarang->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('suratpesananbarang.index')
            ->with('success', 'Surat Pesanan Barang berhasil diapprove');
    }

    public function reject(Request $request, SuratPesananBarang $suratpesananbarang)
    {
        if (auth()->user()->department !== 'FNC') {
            abort(403, 'Hanya Finance yang dapat reject surat pesanan barang');
        }

        if ($suratpesananbarang->status !== 'pending') {
            abort(403, 'Hanya surat dengan status pending yang dapat direject');
        }

        // $validated = $request->validate([
        //     'pemesan' => 'required|string',
        // ]);

        $suratpesananbarang->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            // 'pemesan' => $validated['pemesan'],
        ]);

        return redirect()->route('suratpesananbarang.index')
            ->with('success', 'Surat Pesanan Barang berhasil direject');
    }

    public function cancelApprovalSLS($id)
    {
        $spb = SuratPesananBarang::findOrFail($id);
        
        // Hanya SLS SUPER yang bisa batalkan approval SLS
        if (auth()->user()->department !== 'SLS SUPER') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk membatalkan approval SLS.');
        }
        
        // Hanya bisa batalkan jika statusnya 'diketahui'
        if ($spb->status !== 'diketahui') {
            return redirect()->back()->with('error', 'Hanya bisa membatalkan approval SLS untuk status "Approved by SLS".');
        }
        
        $spb->status = 'pending';
        $spb->save();
        
        return redirect()->back()->with('success', 'Approval SLS berhasil dibatalkan, status kembali ke Pending.');
    }

    public function cancelApprovalFNC($id)
    {
        $spb = SuratPesananBarang::findOrFail($id);
        
        // Hanya FNC yang bisa batalkan approval FNC
        if (auth()->user()->department !== 'FNC') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk membatalkan approval FNC.');
        }
        
        // Hanya bisa batalkan jika statusnya 'approved'
        if ($spb->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya bisa membatalkan approval FNC untuk status "Approved by FNC".');
        }
        
        $spb->status = 'diketahui';
        $spb->save();
        
        return redirect()->back()->with('success', 'Approval FNC berhasil dibatalkan, status kembali ke Approved by SLS.');
    }


    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        
        // // Query dasar dengan eager loading details
        // if ($user->department === 'SLS') {
        //     $query = SuratPesananBarang::where('created_by', $user->id)
        //         ->with(['details', 'approver', 'creator']);
        // } else {
        //     $query = SuratPesananBarang::whereIn('status', ['pending', 'approved', 'rejected'])
        //         ->with(['details', 'creator', 'approver']);
        // }

        $query = SuratPesananBarang::whereIn('status', ['pending', 'approved', 'rejected', 'diketahui'])
                ->with(['details', 'creator', 'approver']);

        // Apply filters sama seperti index
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                ->orWhere('pengirim', 'like', "%{$search}%")
                ->orWhere('penerima', 'like', "%{$search}%")
                ->orWhere('pemesan', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return Excel::download(new SuratPesananBarangExport($data), 'surat-pesanan-barang-' . date('Y-m-d') . '.xlsx');
    }

    public function exportPDF($id)
    {
        $suratpesananbarang = SuratPesananBarang::with(['details', 'creator', 'approver'])->findOrFail($id);
        
        // Check authorizationS
        // if (auth()->user()->department === 'SLS' && $suratpesananbarang->created_by != auth()->id()) {
        //     abort(403, 'Unauthorized access');
        // }

        $pdf = PDF::loadView('suratpesananbarang.pdf', compact('suratpesananbarang'));
        
        return $pdf->download('surat-pesanan-' . $suratpesananbarang->nomor_surat . '.pdf');
    }
}