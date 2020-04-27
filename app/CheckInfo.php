<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckInfo extends Model
{
    protected $fillable = [
        'check_voucher_id',
        'document_code',
        'company_code',
        'fiscal_year',
        'house_bank',
        'account_id',
        'check_number',
    ];
}
