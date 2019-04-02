<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChargeType extends Model
{
    //

    public function expenseGl(){
        return $this->hasMany(ExpenseGl::class, 'charge_type', 'name');
    }

    public function expenseChargeType() {
        return $this->hasOne(ExpenseChargeType::class, 'charge_type_id', 'id');
    }
}
