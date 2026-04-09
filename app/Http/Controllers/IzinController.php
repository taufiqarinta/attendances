<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class IzinController extends Controller
{
    public function index()
    {
        return view('izin.index');
    }

    public function uploadFile(Request $request)
    {
        // Log awal request
        Log::info('=== UPLOAD FILE START ===');
        Log::info('Request data:', [
            'all_data' => $request->all(),
            'has_file' => $request->hasFile('file'),
            'file_original' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : 'no file',
            'headers' => $request->headers->all()
        ]);

        try {
            // Validasi request
            Log::info('Validating request...');
            
            $validator = \Validator::make($request->all(), [
                'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'nik' => 'required',
                'tipe' => 'required|in:1200,1300',
                'startdate' => 'required',
                'enddate' => 'required'
            ]);

            if ($validator->fails()) {
                Log::error('Validasi gagal:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            Log::info('Validasi berhasil');

            // Ambil data
            $file = $request->file('file');
            $nik = $request->nik;
            $tipe = $request->tipe;
            
            Log::info('File info:', [
                'original_name' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'path' => $file->getPathname()
            ]);

            // Konversi tanggal
            try {
                $startdate = Carbon::createFromFormat('d/m/Y', $request->startdate)->format('Y-m-d');
                Log::info('Startdate converted:', ['original' => $request->startdate, 'converted' => $startdate]);
            } catch (\Exception $e) {
                Log::error('Gagal konversi tanggal:', ['error' => $e->getMessage(), 'date' => $request->startdate]);
                $startdate = Carbon::now()->format('Y-m-d');
            }
            
            // Tentukan folder berdasarkan tipe
            $folder = $tipe == '1200' ? 'sakit' : 'cuti-khusus';
            Log::info('Folder tujuan:', ['tipe' => $tipe, 'folder' => $folder]);
            
            // Generate nama file
            $fileName = "{$tipe}_{$nik}_{$startdate}_" . time() . '.' . $file->getClientOriginalExtension();
            Log::info('Generated filename:', ['filename' => $fileName]);
            
            // Path penyimpanan
            $year = Carbon::now()->format('Y');
            $month = Carbon::now()->format('m');
            $directory = "izin/{$year}/{$month}/{$folder}";
            $filePath = "{$directory}/{$fileName}";
            
            Log::info('Storage info:', [
                'directory' => $directory,
                'filePath' => $filePath,
                'disk' => 'public',
                'storage_path' => storage_path('app/public/' . $directory),
                'exists_before' => Storage::disk('public')->exists($directory)
            ]);

            // Cek dan buat direktori
            if (!Storage::disk('public')->exists($directory)) {
                Log::info('Directory tidak ada, mencoba membuat...');
                $created = Storage::disk('public')->makeDirectory($directory);
                Log::info('Directory created:', ['success' => $created]);
            }

            // Simpan file
            Log::info('Menyimpan file...');
            $stored = Storage::disk('public')->putFileAs(
                $directory,
                $file,
                $fileName
            );

            if (!$stored) {
                Log::error('Gagal menyimpan file');
                throw new \Exception('Gagal menyimpan file ke storage');
            }

            Log::info('File berhasil disimpan:', ['stored_path' => $stored]);

            // Cek apakah file benar-benar ada
            $exists = Storage::disk('public')->exists($filePath);
            Log::info('File exists after save:', ['exists' => $exists]);

            if ($exists) {
                Log::info('File info:', [
                    'size' => Storage::disk('public')->size($filePath),
                    'last_modified' => Storage::disk('public')->lastModified($filePath),
                    'url' => Storage::url($filePath)
                ]);
            }

            // Generate URL untuk akses file
            $fileUrl = Storage::url($filePath);
            Log::info('File URL:', ['url' => $fileUrl]);

            Log::info('=== UPLOAD FILE SUCCESS ===');

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'data' => [
                    'file_path' => $filePath,
                    'file_url' => $fileUrl,
                    'file_name' => $fileName,
                    'directory' => $directory,
                    'size' => Storage::disk('public')->size($filePath)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('=== UPLOAD FILE ERROR ===');
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Error line: ' . $e->getLine());
            Log::error('Error file: ' . $e->getFile());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal upload file: ' . $e->getMessage(),
                'error_detail' => [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]
            ], 500);
        }
    }

    /**
     * Mendapatkan file
     */
    public function getFile($filename)
    {
        Log::info('Get file request:', ['filename' => $filename]);
        
        $path = "izin/" . str_replace('-', '/', $filename);
        Log::info('Looking for file at path:', ['path' => $path]);
        
        if (!Storage::disk('public')->exists($path)) {
            Log::error('File not found:', ['path' => $path]);
            abort(404);
        }

        Log::info('File found, sending...');
        return response()->file(Storage::disk('public')->path($path));
    }
}