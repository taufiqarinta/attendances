<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'username',
        'aksi',
        'fitur',
        'deskripsi',
        'ip_address',
        'device',
        'created_at',
    ];

    public $timestamps = false;

    protected $dates = [
        'created_at',
    ];

    /**
     * Relasi ke tabel users
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
