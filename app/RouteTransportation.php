<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteTransportation extends Model
{
    protected $fillable = [
        'from',
        'to',
        'fare',
        'remarks'
    ];

    public function transportation()
    {
        return $this->belongsTo(Transportation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
