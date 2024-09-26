<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseHistory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'expense_id',
        'action',
        'details'
    ];

    /**
     * Get the user that owns the ExpenseHistory
     *
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the expense that owns the ExpenseHistory
     *
     */
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
