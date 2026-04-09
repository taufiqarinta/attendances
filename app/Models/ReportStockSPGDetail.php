<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportStockSPGDetail extends Model
{
    use HasFactory;

    protected $table = 'report_stock_spg_detail';
    
    protected $fillable = [
        'report_id',
        'item_code',
        'nama_barang',
        'ukuran',
        'stock',
        'qty_masuk',
        'catatan'
    ];

    /**
     * Relationship dengan header
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(ReportStockSPG::class, 'report_id');
    }

    /**
     * Relationship dengan item master melalui item_code
     */
    public function itemMaster()
    {
        return $this->belongsTo(ItemMaster::class, 'item_code', 'item_code');
    }
}