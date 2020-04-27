<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckInfoError extends Model
{
    //
    protected $fillable = [
        'check_voucher_id',
        'description',
    ];
}
