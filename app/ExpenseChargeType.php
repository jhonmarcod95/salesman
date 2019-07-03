<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseChargeType extends Model
{

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function chargeType(){
        return $this->hasOne(ChargeType::class, 'id', 'charge_type_id');
    }

    public function expenseType()
    {
        return $this->hasOne(ExpensesType::class,'id','expense_type_id');
    }
}
