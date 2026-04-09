<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarToko extends Model
{
    use HasFactory;

    protected $table = 'daftar_toko';
    
    protected $fillable = [
        'kode_spg',
        'nama_spg',
        'divisi',
        'nama_toko',
        'kota',
        'status' // Tambahkan ini
    ];

    protected $attributes = [
        'status' => 1 // Default aktif
    ];

    protected $casts = [
        'status' => 'boolean'
    ];
    
    /**
     * Relationship dengan User (SPG)
     */
    public function spgUser()
    {
        return $this->belongsTo(User::class, 'kode_spg', 'id_customer');
    }

    /**
     * Scope untuk toko aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope untuk toko nonaktif
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Cek apakah toko aktif
     */
    public function isActive()
    {
        return $this->status == 1;
    }

    /**
     * Cek apakah toko nonaktif
     */
    public function isInactive()
    {
        return $this->status == 0;
    }
}