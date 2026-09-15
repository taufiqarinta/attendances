<?php

namespace App\Models\Orientation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterOrientationCategory extends Model
{
    use HasFactory;
       protected $connection = 'dev_test';

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
     * Satu kategori memiliki banyak orientation activity
     */
    public function activities()
    {
        return $this->hasMany(
            MasterOrientationActivity::class,
            'category_id',
            'id'
        );
    }
}