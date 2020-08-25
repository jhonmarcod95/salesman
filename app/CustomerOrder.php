<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    protected $fillable = [
        'customer_code',
        'do_number',
        'purchase_date',
        'server'
    ];
}
