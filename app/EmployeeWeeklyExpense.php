<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeWeeklyExpense extends Model
{
    protected $fillable = [
        'employee_monthly_expense_id',
        'week_no',
        'user_id',
        'expense_count',
        'verified_count',
        'unverified_count',
        'rejected_count',
        'expense_amount',
        'verified_amount',
        'rejected_amount'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function monthlyExpense() {
        return $this->belongsTo(EmployeeMonthlyExpense::class);
    }
}
