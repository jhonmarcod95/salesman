<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpensesEntry extends Model
{
    public function expenses() {
        return $this->belongsToMany(Expense::class);
    }

    // test function
    public function getExpensesAttribute() {
        return $this->expenses()->pluck('id')->all();
    }
}
