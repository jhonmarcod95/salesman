<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SendLocation extends Model
{
    protected $fillable = [
        'lat',
        'lng',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function signType()
    {
        return $this->belongsTo(SignType::class);
    }
}
