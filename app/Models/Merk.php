<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merk extends Model
{
    use HasFactory;

    protected $fillable = ['name','code_item','is_granite'];

    public function ukurans()
    {
        return $this->hasMany(Ukuran::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_merks', 'id_merks', 'id_customer', 'id', 'id_customer');
    }

}
