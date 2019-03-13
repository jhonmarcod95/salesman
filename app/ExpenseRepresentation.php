<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseRepresentation extends Model
{
    protected $fillable = [
        'attendees',
        'purpose'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

}
