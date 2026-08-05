<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpensesEntry extends Model
{

    protected $fillable = [
        'expenses',
        'totalExpenses'
    ];

    protected $casts = [
        'expenses' => 'array',
    ];

     /**
     * Convet array to string conversion
     */
    public function setExpensesAttribute($value)
    {
        $this->attributes['expenses'] = json_encode($value);
    }

    public function expensesModel() {
        return $this->hasMany(Expense::class);
    }

    public function verifiedExpense() {
        return $this->hasMany(Expense::class)->where('verified_status_id', 1);
    }

    public function unverifiedExpense(){
        return $this->hasMany(Expense::class)->where('verified_status_id', 2);
    }

    public function rejectedExpense(){
        return $this->hasMany(Expense::class)->where('verified_status_id', 3);
    }

    public function balanceRejectedExpense(){
        return $this->hasMany(Expense::class)->where('verified_status_id', 3);
    }

    public function pendingExpense() {
        return $this->hasMany(Expense::class)->where('verified_status_id', 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeExpensePerMonth($query, $start_date, $last_date) {

        return $query
        ->whereHas('expensesModel', function ($expenseQuery) use ($start_date, $last_date) {
                $expenseQuery->whereBetween('created_at', [$start_date, $last_date]);
        })
        ->with(['expensesModel' => function ($expenseQuery) use ($start_date, $last_date) {
            $expenseQuery->whereBetween('created_at', [$start_date, $last_date]);
        }])
        ->withCount(['verifiedExpense' => function ($expenseQuery) use ($start_date, $last_date) {
            $expenseQuery->whereBetween('created_at', [$start_date, $last_date]);
        }])
        ->withCount(['unverifiedExpense' => function ($expenseQuery) use ($start_date, $last_date) {
            $expenseQuery->whereBetween('created_at', [$start_date, $last_date]);
        }])
        ->withCount(['rejectedExpense' => function ($expenseQuery) use ($start_date, $last_date) {
            $expenseQuery->whereBetween('created_at', [$start_date, $last_date]);
        }])
        ->withCount(['balanceRejectedExpense' => function ($expenseQuery) use ($start_date, $last_date) {
            $expenseQuery->whereBetween('created_at', [$start_date, $last_date]);
        }])
        ->withCount(['pendingExpense' => function ($expenseQuery) use ($start_date, $last_date) {
            $expenseQuery->whereBetween('created_at', [$start_date, $last_date]);
        }])
        ->withCount(['expensesModel' => function ($expenseQuery) use ($start_date, $last_date) {
            $expenseQuery->whereBetween('created_at', [$start_date, $last_date]);
        }]);
    }
}
