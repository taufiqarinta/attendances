<?php

namespace App\Http\Controllers\Orientation;

use App\Http\Controllers\Controller;
use App\Models\Orientation\MasterOrientationActivity;
use App\Models\Orientation\MasterOrientationCategory; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MasterOrientationActivityController extends Controller
{
    public function index(Request $request)
    {
        $categories = MasterOrientationCategory::orderBy('category_name')->get();

        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');

        $query = MasterOrientationActivity::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('activity_name', 'LIKE', "%{$search}%")
                    ->orWhere('code_activity', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $query->orderBy('created_at', 'desc');

        $activities = $query->paginate($perPage);

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

        return view('activity-orientation.index', compact('activities', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:dev_test.master_orientation_categories,id',
            'activity_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'required|boolean'
        ]);

        $activity = DB::connection('dev_test')->transaction(function () use ($request) {
            return MasterOrientationActivity::create([
                'category_id' => $request->category_id,
                'code_activity' => $this->nextActivityCode(),
                'activity_name' => $request->activity_name,
                'description' => $request->description,
                'plants' => '-', // default
                'status' => $request->status,
            ]);
        });

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

    public function edit($id)
    {
        $activity = MasterOrientationActivity::findOrFail($id);

        return response()->json([
            'id' => $activity->id,
            'category_id' => $activity->category_id,
            'activity_name' => $activity->activity_name,
            'description' => $activity->description,
            'status' => $activity->status,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:dev_test.master_orientation_categories,id',
            'activity_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'required|boolean'
        ]);

        $activity = MasterOrientationActivity::findOrFail($id);

        $activity->update([
            'category_id' => $request->category_id,
            'activity_name' => $request->activity_name,
            'description' => $request->description,
            'plants' => '-', // default
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

    private function nextActivityCode(): string
    {
        $lastCode = MasterOrientationActivity::query()
            ->where('code_activity', 'REGEXP', '^ACT[0-9]{2}$')
            ->lockForUpdate()
            ->orderByRaw('CAST(RIGHT(code_activity, 2) AS UNSIGNED) DESC')
            ->value('code_activity');

        $nextNumber = $lastCode ? ((int) substr($lastCode, -2)) + 1 : 1;

        if ($nextNumber > 99) {
            throw ValidationException::withMessages([
                'code_activity' => 'Batas kode kegiatan ACT99 telah tercapai.',
            ]);
        }

        return 'ACT' . str_pad((string) $nextNumber, 2, '0', STR_PAD_LEFT);
    }
}