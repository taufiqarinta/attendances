<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMasterTambahan extends Model
{
    use HasFactory;

    protected $table = 'item_master_tambahan';
    
    protected $fillable = [
        'item_code',
        'item_name',
        'ukuran',
        'status',
    ];

    protected $guarded = ['id'];


     public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope untuk item nonaktif
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Cek apakah item aktif
     */
    public function isActive()
    {
        return $this->status == 1;
    }

    /**
     * Cek apakah item nonaktif
     */
    public function isInactive()
    {
        return $this->status == 0;
    }
}