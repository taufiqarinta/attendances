<?php

namespace App\Http\Controllers\Orientation;

use App\Http\Controllers\Controller;
use App\Models\Orientation\MasterOrientationActivity;
use App\Models\Orientation\MasterPlant;
use Illuminate\Http\Request;

class MasterOrientationActivityController extends Controller
{
    /**
     * Display a listing of the resource with server-side pagination.
     */
    public function index(Request $request)
    {
        $plants = MasterPlant::orderBy('name_plant')->get();
        
        // Server-side pagination
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');
        
        $query = MasterOrientationActivity::query();
        
        // Filter by search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('activity_name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
        
        // Order by
        $query->orderBy('created_at', 'desc');
        
        // Paginate
        $activities = $query->paginate($perPage);
        
        // Jika request AJAX, return JSON
        if ($request->ajax()) {
            return response()->json([
                'data' => $activities->items(),
                'pagination' => [
                    'total' => $activities->total(),
                    'per_page' => $activities->perPage(),
                    'current_page' => $activities->currentPage(),
                    'last_page' => $activities->lastPage(),
                    'from' => $activities->firstItem(),
                    'to' => $activities->lastItem(),
                ],
                'links' => (string) $activities->links()
            ]);
        }
        
        return view('activity-orientation.index', compact('activities', 'plants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'activity_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'plant_ids' => 'required|string',
            'status' => 'required|boolean'
        ]);

        $plantIds = explode(',', $request->plant_ids);
        $plantIds = array_map('intval', $plantIds);
        
        $activity = MasterOrientationActivity::create([
            'activity_name' => $request->activity_name,
            'description' => $request->description,
            'plants' => $plantIds,
            'status' => $request->status
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil ditambahkan!',
                'data' => $activity
            ]);
        }

        return redirect()
            ->route('orientation.master-activity.index')
            ->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $activity = MasterOrientationActivity::findOrFail($id);
        
        return response()->json([
            'id' => $activity->id,
            'activity_name' => $activity->activity_name,
            'description' => $activity->description,
            'status' => $activity->status,
            'plants' => $activity->plants ?? []
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'activity_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'plant_ids' => 'required|string',
            'status' => 'required|boolean'
        ]);

        $activity = MasterOrientationActivity::findOrFail($id);
        
        $plantIds = explode(',', $request->plant_ids);
        $plantIds = array_map('intval', $plantIds);
        
        $activity->update([
            'activity_name' => $request->activity_name,
            'description' => $request->description,
            'plants' => $plantIds,
            'status' => $request->status
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil diperbarui!',
                'data' => $activity
            ]);
        }

        return redirect()
            ->route('orientation.master-activity.index')
            ->with('success', 'Kegiatan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $activity = MasterOrientationActivity::findOrFail($id);
        $activity->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil dihapus!'
            ]);
        }

        return redirect()
            ->route('orientation.master-activity.index')
            ->with('success', 'Kegiatan berhasil dihapus!');
    }
}