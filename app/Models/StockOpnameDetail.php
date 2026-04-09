<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameDetail extends Model
{
    use HasFactory;

    protected $table = 'stock_opname_details';
    
    protected $fillable = [
        'opname_id',
        'item_code',
        'nama_barang',
        'ukuran',
        'stock',
        'keterangan'
    ];

    /**
     * Relationships
     */
    public function opname()
    {
        return $this->belongsTo(StockOpname::class, 'opname_id');
    }

    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'item_code', 'item_code');
    }
}