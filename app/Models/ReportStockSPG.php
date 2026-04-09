<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportStockSPG extends Model
{
    use HasFactory;

    protected $table = 'report_stock_spg';
    
    protected $fillable = [
        'kode_report',
        'user_id',
        'nama_spg',
        'toko_id',
        'nama_toko',
        'tahun',
        'bulan',
        'minggu_ke',
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relationship dengan detail
     */
    public function details(): HasMany
    {
        return $this->hasMany(ReportStockSPGDetail::class, 'report_id');
    }

    /**
     * Relationship dengan user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship dengan toko
     */
    public function toko(): BelongsTo
    {
        return $this->belongsTo(DaftarToko::class, 'toko_id');
    }

    /**
     * Generate kode report baru
     */
    public static function generateKodeReport(): string
    {
        $year = date('Y');
        $month = date('m');
        
        // Ambil nomor terakhir untuk bulan ini
        $lastReport = self::whereYear('created_at', $year)
                         ->whereMonth('created_at', $month)
                         ->orderBy('id', 'desc')
                         ->first();
        
        if ($lastReport) {
            // Extract nomor dari kode terakhir
            $lastNumber = (int) substr($lastReport->kode_report, 9);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('RSS%s%s%04d', $year, $month, $newNumber);
    }

    public static function getNextPeriod($tokoId)
    {
        $latestReport = self::where('toko_id', $tokoId)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('minggu_ke', 'desc')
            ->first();
        
        if ($latestReport) {
            $nextWeek = $latestReport->minggu_ke + 1;
            $nextMonth = $latestReport->bulan;
            $nextYear = $latestReport->tahun;
            
            // Jika minggu > 5, pindah ke bulan berikutnya
            if ($nextWeek > 5) {
                $nextWeek = 1;
                $nextMonth = $latestReport->bulan + 1;
                if ($nextMonth > 12) {
                    $nextMonth = 1;
                    $nextYear = $latestReport->tahun + 1;
                }
            }
            
            return [
                'tahun' => $nextYear,
                'bulan' => $nextMonth,
                'minggu_ke' => $nextWeek,
                'previous_report' => $latestReport
            ];
        }
        
        // Default to current period if no previous report
        return [
            'tahun' => date('Y'),
            'bulan' => date('n'),
            'minggu_ke' => 1,
            'previous_report' => null
        ];
    }

    /**
     * Get previous week stock for this store
     */
    public static function getPreviousStock($tokoId, $tahun, $bulan, $mingguKe)
    {
        if ($mingguKe == 1) {
            // If week 1, get from previous month
            $prevMonth = $bulan - 1;
            $prevYear = $tahun;
            
            if ($prevMonth == 0) {
                $prevMonth = 12;
                $prevYear = $tahun - 1;
            }
            
            // Get the last week of previous month
            return self::where('toko_id', $tokoId)
                ->where('tahun', $prevYear)
                ->where('bulan', $prevMonth)
                ->orderBy('minggu_ke', 'desc')
                ->first();
        } else {
            // Get previous week
            return self::where('toko_id', $tokoId)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->where('minggu_ke', $mingguKe - 1)
                ->first();
        }
    }
}