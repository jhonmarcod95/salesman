<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CloseVisit extends Model
{
    protected $fillable = [
        'isApproved'
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function approvedBy() 
    {
        return $this->belongsTo(User::class);
    }
}
