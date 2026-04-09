<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMaster extends Model
{
    use HasFactory;

    protected $table = 'item_master';
    
    protected $fillable = [
        'item_code',
        'item_name',
        'ukuran'
    ];

    protected $guarded = ['id'];
}