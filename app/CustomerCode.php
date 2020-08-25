<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerCode extends Model
{
    protected $fillable = [
        'customer_code',
        'server'
    ];
}
