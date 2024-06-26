<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AapcStore extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'zip_code',
        'contact_number',
        'region_name',
        'barangay',
    ];
}
