<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SignType extends Model
{
    protected $fillable = [
        'name'
    ];

    public function sendLocations()
    {
        return $this->hasMany(SendLocation::class);
    }
}
