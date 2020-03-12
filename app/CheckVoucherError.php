<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckVoucherError extends Model
{
    //
    protected $fillable = [
        'payment_header_id',
        'description',
    ];
}
