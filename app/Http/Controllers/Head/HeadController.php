<?php

namespace App\Http\Controllers\Head;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HeadController extends Controller
{
    public function index(Request $request)
    {
        dd("Masuk bosq");

        $user = Auth::user();
        $search = $request->input('search', '');

        // $query = SubProject::query();

        // if ($search) {
        //     $query->where('nama_pt', 'like', "%{$search}%")
        //         ->orWhere('nama_sub_project', 'like', "%{$search}%");
        // }

        // $subProjects = $query->paginate(10);

        // // Jika request dari AJAX, kirim hanya partial table (tanpa layout)
        // if ($request->ajax()) {
        //     return view('partials.subproject-table', compact('subProjects'))->render();
        // }

        session()->flash('status', 'Selamat datang, ' . $user->name);
        return view('dashboard', compact('user', 'search'));
    }

    public function indexUpload(Request $request)
    {
        $user = Auth::user();
        $userLocationId = $user->location_id;

        $query = File::with(['location', 'user'])->where('location_id', $userLocationId);

        if ($request->has('tanggal') && $request->tanggal != '') {
            $query->whereDate('created_at', $request->tanggal);
        }

        $files = $query->orderBy('created_at', 'desc')->paginate(10);

        $files->appends($request->only('tanggal'));

        $locations = Location::find($userLocationId);

        return view('head.upload.index', compact('locations', 'files'));
    }

    public function storeUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,pdf',
        ]);

        $user = auth()->user();

        $locationId = $user->location_id;
        $location = Location::findOrFail($locationId);

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $folderName = $location->name;

        $relativePath = "uploads/{$folderName}/{$originalName}";
        $uploadedFile->storeAs("public", $relativePath);

        File::create([
            'nama_file' => $originalName,
            'user_id' => $user->id,
            'location_id' => $locationId,
            'path' => $relativePath,
        ]);

        return back()->with('success', 'File berhasil diupload');
    }

    public function deleteUpload($id)
    {
        $file = File::findOrFail($id);

        // Gunakan disk 'public' untuk mengakses file
        if ($file->path && Storage::disk('public')->exists($file->path)) {
            Storage::disk('public')->delete($file->path);
        }

        // Hapus dari database
        $file->delete();

        return redirect()->route('head.upload')->with('success', 'File berhasil dihapus');
    }
}
