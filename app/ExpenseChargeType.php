<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseChargeType extends Model
{
    //



    public function chargeType(){
        return $this->hasOne(chargeType::class, 'id', 'charge_type_id');
    }

    public function expenseType()
    {
        return $this->hasOne(ExpensesType::class,'id','expense_type_id');
    }
}
