<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestSchedule extends Model
{
    protected $fillable = [
        'type',
        'code',
        'name',
        'address',
        'date',
        'start_time',
        'end_time',
        'status',
        'remarks'
    ];

    // Belongs to user
    public function user() {
        return $this->belongsTo(User::class);
    }

}
