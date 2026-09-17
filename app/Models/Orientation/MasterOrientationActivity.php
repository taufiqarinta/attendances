<?php

namespace App\Models\Orientation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterOrientationActivity extends Model
{
    use HasFactory;

    protected $connection = 'hris_kobin';

    protected $table = 'master_orientation_activities';

    protected $fillable = [
        'category_id',
        'code_activity',
        'activity_name',
        'description',
        'plants',
        'status',
    ];

    protected $casts = [
        'plants' => 'array',
        'status' => 'boolean',
    ];

    /**
     * Activity memiliki satu kategori
     */
    public function category()
    {
        return $this->belongsTo(
            MasterOrientationCategory::class,
            'category_id',
            'id'
        );
    }

    /**
     * Accessor untuk menampilkan data plant
     */
    public function getPlantNamesAttribute()
    {
        if (empty($this->plants)) {
            return collect();
        }

        return MasterPlant::whereIn('id', $this->plants)
            ->orderBy('name_plant')
            ->get();
    }

    /**
     * Accessor untuk mendapatkan plant IDs sebagai array
     */
    public function getPlantIdsAttribute()
    {
        return $this->plants ?? [];
    }
}