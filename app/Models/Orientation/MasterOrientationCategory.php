<?php

namespace App\Models\Orientation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterOrientationCategory extends Model
{
    use HasFactory;
    protected $connection = 'db_training';

    protected $table = 'master_orientation_categories';

    protected $fillable = [
        'code_category',
        'category_name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Satu kategori memiliki banyak orientation activity (via pivot)
     */
    public function activities()
    {
        return $this->belongsToMany(
            MasterOrientationActivity::class,
            'master_orientation_activity_category',
            'category_id',
            'activity_id'
        )->withTimestamps();
    }
}