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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
