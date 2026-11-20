<?php

namespace App\Http\Controllers\Orientation;

use App\Http\Controllers\Controller;
use App\Models\Orientation\MasterReaksiEvaluasi;
use Illuminate\Http\Request;

class MasterReaksiEvaluasiController extends Controller
{
    /**
     * Display a listing of the resource with server-side pagination.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');

        $query = MasterReaksiEvaluasi::query();

        // Filter by search
        if ($search) {
            $query->where('reaksi_name', 'LIKE', "%{$search}%");
        }

        // Filter by status
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $query->orderBy('created_at', 'desc');

        $reaksi = $query->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'data' => $reaksi->items(),
                'pagination' => [
                    'total' => $reaksi->total(),
                    'per_page' => $reaksi->perPage(),
                    'current_page' => $reaksi->currentPage(),
                    'last_page' => $reaksi->lastPage(),
                    'from' => $reaksi->firstItem(),
                    'to' => $reaksi->lastItem(),
                ],
                'links' => (string) $reaksi->links()
            ]);
        }

        return view('reaksi-evaluasi.index', compact('reaksi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'reaksi_name' => 'required|string|max:150',
            'status' => 'required|boolean',
        ]);

        $reaksi = MasterReaksiEvaluasi::create([
            'reaksi_name' => $request->reaksi_name,
            'status' => $request->status,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reaksi evaluasi berhasil ditambahkan!',
                'data' => $reaksi
            ]);
        }

        return redirect()
            ->route('master-reaksi-evaluasi.index')
            ->with('success', 'Reaksi evaluasi berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $reaksi = MasterReaksiEvaluasi::findOrFail($id);

        return response()->json([
            'id' => $reaksi->id,
            'reaksi_name' => $reaksi->reaksi_name,
            'status' => $reaksi->status,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'reaksi_name' => 'required|string|max:150',
            'status' => 'required|boolean',
        ]);

        $reaksi = MasterReaksiEvaluasi::findOrFail($id);

        $reaksi->update([
            'reaksi_name' => $request->reaksi_name,
            'status' => $request->status,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reaksi evaluasi berhasil diperbarui!',
                'data' => $reaksi
            ]);
        }

        return redirect()
            ->route('master-reaksi-evaluasi.index')
            ->with('success', 'Reaksi evaluasi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $reaksi = MasterReaksiEvaluasi::findOrFail($id);
        $reaksi->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reaksi evaluasi berhasil dihapus!'
            ]);
        }

        return redirect()
            ->route('master-reaksi-evaluasi.index')
            ->with('success', 'Reaksi evaluasi berhasil dihapus!');
    }
}