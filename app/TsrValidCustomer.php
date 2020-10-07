<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TsrValidCustomer extends Model
{
    protected $fillable = [
        'customer_code',
        'sales_organization',
        'common_division',
        'division',
        'customer_order_block',
        'deletion_flag',
        'server',
    ];
}
