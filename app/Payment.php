<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // Associate user to payment
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Associate expense id to paid payment
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
