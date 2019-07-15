<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subparticular extends Model
{
    protected $fillable = [
        'name',
        'ranking'
    ];

    public function particular()
    {
        return $this->belongsTo(Particular::class);
    }


}
