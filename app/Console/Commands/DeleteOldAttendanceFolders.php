<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DeleteOldAttendanceFolders extends Command
{
    protected $signature = 'attendance:delete-old-folders';
    protected $description = 'Delete attendance folders older than 2 months ago';

    public function handle()
    {
        $cutoffDate = Carbon::now()->subMonths(2)->startOfMonth();
        $disk = Storage::disk('public');
        
        // Ambil TAHUN yang ada di dalam folder attendances/
        $attendancesPath = 'attendances';
        if (!$disk->exists($attendancesPath)) {
            $this->info("Folder attendances tidak ditemukan.");
            return;
        }
        
        $years = $disk->directories($attendancesPath); // ambil: attendances/2026, attendances/2027, dll
        
        $totalDeleted = 0;
        
        foreach ($years as $yearPath) {
            $year = basename($yearPath); // '2026', '2027', dll
            
            // Ambil semua folder bulan dalam tahun ini
            $folders = $disk->directories($yearPath);
            
            foreach ($folders as $folder) {
                $monthNumber = basename($folder); // '01', '02', dll
                
                // Buat tanggal dari tahun + bulan folder
                $folderDate = Carbon::createFromDate((int)$year, (int)$monthNumber, 1);
                
                // Hanya hapus jika folderDate < cutoffDate
                if ($folderDate->lt($cutoffDate)) {
                    $disk->deleteDirectory($folder);
                    $this->info("Deleted: $folder");
                    $totalDeleted++;
                }
            }
        }
        
        $this->info("Selesai. Total folder dihapus dari semua tahun: $totalDeleted");
    }
}