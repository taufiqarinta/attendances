<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarAgen extends Model
{
    use HasFactory;

    protected $table = 'daftar_agens';

    protected $fillable = [
        'npwp',
        'nama_agen',
        'nama_sales',
        'alamat'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}