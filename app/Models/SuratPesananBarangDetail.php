<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPesananBarangDetail extends Model
{
    protected $fillable = [
        'surat_pesanan_barang_id',
        'ukuran',
        'nama_product',
        'brand',
        'kw',
        'jumlah_box',
        'harga_satuan_box',
        'disc',
        'biaya_tambahan_ekspedisi',
        'total_rp',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_box' => 'decimal:2',
        'harga_satuan_box' => 'decimal:2',
        'disc' => 'decimal:2',
        'total_rp' => 'decimal:2',
    ];

    public function suratPesananBarang(): BelongsTo
    {
        return $this->belongsTo(SuratPesananBarang::class);
    }

    public function calculateTotal(): void
    {
        // Hitung harga satuan setelah diskon rupiah dan biaya tambahan
        $hargaSatuanSetelahAdjustment = $this->harga_satuan_box - $this->disc + $this->biaya_tambahan_ekspedisi;
        
        // Total = jumlah box × harga satuan yang sudah disesuaikan
        $this->total_rp = $this->jumlah_box * $hargaSatuanSetelahAdjustment;
    }
}