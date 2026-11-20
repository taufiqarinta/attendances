<?php

namespace App\Http\Controllers\Orientation;

use App\Http\Controllers\Controller;
use App\Models\Orientation\MasterOrientationCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MasterOrientationCategoryController extends Controller
{
    /**
     * Display a listing of the resource with server-side pagination.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');

        $query = MasterOrientationCategory::query();

        // Filter by search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('category_name', 'LIKE', "%{$search}%")
                  ->orWhere('code_category', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $query->orderBy('created_at', 'desc');

        $categories = $query->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'data' => $categories->items(),
                'pagination' => [
                    'total' => $categories->total(),
                    'per_page' => $categories->perPage(),
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'from' => $categories->firstItem(),
                    'to' => $categories->lastItem(),
                ],
                'links' => (string) $categories->links()
            ]);
        }

        return view('category-orientation.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:150',
            'status' => 'required|boolean',
        ]);

        $category = MasterOrientationCategory::create([
            'code_category' => $this->nextCategoryCode(),
            'category_name' => $request->category_name,
            'status' => $request->status,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan!',
                'data' => $category
            ]);
        }

        return redirect()
            ->route('master-category.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = MasterOrientationCategory::findOrFail($id);

        return response()->json([
            'id' => $category->id,
            'category_name' => $category->category_name,
            'status' => $category->status,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:150',
            'status' => 'required|boolean',
        ]);

        $category = MasterOrientationCategory::findOrFail($id);

        $category->update([
            'category_name' => $request->category_name,
            'status' => $request->status,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui!',
                'data' => $category
            ]);
        }

        return redirect()
            ->route('master-category.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = MasterOrientationCategory::findOrFail($id);
        $category->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus!'
            ]);
        }

        return redirect()
            ->route('master-category.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }

    /**
     * Membuat kode kategori berformat CAT01, CAT02, dan seterusnya.
     */
    private function nextCategoryCode(): string
    {
        $lastCode = MasterOrientationCategory::query()
            ->where('code_category', 'REGEXP', '^CAT[0-9]{2}$')
            ->lockForUpdate()
            ->orderByRaw('CAST(RIGHT(code_category, 2) AS UNSIGNED) DESC')
            ->value('code_category');

        $nextNumber = $lastCode ? ((int) substr($lastCode, -2)) + 1 : 1;

        if ($nextNumber > 99) {
            throw ValidationException::withMessages([
                'code_category' => 'Batas kode kategori CAT99 telah tercapai.',
            ]);
        }

        return 'CAT' . str_pad((string) $nextNumber, 2, '0', STR_PAD_LEFT);
    }
}