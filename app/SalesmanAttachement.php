<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesmanAttachement extends Model
{
    protected $fillable = [
        'remarks',
        'attachment'
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
