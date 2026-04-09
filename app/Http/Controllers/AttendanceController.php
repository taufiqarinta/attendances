<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    // Method untuk menampilkan form absensi (sudah ada)
    // public function index()
    // {
    //     return view('absensi.index');
    // }

    // Method untuk menyimpan foto dari backend
    public function savePhotoFromBackend(Request $request)
    {
        try {
            $request->validate([
                'photoData' => 'required',
                'PersonnelNo' => 'required',
                'datetime' => 'required'
            ]);

            $photoData = $request->photoData;
            $personnelNo = $request->PersonnelNo;
            $datetime = $request->datetime;

            // Decode base64
            $base64Str = preg_replace('#^data:image/\w+;base64,#i', '', $photoData);
            $imageData = base64_decode($base64Str);

            if ($imageData === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal decode gambar'
                ], 400);
            }

            // Generate path
            $date = \Carbon\Carbon::parse($datetime);
            $year = $date->format('Y');
            $month = $date->format('m');
            $fileName = "attendance_{$personnelNo}_{$date->format('Ymd_His')}.jpg";
            $filePath = "attendances/{$year}/{$month}/{$fileName}";

            // Simpan ke storage Laravel (public disk)
            Storage::disk('public')->put($filePath, $imageData);

            // Buat symlink dulu: php artisan storage:link
            $photoUrl = Storage::url($filePath);

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil disimpan',
                'data' => [
                    'file_path' => $filePath,
                    'photo_url' => $photoUrl
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk mengakses foto
    public function getPhoto($path)
    {
        $path = str_replace(['..', './', '../'], '', $path);
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    }
}