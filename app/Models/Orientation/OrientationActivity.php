<?php

namespace App\Models\Orientation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrientationActivity extends Model
{
    use HasFactory;
    protected $connection = 'hris_kobin';
    protected $table = 'orientation_activities';

    protected $fillable = [
        'orientation_id',
        'master_orientation_activitie_id',
        'activity_date',
        'start_time',
        'end_time',
        'pic_employee_id',
        'status',
        'score',
        'score_note',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    /**
     * Relasi ke Orientation Program
     */
    public function orientation()
    {
        return $this->belongsTo(
            OrientationProgram::class,
            'orientation_id'
        );
    }

    /**
     * Relasi ke Master Activity
     */
    public function masterActivity()
    {
        return $this->belongsTo(
            MasterOrientationActivity::class,
            'master_orientation_activitie_id'
        );
    }
}