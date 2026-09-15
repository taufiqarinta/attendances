<?php

namespace App\Models\Orientation;

use App\Models\Orientation\MasterPlant;
use App\Models\Orientation\MasterOrientationCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrientationProgram extends Model
{
    use HasFactory;

    protected $connection = 'dev_test';
    protected $table = 'orientations';

    protected $fillable = [
        'category_id',
        'batch_name',
        'master_plants_id',
        'participants',
        'hr_pic',
        'status',
    ];

    protected $casts = [
        'participants' => 'array',
        'hr_pic' => 'array',
    ];

    /**
     * Relasi ke Master Orientation Category
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
     * Relasi ke Master Plant
     */
    public function plant()
    {
        return $this->belongsTo(
            MasterPlant::class,
            'master_plants_id',
            'id'
        );
    }

    /**
     * Rincian kegiatan dalam program orientation.
     */
    public function activities()
    {
        return $this->hasMany(
            OrientationActivity::class,
            'orientation_id',
            'id'
        );
    }
}