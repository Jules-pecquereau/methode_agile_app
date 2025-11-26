<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'start', 'end', 'team_id', 'task_id'];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
