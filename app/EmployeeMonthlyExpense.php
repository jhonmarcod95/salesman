<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeMonthlyExpense extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'expense_count',
        'verified_count',
        'unverified_count',
        'rejected_count',
        'expense_amount',
        'verified_amount',
        'unverified_amount',
        'rejected_amount',
        'balance_rejected_amount',
        'date_notified',
        'is_acknowledge',
        'acknowledge_date'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function weeklyExpense() {
        return $this->hasMany(EmployeeWeeklyExpense::class);
    }
}