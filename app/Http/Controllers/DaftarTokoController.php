<?php

namespace App\Http\Controllers;

use App\Models\DaftarToko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaftarTokoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    

    public function index(Request $request)
    {
        $query = DaftarToko::query();
        
        if (Auth::user()->role_as != 1) {
            $query->where('kode_spg', Auth::user()->id_customer);
        }
        
        // Filter search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_toko', 'like', "%{$search}%")
                ->orWhere('kota', 'like', "%{$search}%")
                ->orWhere('nama_spg', 'like', "%{$search}%")
                ->orWhere('kode_spg', 'like', "%{$search}%");
            });
        }
        
        // Order by
        $query->orderBy('nama_toko');
        
        // Get results with pagination
        $tokoList = $query->paginate(20)->withQueryString();
        
        return view('daftar-toko.index', compact('tokoList'));
    }

    
    public function create()
    {
        // Ambil data SPG dari tabel users
        $spgList = User::whereNotNull('id_customer')
            ->orderBy('name')
            ->get(['id', 'id_customer', 'name']);
            
        return view('daftar-toko.create', compact('spgList'));
    }

   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_spg' => 'required|string|max:255',
            'nama_spg' => 'required|string|max:255',
            'nama_toko' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
        ]);
        
        $validated['divisi'] = 'SPG';
        $validated['status'] = 1;
        
        try {
            DaftarToko::create($validated);
            
            return redirect()->route('daftar-toko.index')
                ->with('success', 'Data toko berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    
    public function show(DaftarToko $daftar_toko)
    {
        // Check authorization - admin bisa melihat semua, SPG hanya toko mereka
        if (Auth::user()->role_as != 1 && $daftar_toko->kode_spg != Auth::user()->id_customer) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('daftar-toko.show', compact('daftar_toko'));
    }

    public function edit(DaftarToko $daftar_toko)
    {
        // Check authorization
        if (Auth::user()->role_as != 1 && $daftar_toko->kode_spg != Auth::user()->id_customer) {
            abort(403, 'Unauthorized action.');
        }
        
        // Ambil data SPG dari tabel users
        $spgList = User::whereNotNull('id_customer')
            ->orderBy('name')
            ->get(['id', 'id_customer', 'name']);
            
        return view('daftar-toko.edit', compact('daftar_toko', 'spgList'));
    }

    
    public function update(Request $request, DaftarToko $daftar_toko)
    {
        // Check authorization
        if (Auth::user()->role_as != 1 && $daftar_toko->kode_spg != Auth::user()->id_customer) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'kode_spg' => 'required|string|max:255',
            'nama_spg' => 'required|string|max:255',
            'nama_toko' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);
        
        // Set divisi otomatis ke "SPG"
        $validated['divisi'] = 'SPG';
        
        try {
            $daftar_toko->update($validated);
            
            return redirect()->route('daftar-toko.index')
                ->with('success', 'Data toko berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function activate(DaftarToko $daftar_toko)
    {
        if (Auth::user()->role_as != 1) {
            abort(403, 'Unauthorized action.');
        }
        
        // Cek apakah toko sudah aktif
        if ($daftar_toko->isActive()) {
            return redirect()->back()
                ->with('warning', 'Toko sudah dalam status aktif.');
        }
        
        try {
            $daftar_toko->update(['status' => 1]);
            
            return redirect()->route('daftar-toko.index')
                ->with('success', 'Toko berhasil diaktifkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deactivate(DaftarToko $daftar_toko)
    {
        if (Auth::user()->role_as != 1) {
            abort(403, 'Unauthorized action.');
        }
        
        // Cek apakah toko sudah nonaktif
        if ($daftar_toko->isInactive()) {
            return redirect()->back()
                ->with('warning', 'Toko sudah dalam status nonaktif.');
        }
        
        try {
            $daftar_toko->update(['status' => 0]);
            
            return redirect()->route('daftar-toko.index')
                ->with('success', 'Toko berhasil dinonaktifkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    
    // public function destroy(DaftarToko $daftar_toko)
    // {
    //     // Check authorization - hanya admin yang bisa menghapus
    //     if (Auth::user()->role_as != 1) {
    //         abort(403, 'Unauthorized action.');
    //     }
        
    //     try {
    //         $daftar_toko->delete();
            
    //         return redirect()->route('daftar-toko.index')
    //             ->with('success', 'Data toko berhasil dihapus.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }
    
    public function getSpgByKode(Request $request)
    {
        $kodeSpg = $request->input('kode_spg');
        
        $spg = User::where('id_customer', $kodeSpg)
            ->first(['id', 'name', 'id_customer']);
            
        if ($spg) {
            return response()->json([
                'success' => true,
                'nama_spg' => $spg->name
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'SPG tidak ditemukan'
        ]);
    }
}