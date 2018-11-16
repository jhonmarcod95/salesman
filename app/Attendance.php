<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'sign_in',
        'sign_in_image',
        'sign_in_latitude',
        'sign_in_longitude',
        'sign_in_speed',
        'sign_out',
        'sign_out_image',
        'sign_out_latitude',
        'sign_out_longitude',
        'sign_out_speed',
        'remarks'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
