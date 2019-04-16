<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExpenseBypass extends Model
{
    protected $fillable = [
        'user_id',
        'remarks',
        'created_by',
        'expenses_type_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function expensesType() {
        return $this->belongsTo(ExpensesType::class);
    }

    // Query Scope

    /**
     * Check if user has a expense bypass model
     */
    public function scopeBypass($query, $expenses_type_id) {

        $bypass = $query->where('user_id', Auth::user()->id)
            ->where('expenses_type_id', $expenses_type_id);

        return $bypass;

    }
}
