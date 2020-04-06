<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckInfo extends Model
{
    protected $fillable = [
        'document_code',
        'company_code',
        'fiscal_year',
        'house_bank',
        'account_id',
        'check_number',
    ];
}
