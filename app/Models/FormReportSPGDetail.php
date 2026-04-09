<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormReportSPGDetail extends Model
{
    use HasFactory;

    protected $table = 'form_reportspg_detail';
    
    protected $fillable = [
        'report_id',
        'item_code',
        'nama_barang',
        'ukuran',
        'qty_terjual',
        'qty_masuk',
        'catatan'
    ];

    /**
     * Relationship dengan header
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(FormReportSPG::class, 'report_id');
    }

    /**
     * Relationship dengan item master melalui item_code
     */
    public function itemMaster()
    {
        return $this->belongsTo(ItemMaster::class, 'item_code', 'item_code');
    }
}