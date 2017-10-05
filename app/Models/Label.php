<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $fillable = [
        'name', 'color_id', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->morphedByMany('App\Models\Tag', 'labellable');
    }

    public function projects()
    {
        return $this->morphedByMany('App\Models\Project', 'labellable');
    }
}
