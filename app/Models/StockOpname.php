<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stock_opnames';
    
    protected $fillable = [
        'kode_opname',
        'user_id',
        'nama_spg',
        'toko_id',
        'nama_toko',
        'tahun',
        'bulan',
        'tanggal',
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Generate kode opname
     */
    public static function generateKodeOpname()
    {
        $prefix = 'RSO';
        $date = now()->format('Ymd');
        
        $latest = self::where('kode_opname', 'like', $prefix . $date . '%')
            ->orderBy('kode_opname', 'desc')
            ->first();
            
        if ($latest) {
            $lastNumber = intval(substr($latest->kode_opname, -4));
            $number = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $number = '0001';
        }
        
        return $prefix . $date . $number;
    }

    /**
     * Get next period for stock opname
     */
    public static function getNextPeriod($tokoId = null)
    {
        $currentMonth = date('n');
        $currentYear = date('Y');
        
        // Cari opname terakhir untuk toko
        if ($tokoId) {
            $latestOpname = self::where('toko_id', $tokoId)
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->first();
                
            if ($latestOpname) {
                // Jika bulan ini sudah ada opname, tetap bulan ini
                if ($latestOpname->tahun == $currentYear && $latestOpname->bulan == $currentMonth) {
                    return [
                        'tahun' => $currentYear,
                        'bulan' => $currentMonth,
                    ];
                }
                // Jika bulan lalu, ke bulan ini
                else {
                    return [
                        'tahun' => $currentYear,
                        'bulan' => $currentMonth,
                    ];
                }
            }
        }
        
        // Default: bulan dan tahun sekarang
        return [
            'tahun' => $currentYear,
            'bulan' => $currentMonth,
        ];
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toko()
    {
        return $this->belongsTo(DaftarToko::class, 'toko_id');
    }

    public function details()
    {
        return $this->hasMany(StockOpnameDetail::class, 'opname_id');
    }
}