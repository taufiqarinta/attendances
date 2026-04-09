<?php

namespace App\Http\Controllers;

use App\Models\DaftarHarga;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Exports\DaftarHargaExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DaftarHargaImport;

class DaftarHargaController extends Controller
{
    // public function index(Request $request)
    // {
    //     $filters = $request->only(['search', 'kategori', 'brand']);

    //     $daftarHargas = DaftarHarga::when($request->search, function ($query, $search) {
    //         return $query->where('type', 'like', "%{$search}%")
    //                     ->orWhere('brand', 'like', "%{$search}%")
    //                     ->orWhere('kategori', 'like', "%{$search}%");
    //     })
    //     // ->when($request->kategori, function ($query, $kategori) {
    //     //     return $query->where('kategori', $kategori);
    //     // })
    //     // ->when($request->brand, function ($query, $brand) {
    //     //     return $query->where('brand', $brand);
    //     // })
    //     ->orderBy('id', 'desc')
    //     ->paginate(10);

    //     return view('daftar-harga.index', compact('daftarHargas', 'filters'));
    // }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'kategori', 'brand']);

        $daftarHargas = DaftarHarga::when($request->search, function ($query, $search) {
                return $query->where('type', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%")
                            ->orWhere('kategori', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Tambahkan parameter filters ke pagination links
        $daftarHargas->appends($filters);

        return view('daftar-harga.index', compact('daftarHargas', 'filters'));
    }

    public function create()
    {
        return view('daftar-harga.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'kw' => 'required|string|max:50',
            'brand' => 'required|string|max:100',
            'ukuran' => 'required|string|max:50',
            'karton' => 'required|string|max:100',
            'kategori' => 'required|string|max:100',
            'kel_harga_miss2' => 'required|string|max:100',
            'harga_franco' => 'required|numeric|min:0',
            'harga_loco' => 'required|numeric|min:0',
        ]);

        // // Convert to uppercase
        // $validated = array_map(function ($value) {
        //     return is_string($value) ? Str::upper($value) : $value;
        // }, $validated);

        DaftarHarga::create($validated);

        return redirect()->route('daftarharga.index')
                        ->with('success', 'Data daftar harga berhasil ditambahkan.');
    }

    public function show($id)
    {
        $daftarHarga = DaftarHarga::findOrFail($id);
        return view('daftar-harga.show', compact('daftarHarga'));
    }

    public function edit($id)
    {
        $daftarHarga = DaftarHarga::findOrFail($id);
        return view('daftar-harga.edit', compact('daftarHarga'));
    }

    public function update(Request $request, $id)
    {
        $daftarHarga = DaftarHarga::findOrFail($id);
        
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'kw' => 'required|string|max:50',
            'brand' => 'required|string|max:100',
            'ukuran' => 'required|string|max:50',
            'karton' => 'required|string|max:100',
            'kategori' => 'required|string|max:100',
            'kel_harga_miss2' => 'required|string|max:100',
            'harga_franco' => 'required|numeric|min:0',
            'harga_loco' => 'required|numeric|min:0',
        ]);

        // // Convert to uppercase
        // $validated = array_map(function ($value) {
        //     return is_string($value) ? Str::upper($value) : $value;
        // }, $validated);

        $daftarHarga->update($validated);

        return redirect()->route('daftarharga.index')
                        ->with('success', 'Data daftar harga berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $daftarHarga = DaftarHarga::findOrFail($id);
        $daftarHarga->delete();

        return redirect()->route('daftarharga.index')
                        ->with('success', 'Data daftar harga berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['search', 'kategori', 'brand']);
        
        $filename = 'daftar-harga-' . date('Y-m-d-H-i-s') . '.xlsx';
        
        return Excel::download(new DaftarHargaExport($filters), $filename);
    }

    public function importForm()
    {
        return view('daftar-harga.import-form');
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240' // 10MB max
        ]);

        try {
            $import = new DaftarHargaImport();
            $data = Excel::toArray($import, $request->file('file'));
            
            // Simpan data sementara di session untuk preview
            $request->session()->put('import_data', $data[0]);
            $request->session()->put('file_path', $request->file('file')->store('temp'));

            return view('daftar-harga.import-preview', [
                'previewData' => array_slice($data[0], 0, 10), // Preview 10 data pertama
                'totalRows' => count($data[0])
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error membaca file: ' . $e->getMessage());
        }
    }

    public function importProcess(Request $request)
    {
        if (!$request->session()->has('import_data') || !$request->session()->has('file_path')) {
            return redirect()->route('daftarharga.import-form')->with('error', 'Data import tidak ditemukan. Silakan upload ulang.');
        }

        try {
            // Truncate table
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            \DB::table('daftar_hargas')->truncate();
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Import data dari file
            $filePath = $request->session()->get('file_path');
            Excel::import(new DaftarHargaImport(), $filePath);

            // Hapus session data
            $request->session()->forget(['import_data', 'file_path']);

            // Hapus file temporary
            \Storage::delete($filePath);

            return redirect()->route('daftarharga.index')->with('success', 'Data berhasil diimport!');

        } catch (\Exception $e) {
            return redirect()->route('daftarharga.import-form')->with('error', 'Error import data: ' . $e->getMessage());
        }
    }
}