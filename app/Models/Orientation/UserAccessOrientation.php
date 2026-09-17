<?php

namespace App\Models\Orientation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccessOrientation extends Model
{
    use HasFactory;
    protected $connection = 'hris_kobin';

    protected $table = 'user_access_orientation';

    protected $fillable = [
        'nama',
        'nik',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];
}