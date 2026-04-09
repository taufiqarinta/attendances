<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarHarga extends Model
{
    use HasFactory;

    protected $table = 'daftar_hargas';

    protected $fillable = [
        'type',
        'kw',
        'brand',
        'ukuran',
        'karton',
        'kategori',
        'kel_harga_miss2',
        'harga_franco',
        'harga_loco',
    ];

    protected $casts = [
        'harga_franco' => 'decimal:2',
        'harga_loco' => 'decimal:2',
    ];
}