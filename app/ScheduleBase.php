<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleBase extends Model
{
    protected $fillable = [
        'name'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
