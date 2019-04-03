<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesmanVendor extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_code',
        'sap_server'
    ];
}
