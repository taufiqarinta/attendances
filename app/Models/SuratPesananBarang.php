<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratPesananBarang extends Model
{
    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'tanggal_kirim',
        'pengirim',
        'penerima',
        'dikirim_ke',
        'status',
        'total_keseluruhan',
        'pemesan',
        'jenis_harga',
        'nomor_do',
        'total_jumlahbox',
        'created_by',
        'approved_by',
        'approved_at',
        'diketahui_by',
        'diketahui_at',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_kirim' => 'date',
        'approved_at' => 'datetime',
        'total_keseluruhan' => 'decimal:2',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(SuratPesananBarangDetail::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function diketahuir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diketahui_by');
    }

    public static function generateNomorSurat(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = $year . $month;

        $lastSurat = self::where('nomor_surat', 'LIKE', $prefix . '%')
            ->orderBy('nomor_surat', 'desc')
            ->first();

        if ($lastSurat) {
            $lastNumber = (int) substr($lastSurat->nomor_surat, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotal(): void
    {
        $this->total_keseluruhan = $this->details->sum('total_rp');
        $this->save();
    }
}