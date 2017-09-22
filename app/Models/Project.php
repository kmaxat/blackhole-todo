<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Task;
use App\Models\Color;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'color_id', 'user_id'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
