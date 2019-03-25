<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentHeader extends Model
{
    protected $fillable = [
        'company_code',
        'company_name',
        'reference_number',
        'ap_user',
        'vendor_code',
        'vendor_name',
        'document_type',
        'payment_terms',
        'header_text',
        'document_date',
        'posting_date',
        'baseline_date',
    ];

    public function paymentDetail() {
        return $this->hasMany(PaymentDetail::class);
    }
}
