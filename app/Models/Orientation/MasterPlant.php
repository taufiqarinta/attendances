<?php

namespace App\Models\Orientation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPlant extends Model
{
    use HasFactory;
    protected $connection = 'dev_test';
    protected $table = 'master_plants';

    protected $fillable = [
        'code',
        'name_plant'
    ];
}