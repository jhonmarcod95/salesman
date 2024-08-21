<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Expense extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'amount',
        'attachment',
        'remarks',
        'dms_reference',
        'expense_verification_status_id',
        'expense_rejected_reason_id',
        'is_verified',
        'verified_by',
        'date_verified',
        'status_id',
        'expense_from',
        'expense_to',
        'should_be_posting_date',
        'expense_grouping'
    ];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function expensesType() {
        return $this->belongsTo(ExpensesType::class);
    }

    public function expensesEntry() {
        return $this->belongsTo(ExpensesEntry::class);
    }

    public function payments(){
        return $this->hasOne(Payment::class);
    }

    public function receiptExpenses() {
        return $this->hasOne(ReceiptExpense::class);
    }

    public function routeTransportation() {
        return $this->hasOne(RouteTransportation::class);
    }

    public function postedPayments()
    {
        return $this->hasMany(Payment::class);
    }

    public function expenseVerificationStatus()
    {
        return $this->belongsTo(ExpenseVerificationStatus::class);
    }
}
