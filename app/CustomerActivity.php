<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerActivity extends Model
{
    protected $fillable = [
        'customer_id',
        'activity_description',
        'activity_date',
    ];
}
