<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Project;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'description', 'priority', 'user_id',
    ];

    //Relationships
    public function project(){
        return $this->belongsTo(Project::class);
    }
}
