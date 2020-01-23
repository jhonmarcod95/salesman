<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseExclusive extends Model
{
    protected $fillable = [
        'users_array_id',
        'remarks'
    ];

    public function expenseExclusivable()
    {
        return $this->morphTo();
    }
}
