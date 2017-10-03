<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Task;
use App\Models\Color;

class Project extends Model
{
    protected $fillable = [
        'name', 'color_id', 'user_id', 'status'
    ];

    //Relationships
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    //Scopes
    public function scopeActive($query)
    {
        return $query->where('status', null);
    }
}
