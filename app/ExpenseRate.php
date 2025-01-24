<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\ExpensesType;

class ExpenseRate extends Model
{
    protected $fillable = [
        'amount',
        'expenses_type_id',
        'user_id',
        'created_by',
        'validity_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expensesType() {
        return $this->belongsTo(ExpensesType::class);
    }

    // Query Scopes
    public function scopeRateAmount ($query, $expenses_type_id)
    {
        $check = $query->where('user_id', Auth::user()->id)
                    ->where('expenses_type_id', $expenses_type_id);

        return $check;
    }

}
