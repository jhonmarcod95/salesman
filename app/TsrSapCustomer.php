<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TsrSapCustomer extends Model
{
    protected $fillable = [
        'customer_code',
        'tsr_customer_code',
        'sales_organization',
        'common_division',
        'division',
        'partner_function',
        'server',
    ];
}
