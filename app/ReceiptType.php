<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceiptType extends Model
{
    protected $fillable = [
        'name',
        'tax_code'
    ];
}
