<?php

namespace App\Models\Orientation;

use App\Models\Orientation\MasterPlant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrientationProgram extends Model
{
    use HasFactory;
    protected $connection = 'hris_kobin';
    protected $table = 'orientations';

    protected $fillable = [
        'batch_name',
        'master_plants_id',
        'participants',
        'status',
    ];

    protected $casts = [
        'participants' => 'array',
    ];

    /**
     * Relasi ke Master Plant
     */
    public function plant()
    {
        return $this->belongsTo(
            MasterPlant::class,
            'master_plants_id'
        );
    }

    /**
     * Rincian kegiatan dalam program orientation.
     */
    public function activities()
    {
        return $this->hasMany(OrientationActivity::class, 'orientation_id');
    }
}