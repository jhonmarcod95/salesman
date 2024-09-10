<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentDetail extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'item',
        'item_text',
        'gl_account',
        'description',
        'assignment',
        'input_tax_code',
        'internal_order',
        'amount',
        'charge_type',
        'business_area',
        'or_number',
        'supplier_name',
        'supplier_address',
        'supplier_tin_number',
        'payment_header_id',
    ];

    public function balanceHistory() {
        return $this->hasOne(BalanceHistory::class);
    }
}
