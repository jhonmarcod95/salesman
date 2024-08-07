<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceiptExpense extends Model
{
    protected $fillable = [
        'receipt_transaction',
        'receipt_type',
        'receipt_number',
        'vendor_name',
        'vendor_address',
        'tin_number',
        'date_receipt',
        'tin_number_extend',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function expense() {
        return $this->belongsTo(Expense::class);
    }

    public function receiptType() {
        return $this->belongsTo(ReceiptType::class,'receipt_type_id');
    }

    public function receiptTransaction() {
        return $this->belongsTo(ReceiptTransaction::class,'receipt_transaction');
    }

}
