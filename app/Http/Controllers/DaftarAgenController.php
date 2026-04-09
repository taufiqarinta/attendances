<?php

namespace App\Http\Controllers;

use App\Models\DaftarAgen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DaftarAgenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->search ?? ''
        ];

        $query = DaftarAgen::query();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama_agen', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('nama_sales', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('npwp', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('alamat', 'like', '%' . $filters['search'] . '%');
            });
        }

        $daftarAgens = $query->latest()->paginate(10);

        $daftarAgens->appends($filters);

        return view('daftar-agen.index', compact('daftarAgens', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('daftar-agen.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'npwp' => 'nullable|string|max:255',
            'nama_agen' => 'required|string|max:255',
            'nama_sales' => 'nullable|string|max:255',
            'alamat' => 'nullable|string'
        ]);

        try {
            DaftarAgen::create($request->all());
            
            return redirect()->route('daftar-agen.index')
                ->with('success', 'Data agen berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan data agen.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DaftarAgen $daftarAgen)
    {
        return view('daftar-agen.show', compact('daftarAgen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DaftarAgen $daftarAgen)
    {
        return view('daftar-agen.edit', compact('daftarAgen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DaftarAgen $daftarAgen)
    {
        $request->validate([
            'npwp' => 'nullable|string|max:255',
            'nama_agen' => 'required|string|max:255',
            'nama_sales' => 'nullable|string|max:255',
            'alamat' => 'nullable|string'
        ]);

        try {
            $daftarAgen->update($request->all());
            
            return redirect()->route('daftar-agen.index')
                ->with('success', 'Data agen berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data agen.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DaftarAgen $daftarAgen)
    {
        try {
            $daftarAgen->delete();
            
            return redirect()->route('daftar-agen.index')
                ->with('success', 'Data agen berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data agen.');
        }
    }
}