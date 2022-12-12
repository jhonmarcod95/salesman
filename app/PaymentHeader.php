<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use function foo\func;

class PaymentHeader extends Model implements Auditable
{

    use \Awobaz\Compoships\Compoships;
    use \OwenIt\Auditing\Auditable;

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
        'document_code',
        'expense_from',
        'expense_to',
    ];

    public function paymentDetail() {
        return $this->hasMany(PaymentDetail::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'document_code', 'document_code');
    }

    public function company(){
        return $this->hasOne(Company::class, 'code', 'company_code');
    }

    public function payable(){
        return $this->hasOne(PaymentDetail::class)->where('item', '1');
    }

    public function checkVoucher(){
        return $this->hasOne(CheckVoucher::class, ['reference_number', 'company_code'], ['reference_number', 'company_code']);
    }
}
