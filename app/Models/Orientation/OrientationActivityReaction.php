<?php

namespace App\Models\Orientation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrientationActivityReaction extends Model
{
    use HasFactory;

    protected $connection = 'dev_test';

    protected $table = 'orientation_activity_reactions';

protected $fillable = [
    'orientation_activity_id',
    'employee_id',       
    'reaction_id',
    'note',
    'rating',
];

protected $casts = [
    'employee_id' => 'array',   
];

    /**
     * Relasi ke Orientation Activity
     */
    public function activity()
    {
        return $this->belongsTo(
            OrientationActivity::class,
            'orientation_activity_id',
            'id'
        );
    }

    /**
     * Relasi ke Master Reaksi Evaluasi
     */
    public function reaction()
    {
        return $this->belongsTo(
            MasterReaksiEvaluasi::class,
            'reaction_id',
            'id'
        );
    }
}