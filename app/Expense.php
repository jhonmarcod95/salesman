<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'amount',
        'attachment',
        'remarks',
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
}
