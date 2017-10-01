<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Project;

class Color extends Model
{
    protected $fillable = [
        'name', 'hex_string'
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
