<?php

namespace App\Http\Controllers;

use App\Models\ItemMasterTambahan;
use Illuminate\Http\Request;

class ItemMasterTambahanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $items = ItemMasterTambahan::when($search, function($query, $search) {
                return $query->where('item_code', 'like', '%' . $search . '%')
                           ->orWhere('item_name', 'like', '%' . $search . '%')
                           ->orWhere('ukuran', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
            
        // Simpan filter untuk view
        $filters = [
            'search' => $search,
        ];
        
        return view('itemmaster-tambahan.index', compact('items', 'filters'));
    }

    public function create()
    {
        // Generate item_code otomatis
        $lastItem = ItemMasterTambahan::orderBy('item_code', 'desc')->first();
        $nextNumber = 1;
        
        if ($lastItem && preg_match('/^ITM(\d+)$/', $lastItem->item_code, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }
        
        $itemCode = 'ITM' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        return view('itemmaster-tambahan.create', compact('itemCode'));
    }

    public function store(Request $request)
    {
        // Generate item_code otomatis
        $lastItem = ItemMasterTambahan::orderBy('item_code', 'desc')->first();
        $nextNumber = 1;
        
        if ($lastItem && preg_match('/^ITM(\d+)$/', $lastItem->item_code, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }
        
        $itemCode = 'ITM' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'ukuran' => 'nullable|string|max:50',
        ]);

        // Tambahkan item_code yang di-generate
        $validated['item_code'] = $itemCode;

        ItemMasterTambahan::create($validated);

        return redirect()->route('itemmaster-tambahan.index')
            ->with('success', 'Item berhasil ditambahkan dengan kode: ' . $itemCode);
    }

    public function show($id)
    {
        $itemMaster = ItemMasterTambahan::findOrFail($id);
        return view('itemmaster-tambahan.show', compact('itemMaster'));
    }

    public function edit($id) 
    {
        $itemMaster = ItemMasterTambahan::findOrFail($id);
        return view('itemmaster-tambahan.edit', compact('itemMaster'));
    }

    public function update(Request $request, $id) 
    {
        $itemMaster = ItemMasterTambahan::findOrFail($id); 
        
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'ukuran' => 'nullable|string|max:50',
        ]);

        $validated['item_code'] = $itemMaster->item_code;

        $itemMaster->update($validated);

        return redirect()->route('itemmaster-tambahan.index')
            ->with('success', 'Item berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = ItemMasterTambahan::findOrFail($id);
        
        // Nonaktifkan item terlebih dahulu
        $item->update(['status' => 0]);
        
        return redirect()->route('itemmaster-tambahan.index')
            ->with('success', 'Item berhasil dinonaktifkan.');
    }

    /**
     * Nonaktifkan item
     */
    public function deactivate($id)
    {
        $item = ItemMasterTambahan::findOrFail($id);
        
        // Cek apakah item sudah nonaktif
        if ($item->isInactive()) {
            return redirect()->back()
                ->with('warning', 'Item sudah dalam status nonaktif.');
        }
        
        $item->update(['status' => 0]);
        
        return redirect()->route('itemmaster-tambahan.index')
            ->with('success', 'Item berhasil dinonaktifkan.');
    }

    /**
     * Aktifkan item
     */
    public function activate($id)
    {
        $item = ItemMasterTambahan::findOrFail($id);
        
        // Cek apakah item sudah aktif
        if ($item->isActive()) {
            return redirect()->back()
                ->with('warning', 'Item sudah dalam status aktif.');
        }
        
        $item->update(['status' => 1]);
        
        return redirect()->route('itemmaster-tambahan.index')
            ->with('success', 'Item berhasil diaktifkan.');
    }
}