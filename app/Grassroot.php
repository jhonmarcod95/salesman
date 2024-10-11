<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grassroot extends Model
{
    protected $fillable = [
        'amount',
        'remarks'
    ];

    /**
     * User who created an grassroot expense
     */
    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Related expense id
     */
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * Related grassroots expense type 
     */
    public function grassrootExpenseType()
    {
        return $this->belongsTo(GrassrootsExpenseType::class, 'grassroots_expense_type_id');
    }
}
