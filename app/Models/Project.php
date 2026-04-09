<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'id',
        'name'
    ];

    public function subProjects()
    {
        return $this->hasMany(SubProject::class, 'project_id', 'id');
    }
}
