<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseMonthlyDmsUnverified extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'expense_monthly_dms_receive_id',
        'expense_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function monthlyDmsReceived() {
        return $this->belongsTo(ExpenseMonthlyDmsReceive::class);
    }
}
