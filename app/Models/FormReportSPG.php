<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormReportSPG extends Model
{
    use HasFactory;

    protected $table = 'form_reportspg';
    
    protected $fillable = [
        'kode_report',
        'tanggal',
        'user_id',
        'nama_spg',
        'toko_id',
        'no_sales',
        'total_customer',    
        'customer_transaksi', 
        'customer_lost_sale',
        'analisa_lost_sale', 
    ];

    protected $casts = [
        'tanggal' => 'date',
        'no_sales' => 'boolean' // Cast sebagai boolean
    ];

    /**
     * Relationship dengan detail
     */
    public function details(): HasMany
    {
        return $this->hasMany(FormReportSPGDetail::class, 'report_id');
    }

    public function buktiTransaksis(): HasMany
    {
        return $this->hasMany(BuktiTransaksi::class, 'form_reportspg_id');
    }

    /**
     * Relationship dengan user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
        
        return sprintf('RPS%s%s%04d', $year, $month, $newNumber);
    }
}