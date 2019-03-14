<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\ExpensesType;

class ExpenseRate extends Model
{
    protected $fillable = [
        'amount'
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

        $default_rate = ExpensesType::find($expenses_type_id)->amount_rate;

        return $check->exists() ? $check->pluck('amount')->first() : $default_rate;
    }

}
