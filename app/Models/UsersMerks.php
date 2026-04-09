<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersMerks extends Model
{
    use HasFactory;

    protected $table = 'users_merks';

    protected $fillable = [
        'id_users_merks',
        'id_customer',
        'id_merks',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
