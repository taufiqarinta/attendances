<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuktiTransaksi extends Model
{
    use HasFactory;

    protected $table = 'bukti_transaksi';
    
    protected $fillable = [
        'form_reportspg_id',
        'nama_file'
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(FormReportSPG::class, 'form_reportspg_id');
    }
}