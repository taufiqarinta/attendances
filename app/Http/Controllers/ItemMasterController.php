<?php

namespace App\Http\Controllers;

use App\Models\ItemMaster;
use App\Models\Merk;
use Illuminate\Http\Request;

class ItemMasterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = auth()->user();
        $idCust = $user->id_customer;
        
        // Ambil code_item dari merk yang dimiliki user
        if ($user->role_as == 1) {
            // Admin: bisa lihat semua item (tidak perlu filter)
            $items = ItemMaster::when($search, function($query, $search) {
                    return $query->where('item_code', 'like', '%' . $search . '%')
                            ->orWhere('item_name', 'like', '%' . $search . '%')
                            ->orWhere('ukuran', 'like', '%' . $search . '%');
                })
                ->orderBy('id', 'asc')
                ->paginate(10);
        } else {
            // Role lain: hanya merk yang sesuai user
            $merkCodes = Merk::select('merks.code_item')
                ->join('users_merks', 'users_merks.id_merks', '=', 'merks.id')
                ->where('users_merks.id_customer', $idCust)
                ->pluck('code_item')
                ->toArray();
            
            // Jika user tidak memiliki akses merk sama sekali
            if (empty($merkCodes)) {
                $items = ItemMaster::where('id', 0)->paginate(10);
            } else {
                $items = ItemMaster::when($search, function($query, $search) {
                        return $query->where('item_code', 'like', '%' . $search . '%')
                                ->orWhere('item_name', 'like', '%' . $search . '%')
                                ->orWhere('ukuran', 'like', '%' . $search . '%');
                    })
                    ->where(function($query) use ($merkCodes) {
                        // Buat kondisi WHERE untuk setiap code_item
                        $query->where(function($subQuery) use ($merkCodes) {
                            foreach ($merkCodes as $index => $merkCode) {
                                if ($index === 0) {
                                    // Kondisi pertama
                                    $subQuery->whereRaw("SUBSTRING(item_code, 2, 1) = ?", [$merkCode]);
                                } else {
                                    // Kondisi OR untuk code_item lainnya
                                    $subQuery->orWhereRaw("SUBSTRING(item_code, 2, 1) = ?", [$merkCode]);
                                }
                            }
                        });
                    })
                    ->orderBy('id', 'asc')
                    ->paginate(10);
            }
        }
        
        // Simpan filter untuk view
        $filters = [
            'search' => $search,
        ];
        
        return view('itemmaster.index', compact('items', 'filters'));
    }

    public function create()
    {
        return view('itemmaster.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:50|unique:item_master',
            'item_name' => 'required|string|max:255',
            'ukuran' => 'nullable|string|max:50',
        ]);

        ItemMaster::create($validated);

        return redirect()->route('itemmaster.index')
            ->with('success', 'Item berhasil ditambahkan.');
    }

    public function show($id)
    {
        $itemMaster = ItemMaster::findOrFail($id);
        return view('itemmaster.show', compact('itemMaster'));
    }

    public function edit($id) 
    {
        $itemMaster = ItemMaster::findOrFail($id);
        return view('itemmaster.edit', compact('itemMaster'));
    }

    public function update(Request $request, $id) 
    {
        $itemMaster = ItemMaster::findOrFail($id); 
        
        $validated = $request->validate([
            'item_code' => 'required|string|max:50|unique:item_master,item_code,' . $itemMaster->id,
            'item_name' => 'required|string|max:255',
            'ukuran' => 'nullable|string|max:50',
        ]);

        $itemMaster->update($validated);

        return redirect()->route('itemmaster.index')
            ->with('success', 'Item berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $itemMaster = ItemMaster::findOrFail($id); 
        $itemMaster->delete();

        return redirect()->route('itemmaster.index')
            ->with('success', 'Item berhasil dihapus.');
    }
}