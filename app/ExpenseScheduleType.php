<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseScheduleType extends Model
{
    public function salesman()
    {
        return $this->belongsTo(User::class,'salesman_id');
    }
}
