<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubProject extends Model
{
    use HasFactory;

    protected $table = 'sub_projects';

    protected $fillable = [
        'id',
        'nama_pt',
        'nama_sub_project',
        'project_id'
    ];

    public function dailyActivities()
    {
        return $this->hasMany(DailyActivity::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
