<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseDeduction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable,SoftDeletes;

    protected $fillable = [
        'expense_id',
        'employee_monthly_expense_id',
        'balance_from_amount',
        'balance_to_amount',
        'balance_deducted_amount',
        'expense_from_amount',
        'expense_to_amount',
        'expense_remaining_amount'
    ];
}
