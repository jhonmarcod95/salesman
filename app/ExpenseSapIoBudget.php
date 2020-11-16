<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseSapIoBudget extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'io',
        'io_date',
        'planned_budget',
        'budget_balance',
        'status',
        'server'
    ];
}
