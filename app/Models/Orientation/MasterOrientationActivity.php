<?php

namespace App\Models\Orientation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterOrientationActivity extends Model
{
    use HasFactory;

    protected $connection = 'db_training';

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

    public function category()
    {
        return $this->belongsTo(
            MasterOrientationCategory::class,
            'category_id',
            'id'
        );
    }

    public function categories()
    {
        return $this->belongsToMany(
            MasterOrientationCategory::class,
            'master_orientation_activity_category',
            'activity_id',
            'category_id'
        )->withTimestamps();
    }

    public function getCategoryIdsAttribute()
    {
        return $this->categories->pluck('id')->toArray();
    }

    public function getCategoryNamesAttribute()
    {
        return $this->categories->pluck('category_name')->toArray();
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