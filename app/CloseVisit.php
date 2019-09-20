<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CloseVisit extends Model
{
    protected $fillable = [
        'isApproved',
        'reason',
        'approved_date'
    ];

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function confirmedBy() 
    {
        return $this->belongsTo(User::class,'confirmed_by','id');
    }
}
