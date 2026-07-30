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
        'code_activity',
        'activity_name',
        'description',
        'plants',
        'status'
    ];

    protected $casts = [
        'plants' => 'array', // Cast JSON ke array
        'status' => 'boolean'
    ];

    // Accessor untuk menampilkan nama plant
    public function getPlantNamesAttribute()
    {
        if (empty($this->plants)) {
            return collect();
        }

        return MasterPlant::whereIn('id', $this->plants)
            ->orderBy('name_plant')
            ->get();
    }

    // Accessor untuk mendapatkan plant ids sebagai array
    public function getPlantIdsAttribute()
    {
        return $this->plants ?? [];
    }
}