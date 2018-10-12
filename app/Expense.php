<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'amount',
        'attachement',
        'remarks'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function expensesType() {
        return $this->belongsTo(ExpensesType::class);
    }
}
