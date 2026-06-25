<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeofencePlant extends Model
{
    
    protected $connection = 'hris_kobin';

    /**
     * Nama tabel aktual di database hris_kobin.
     */
    public function getTable()
    {
        return config('attendance.mode') == 'test'
            ? 'master_geofence_plant_test'
            : 'master_geofence_plant';
    }

    /**
     * Kolom yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'plant',
        'plant_name',
        'type',
        'radius',
        'longitude',
        'latitude',
        'exclude_department',
        'exclude_nik',
        'status',
    ];

    /**
     * Casting tipe data otomatis.
     */
    protected $casts = [
        'radius' => 'float',
        'longitude' => 'float',
        'latitude' => 'float',
        'status' => 'integer',
    ];

    /**
     * Jika tabel di DB sudah punya kolom created_at & updated_at
     * bertipe datetime/timestamp, biarkan true (default Laravel).
     * Set false jika tabel TIDAK punya kolom tersebut.
     */
    public $timestamps = true;

    /**
     * Tipe data tipe IN/OUT yang valid — dipakai untuk validasi & dropdown.
     */
    public const TYPES = ['in', 'out'];

    /**
     * Status aktif/nonaktif — dipakai untuk validasi & dropdown.
     * Sesuaikan jika di database Anda status memakai nilai/representasi lain.
     */
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    /**
     * Helper: ubah string "IT,HRD,Finance" menjadi array ['IT','HRD','Finance'].
     * Dipakai di form edit agar mudah ditampilkan per-item, dan di tampilan
     * index sebagai badge per departemen/NIK yang dikecualikan.
     */
    public function getExcludeDepartmentArrayAttribute(): array
    {
        return $this->toArrayFromCommaString($this->exclude_department);
    }

    public function getExcludeNikArrayAttribute(): array
    {
        return $this->toArrayFromCommaString($this->exclude_nik);
    }

    private function toArrayFromCommaString(?string $value): array
    {
        if (empty($value)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    /**
     * Helper: label status untuk ditampilkan di Blade.
     */
    public function getStatusLabelAttribute(): string
    {
        return (int) $this->status === self::STATUS_ACTIVE ? 'Aktif' : 'Nonaktif';
    }
}
