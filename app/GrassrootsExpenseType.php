<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrassrootsExpenseType extends Model
{
    protected $fillable = [
        'name',
        'amount_rate',
        'grassroots_expense_type_id',
        'status'
    ];
}
