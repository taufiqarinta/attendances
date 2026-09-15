<?php

namespace App\Models\Orientation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterReaksiEvaluasi extends Model
{
    use HasFactory;

    protected $connection = 'dev_test';

    protected $table = 'master_reaksi_evaluasi';

    protected $fillable = [
        'reaksi_name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}